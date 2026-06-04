<?php
/**
 * Movify - Database Connection (PDO)
 * Singleton pattern ile veritabanı bağlantısı
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    private $host = 'localhost';
    private $dbname = 'movify';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevent cloning
    private function __clone() {}
}

/**
 * Global helper: get PDO instance
 */
function db() {
    return Database::getInstance()->getConnection();
}

/**
 * Session başlat (her sayfada kullanılacak)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Base URL constant
 */
define('BASE_URL', '/movify');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

/**
 * Helper: Redirect
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Helper: Sanitize output (XSS koruması)
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Helper: Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Helper: Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Helper: Get current profile ID
 */
function currentProfileId() {
    return $_SESSION['profile_id'] ?? null;
}

/**
 * Helper: Get current user ID
 */
function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Helper: Flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Helper: YouTube video ID çıkartma
 */
function extractYoutubeId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
    preg_match($pattern, $url, $matches);
    return $matches[1] ?? null;
}
