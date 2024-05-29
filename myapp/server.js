const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const app = express();
const port = 3000;

// Middleware
app.use(bodyParser.json());

// Create a MySQL connection pool
const pool = mysql.createPool({
    host:        'localhost',
    user:        'root',
    password:    '',
    database:    'db_iot'
});

// Utility function to execute SQL queries
const executeQuery = (sql, params) => {
    return new Promise((resolve, reject) => {
        pool.query(sql, params, (error, results) => {
            if (error) {
                return reject(error);
            }
            resolve(results);
        });
    });
};

// Routes
app.get('/', (req, res) => {
    res.send('Hello World!');
});

// Get all users
app.get('/users', async (req, res) => {
    try {
        const sql = `SELECT u.id, u.username, u.email, u.role, u.access, ud.deviceName, ud.device_requirements1, ud.device_requirements2, ud.device_requirements3
                     FROM users u
                     LEFT JOIN user_devices ud ON u.id = ud.user_id
                     WHERE u.role = 1`;
        const results = await executeQuery(sql);
        res.json({ status: 'success', data: results });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get user by ID
app.get('/users/:id', async (req, res) => {
    try {
        const userId = req.params.id;
        const sql = `SELECT u.id, u.username, u.email, u.role, u.access, ud.deviceName, ud.device_requirements1, ud.device_requirements2, ud.device_requirements3
                     FROM users u
                     LEFT JOIN user_devices ud ON u.id = ud.user_id
                     WHERE u.id = ?`;
        const results = await executeQuery(sql, [userId]);
        if (results.length > 0) {
            res.json({ status: 'success', data: results[0] });
        } else {
            res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Add new user
app.post('/users', async (req, res) => {
    const { username, email, password, deviceName, device_requirements1, device_requirements2, device_requirements3 } = req.body;

    if (!username || !email || !password || !deviceName || !device_requirements1 || !device_requirements2 || !device_requirements3) {
        return res.status(400).json({ status: 'error', message: 'Data belum terisi' });
    }

    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        const userSql = `INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 1)`;
        const userResult = await executeQuery(userSql, [username, email, password]);

        const userId = userResult.insertId;
        const deviceSql = `INSERT INTO user_devices (user_id, deviceName, device_requirements1, device_requirements2, device_requirements3)
                           VALUES (?, ?, ?, ?, ?)`;
        await executeQuery(deviceSql, [userId, deviceName, device_requirements1, device_requirements2, device_requirements3]);

        await connection.commit();
        res.status(201).json({ status: 'success', message: 'Data berhasil di tambahkan' });
    } catch (error) {
        await connection.rollback();
        res.status(500).json({ status: 'error', message: error.message });
    } finally {
        connection.release();
    }
});

// Delete user by ID
app.delete('/users/:id', async (req, res) => {
    const userId = req.params.id;
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        const userSql = `DELETE FROM users WHERE id = ?`;
        const userResult = await executeQuery(userSql, [userId]);

        if (userResult.affectedRows > 0) {
            const deviceSql = `DELETE FROM user_devices WHERE user_id = ?`;
            await executeQuery(deviceSql, [userId]);
            await connection.commit();
            res.json({ status: 'success', message: 'Data berhasil di hapus' });
        } else {
            res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
    } catch (error) {
        await connection.rollback();
        res.status(500).json({ status: 'error', message: error.message });
    } finally {
        connection.release();
    }
});

// User login
app.post('/login', async (req, res) => {
    const { email, password } = req.body;

    if (!email || !password) {
        return res.status(400).json({ status: 'error', message: 'Username dan password harus diisi' });
    }

    try {
        const sql = `SELECT u.*, ud.device_requirements1, ud.device_requirements2, ud.device_requirements3
                     FROM users u
                     LEFT JOIN user_devices ud ON u.id = ud.user_id
                     WHERE u.email = ? AND u.password = ?`;
        const results = await executeQuery(sql, [email, password]);
        if (results.length > 0) {
            res.json({ status: 'success', message: 'Login berhasil', user: results[0] });
        } else {
            res.status(401).json({ status: 'error', message: 'Username atau password salah' });
        }
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get user device requirements
app.get('/user_device/:user_id', async (req, res) => {
    const userId = req.params.user_id;

    try {
        const sql = `SELECT device_requirements1, device_requirements2, device_requirements3
                     FROM user_devices
                     WHERE user_id = ?`;
        const results = await executeQuery(sql, [userId]);

        if (results.length > 0) {
            const combinedData = results.map(row => {
                let combinedRow = {};
                if (row.device_requirements1) combinedRow["device_requirements1"] = { [row.device_requirements1]: '25°C' }; // temperature
                if (row.device_requirements2) combinedRow["device_requirements2"] = { [row.device_requirements2]: '60%' }; // humidity
                if (row.device_requirements3) combinedRow["device_requirements3"] = { [row.device_requirements3]: '6.5' }; // pH level
                return combinedRow;
            });

            res.json({ status: 'success', data: combinedData });
        } else {
            res.status(404).json({ status: 'error', message: 'User not found' });
        }
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// User registration
// User registration
app.post('/register', async (req, res) => {
    const { username, email, password, deviceName, device_requirements1, device_requirements2, device_requirements3 } = req.body;

    if (!username || !email || !password || !deviceName || !device_requirements1 || !device_requirements2 || !device_requirements3) {
        return res.status(400).json({ status: 'error', message: 'Semua kolom harus diisi' });
    }

    try {
        const userSql = `INSERT INTO users (username, email, password, role, access) VALUES (?, ?, ?, 1, 0)`;
        const userResult = await executeQuery(userSql, [username, email, password]);
        const userId = userResult.insertId;

        const deviceSql = `INSERT INTO user_devices (user_id, deviceName, device_requirements1, device_requirements2, device_requirements3)
                           VALUES (?, ?, ?, ?, ?)`;
        await executeQuery(deviceSql, [userId, deviceName, device_requirements1, device_requirements2, device_requirements3]);

        res.status(201).json({ status: 'success', message: 'Pendaftaran berhasil' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});




app.listen(port, () => {
    console.log(`App listening at http://localhost:${port}`);
});
