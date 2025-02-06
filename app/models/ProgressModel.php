<?php
class ProgressModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function handleError($message, $code) {
        $_SESSION['error_message'] = $message;
        $_SESSION['error_code'] = $code;
        $_SESSION['error_animation'] = 'animate__animated animate__slideInDown';
        return false;
    }

    public function getAllProgress() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->handleError('User not authenticated', 401);
            }
            $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$result) {
                return $this->handleError('No progress records found', 404);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getAllProgress: " . $e->getMessage());
            return $this->handleError('Failed to retrieve progress data', 500);
        }
    }

    public function getProgressByUserId($userId) {
        try {
            if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId) {
                return $this->handleError('Unauthorized access', 403);
            }
            $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$result) {
                return $this->handleError('No progress records found for user', 404);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getProgressByUserId: " . $e->getMessage());
            return $this->handleError('Failed to retrieve user progress data', 500);
        }
    }

    public function getProgressById($id) {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->handleError('User not authenticated', 401);
            }
            $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return $this->handleError('Progress record not found', 404);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getProgressById: " . $e->getMessage());
            return $this->handleError('Failed to retrieve progress record', 500);
        }
    }

    public function createProgress($data) {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->handleError('User not authenticated', 401);
            }
            if (empty($data['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
                return $this->handleError('CSRF token validation failed', 403);
            }
            
            $stmt = $this->pdo->prepare("INSERT INTO progress (user_id, date, weight, body_fat_percentage) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([
                $_SESSION['user_id'],
                htmlspecialchars($data['date']),
                filter_var($data['weight'], FILTER_VALIDATE_FLOAT),
                filter_var($data['body_fat_percentage'], FILTER_VALIDATE_FLOAT)
            ]);
            
            if (!$success) {
                return $this->handleError('Failed to create progress record', 500);
            }
            return $success;
        } catch (PDOException $e) {
            error_log("Database error in createProgress: " . $e->getMessage());
            return $this->handleError('Failed to create progress record', 500);
        }
    }

    public function updateProgress($id, $data) {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->handleError('User not authenticated', 401);
            }
            if (empty($data['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
                return $this->handleError('CSRF token validation failed', 403);
            }
            
            $stmt = $this->pdo->prepare("UPDATE progress SET date = ?, weight = ?, body_fat_percentage = ? WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([
                htmlspecialchars($data['date']),
                filter_var($data['weight'], FILTER_VALIDATE_FLOAT),
                filter_var($data['body_fat_percentage'], FILTER_VALIDATE_FLOAT),
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
            
            if (!$success) {
                return $this->handleError('Failed to update progress record', 500);
            }
            return $success;
        } catch (PDOException $e) {
            error_log("Database error in updateProgress: " . $e->getMessage());
            return $this->handleError('Failed to update progress record', 500);
        }
    }

    public function deleteProgress($id, $csrfToken) {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->handleError('User not authenticated', 401);
            }
            if (empty($csrfToken) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
                return $this->handleError('CSRF token validation failed', 403);
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM progress WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
            
            if (!$success) {
                return $this->handleError('Failed to delete progress record', 500);
            }
            return $success;
        } catch (PDOException $e) {
            error_log("Database error in deleteProgress: " . $e->getMessage());
            return $this->handleError('Failed to delete progress record', 500);
        }
    }
}
?>