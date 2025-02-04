<?php
class WorkoutModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllWorkouts() {
        $stmt = $this->pdo->prepare("SELECT * FROM workouts");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createWorkout($data) {
        $stmt = $this->pdo->prepare("INSERT INTO workouts (id,name, description, duration) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['id'], $data['workout_name'], $data['description'], $data['duration']]);
    }

    public function updateWorkout($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE workouts SET workout_name = ?, description = ?, duration = ? WHERE id = ?");
        return $stmt->execute([$data['workout_name'], $data['description'], $data['duration'], $id]);
    }

    public function deleteWorkout($id) {
        $stmt = $this->pdo->prepare("DELETE FROM workouts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getWorkoutById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM workouts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>