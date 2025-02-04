<?php
class ProgressModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProgress() {
        $stmt = $this->pdo->query("SELECT * FROM progress");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgressByUserId($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgressById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProgress($data) {
        $stmt = $this->pdo->prepare("INSERT INTO progress (user_id, date, weight, body_fat_percentage) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'], 
            $data['date'], 
            $data['weight'], 
            $data['body_fat_percentage']
        ]);
    }

    public function updateProgress($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE progress SET date = ?, weight = ?, body_fat_percentage = ? WHERE id = ?");
        return $stmt->execute([
            $data['date'], 
            $data['weight'], 
            $data['body_fat_percentage'], 
            $id
        ]);
    }

    public function deleteProgress($id) {
        $stmt = $this->pdo->prepare("DELETE FROM progress WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>