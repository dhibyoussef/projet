<?php
class WorkoutModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getWorkoutsByUserId($userId) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM workouts WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getWorkoutsByUserId: " . $e->getMessage());
            throw new Exception('Failed to retrieve workouts');
        }
    }

    public function createWorkout($data) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("INSERT INTO workouts (user_id, workout_name, description, duration) VALUES (?, ?, ?, ?)");
            return $stmt->execute([
                $_SESSION['user_id'],
                htmlspecialchars($data['workout_name']),
                htmlspecialchars($data['description']),
                filter_var($data['duration'], FILTER_VALIDATE_INT)
            ]);
        } catch (PDOException $e) {
            error_log("Database error in createWorkout: " . $e->getMessage());
            throw new Exception('Failed to create workout');
        }
    }

    public function updateWorkout($id, $data) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("UPDATE workouts SET workout_name = ?, description = ?, duration = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                htmlspecialchars($data['workout_name']),
                htmlspecialchars($data['description']),
                filter_var($data['duration'], FILTER_VALIDATE_INT),
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Database error in updateWorkout: " . $e->getMessage());
            throw new Exception('Failed to update workout');
        }
    }

    public function deleteWorkout($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("DELETE FROM workouts WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Database error in deleteWorkout: " . $e->getMessage());
            throw new Exception('Failed to delete workout');
        }
    }

    public function getWorkoutById($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM workouts WHERE id = ? AND user_id = ?");
            $stmt->execute([
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getWorkoutById: " . $e->getMessage());
            throw new Exception('Failed to retrieve workout');
        }
    }
}
?>