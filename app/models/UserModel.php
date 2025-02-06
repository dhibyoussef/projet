<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');  // Changed to display errors on the same page
ini_set('log_errors', '1');

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function handleError($message, $animation = 'window-shake') {  // Changed default animation to window-shake
        $_SESSION['error_message'] = $message;
        $_SESSION['error_animation'] = $animation;
        return false;
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-fade');
        }
    }

    public function createUser($name, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $result = $stmt->execute([$name, $email, $hashedPassword]);
            
            if (!$result) {
                return $this->handleError('Failed to create user. Please try again.', 'window-bounce');
            }
            return $result;
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-shake');
        }
    }

    public function getUserById($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            return $this->handleError('Unauthorized access', 'window-shake');
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-fade');
        }
    }

    public function updateUser($id, $name, $email, $password) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            return $this->handleError('Unauthorized access', 'window-shake');
        }
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $result = $stmt->execute([$name, $email, $hashedPassword, $id]);
            
            if (!$result) {
                return $this->handleError('Failed to update user. Please try again.', 'window-bounce');
            }
            return $result;
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-fade');
        }
    }

    public function deleteUser($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            return $this->handleError('Unauthorized access', 'window-shake');
        }
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                return $this->handleError('Failed to delete user. Please try again.', 'window-bounce');
            }
            return $result;
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-fade');
        }
    }

    public function authenticate($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['authenticated'] = true;
                
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                
                return $user;
            }
            return $this->handleError('Invalid email or password', 'window-shake');
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-fade');
        }
    }

    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
        } catch (PDOException $e) {
            return $this->handleError('Database error. Please try again.', 'window-fade');
        }
    }

    public function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return $this->handleError('CSRF token validation failed', 'window-shake');
        }
        return true;
    }
}
?>