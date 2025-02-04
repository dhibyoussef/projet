<?php
session_start();
class BaseController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo; // Initializes the database connection using a PDO instance

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../../index.php');
            exit();
        }
    }
}
?>