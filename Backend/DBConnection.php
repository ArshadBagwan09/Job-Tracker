<?php
namespace Backend;

use \PDO;
use \PDOException;

class DBConnection {

    private static $host = "localhost";
    private static $dbname = "jobapptracker";
    private static $username = "root";
    private static $password = ""; // apna MySQL password

    public static function getConnection() {
        try {
            $conn = new PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                self::$username,
                self::$password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            return null;
        }
    }
}