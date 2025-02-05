<?php
class NutritionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getNutrition() {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM nutrition WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getNutrition: " . $e->getMessage());
            throw new Exception('Failed to retrieve nutrition data');
        }
    }

    public function getNutritionById($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM nutrition WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception('Nutrition record not found');
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getNutritionById: " . $e->getMessage());
            throw new Exception('Failed to retrieve nutrition record');
        }
    }

    public function addNutrition($data) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        
        // Validate required fields
        $requiredFields = ['date', 'food_item', 'calories', 'protein', 'carbs', 'fats'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        try {
            $stmt = $this->pdo->prepare("INSERT INTO nutrition (user_id, date, food_item, calories, protein, carbs, fats) VALUES (?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $_SESSION['user_id'],
                htmlspecialchars($data['date']),
                htmlspecialchars($data['food_item']),
                filter_var($data['calories'], FILTER_VALIDATE_FLOAT),
                filter_var($data['protein'], FILTER_VALIDATE_FLOAT),
                filter_var($data['carbs'], FILTER_VALIDATE_FLOAT),
                filter_var($data['fats'], FILTER_VALIDATE_FLOAT)
            ]);
        } catch (PDOException $e) {
            error_log("Database error in addNutrition: " . $e->getMessage());
            throw new Exception('Failed to add nutrition record');
        }
    }

    public function updateNutrition($id, $data) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        
        // Validate required fields
        $requiredFields = ['date', 'food_item', 'calories', 'protein', 'carbs', 'fats'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE nutrition SET date = ?, food_item = ?, calories = ?, protein = ?, carbs = ?, fats = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                htmlspecialchars($data['date']),
                htmlspecialchars($data['food_item']),
                filter_var($data['calories'], FILTER_VALIDATE_FLOAT),
                filter_var($data['protein'], FILTER_VALIDATE_FLOAT),
                filter_var($data['carbs'], FILTER_VALIDATE_FLOAT),
                filter_var($data['fats'], FILTER_VALIDATE_FLOAT),
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Database error in updateNutrition: " . $e->getMessage());
            throw new Exception('Failed to update nutrition record');
        }
    }

    public function deleteNutrition($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated');
        }
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM nutrition WHERE id = ? AND user_id = ?");
            return $stmt->execute([
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Database error in deleteNutrition: " . $e->getMessage());
            throw new Exception('Failed to delete nutrition record');
        }
    }
}
?>