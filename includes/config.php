<?php

define('DB_HOST', 'localhost');

define('DB_NAME', 'db_herfa');

define('DB_USER', 'root');

define('DB_PASS', '');

define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'HERFA');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');

define('UPLOAD_URL', 'uploads/');

function getPDO(): PDO {

    static $pdo = null;

    if ($pdo === null) {

        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {

            die('<div style="font-family:sans-serif;padding:2rem;color:#c62828;background:#fce4ec;border-radius:10px;margin:2rem auto;max-width:500px">
                 <h3>⚠️ Database Connection Error</h3>
                 <p>Could not connect to the database. Please check your <code>includes/config.php</code> settings.</p>
                 <small style="color:#888">' . htmlspecialchars($e->getMessage()) . '</small>
                 </div>');
        }
    }

    return $pdo;
}
