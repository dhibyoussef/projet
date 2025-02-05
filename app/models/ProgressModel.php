<?php
class ProgressModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProgress() {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgressByUserId($userId) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId) {
            throw new Exception('Unauthorized access');
        }
        $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgressById($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProgress($data) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        $stmt = $this->pdo->prepare("INSERT INTO progress (user_id, date, weight, body_fat_percentage) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $_SESSION['user_id'],
            htmlspecialchars($data['date']),
            filter_var($data['weight'], FILTER_VALIDATE_FLOAT),
            filter_var($data['body_fat_percentage'], FILTER_VALIDATE_FLOAT)
        ]);
    }

    public function updateProgress($id, $data) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        $stmt = $this->pdo->prepare("UPDATE progress SET date = ?, weight = ?, body_fat_percentage = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([
            htmlspecialchars($data['date']),
            filter_var($data['weight'], FILTER_VALIDATE_FLOAT),
            filter_var($data['body_fat_percentage'], FILTER_VALIDATE_FLOAT),
            filter_var($id, FILTER_VALIDATE_INT),
            $_SESSION['user_id']
        ]);
    }

    public function deleteProgress($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        $stmt = $this->pdo->prepare("DELETE FROM progress WHERE id = ? AND user_id = ?");
        return $stmt->execute([
            filter_var($id, FILTER_VALIDATE_INT),
            $_SESSION['user_id']
        ]);
    }
}
?>