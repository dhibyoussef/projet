<?php
class NutritionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getNutrition() {
        $stmt = $this->pdo->prepare("SELECT * FROM nutrition");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNutritionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM nutrition WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addNutrition($data) {
        $stmt = $this->pdo->prepare("INSERT INTO nutrition (user_id, date, food_item, calories, protein, carbs, fats) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'], 
            $data['date'], 
            $data['food_item'], 
            $data['calories'], 
            $data['protein'], 
            $data['carbs'], 
            $data['fats']
        ]);
    }

    public function updateNutrition($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE nutrition SET date = ?, food_item = ?, calories = ?, protein = ?, carbs = ?, fats = ? WHERE id = ?");
        return $stmt->execute([
            $data['date'], 
            $data['food_item'], 
            $data['calories'], 
            $data['protein'], 
            $data['carbs'], 
            $data['fats'], 
            $id
        ]);
    }

    public function deleteNutrition($id) {
        $stmt = $this->pdo->prepare("DELETE FROM nutrition WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>