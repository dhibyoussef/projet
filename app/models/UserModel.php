<?php
class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getUserByEmail: " . $e->getMessage());
            return null;
        }
    }

    public function createUser($name, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $hashedPassword]);
        } catch (PDOException $e) {
            error_log("Database error in createUser: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            throw new Exception('Unauthorized access');
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getUserById: " . $e->getMessage());
            return null;
        }
    }

    public function updateUser($id, $name, $email, $password) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            throw new Exception('Unauthorized access');
        }
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            return $stmt->execute([$name, $email, $hashedPassword, $id]);
        } catch (PDOException $e) {
            error_log("Database error in updateUser: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            throw new Exception('Unauthorized access');
        }
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Database error in deleteUser: " . $e->getMessage());
            return false;
        }
    }

    public function authenticate($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID upon successful authentication
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['authenticated'] = true;
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            error_log("Database error in authenticate: " . $e->getMessage());
            return null;
        }
    }

    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
        } catch (PDOException $e) {
            error_log("Database error in emailExists: " . $e->getMessage());
            return false;
        }
    }
}
?>