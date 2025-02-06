<?php
class NutritionModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            throw new InvalidArgumentException('Invalid CSRF token', 403);
        }
    }

    public function getNutrition() {
        if (!isset($_SESSION['user_id'])) {
            throw new RuntimeException('User not authenticated', 401);
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM nutrition WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($result)) {
                throw new RuntimeException('No nutrition data found', 404);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getNutrition: " . $e->getMessage());
            throw new RuntimeException('Failed to retrieve nutrition data. Please try again later.', 500);
        }
    }

    public function getNutritionById($id) {
        if (!isset($_SESSION['user_id'])) {
            throw new RuntimeException('User not authenticated', 401);
        }
        
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Invalid nutrition ID', 400);
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM nutrition WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new RuntimeException('Nutrition record not found', 404);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getNutritionById: " . $e->getMessage());
            throw new RuntimeException('Failed to retrieve nutrition record. Please try again later.', 500);
        }
    }

    public function addNutrition($data) {
        if (!isset($_SESSION['user_id'])) {
            throw new RuntimeException('User not authenticated', 401);
        }
        
        if (!isset($data['csrf_token'])) {
            throw new InvalidArgumentException('CSRF token missing', 400);
        }
        $this->validateCSRFToken($data['csrf_token']);
        
        $requiredFields = ['date', 'food_item', 'calories', 'protein', 'carbs', 'fats'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Missing required field: $field";
            }
        }
        
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors), 400);
        }
        
        try {
            $stmt = $this->pdo->prepare("INSERT INTO nutrition (user_id, date, food_item, calories, protein, carbs, fats) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([
                $_SESSION['user_id'],
                htmlspecialchars($data['date']),
                htmlspecialchars($data['food_item']),
                filter_var($data['calories'], FILTER_VALIDATE_FLOAT),
                filter_var($data['protein'], FILTER_VALIDATE_FLOAT),
                filter_var($data['carbs'], FILTER_VALIDATE_FLOAT),
                filter_var($data['fats'], FILTER_VALIDATE_FLOAT)
            ]);
            
            if (!$success) {
                throw new RuntimeException('Failed to add nutrition record', 500);
            }
            return ['status' => 'success', 'message' => 'Nutrition record added successfully!'];
        } catch (PDOException $e) {
            error_log("Database error in addNutrition: " . $e->getMessage());
            throw new RuntimeException('Failed to add nutrition record. Please try again later.', 500);
        }
    }

    public function updateNutrition($id, $data) {
        if (!isset($_SESSION['user_id'])) {
            throw new RuntimeException('User not authenticated', 401);
        }
        
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Invalid nutrition ID', 400);
        }
        
        if (!isset($data['csrf_token'])) {
            throw new InvalidArgumentException('CSRF token missing', 400);
        }
        $this->validateCSRFToken($data['csrf_token']);
        
        $requiredFields = ['date', 'food_item', 'calories', 'protein', 'carbs', 'fats'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Missing required field: $field";
            }
        }
        
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors), 400);
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE nutrition SET date = ?, food_item = ?, calories = ?, protein = ?, carbs = ?, fats = ? WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([
                htmlspecialchars($data['date']),
                htmlspecialchars($data['food_item']),
                filter_var($data['calories'], FILTER_VALIDATE_FLOAT),
                filter_var($data['protein'], FILTER_VALIDATE_FLOAT),
                filter_var($data['carbs'], FILTER_VALIDATE_FLOAT),
                filter_var($data['fats'], FILTER_VALIDATE_FLOAT),
                $id,
                $_SESSION['user_id']
            ]);
            
            if (!$success) {
                throw new RuntimeException('Failed to update nutrition record', 500);
            }
            return ['status' => 'success', 'message' => 'Nutrition record updated successfully!'];
        } catch (PDOException $e) {
            error_log("Database error in updateNutrition: " . $e->getMessage());
            throw new RuntimeException('Failed to update nutrition record. Please try again later.', 500);
        }
    }

    public function deleteNutrition($id, $csrfToken) {
        if (!isset($_SESSION['user_id'])) {
            throw new RuntimeException('User not authenticated', 401);
        }
        
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Invalid nutrition ID', 400);
        }
        
        if (!isset($csrfToken)) {
            throw new InvalidArgumentException('CSRF token missing', 400);
        }
        $this->validateCSRFToken($csrfToken);
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM nutrition WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([$id, $_SESSION['user_id']]);
            
            if (!$success) {
                throw new RuntimeException('Failed to delete nutrition record', 500);
            }
            return ['status' => 'success', 'message' => 'Nutrition record deleted successfully!'];
        } catch (PDOException $e) {
            error_log("Database error in deleteNutrition: " . $e->getMessage());
            throw new RuntimeException('Failed to delete nutrition record. Please try again later.', 500);
        }
    }
}
?>