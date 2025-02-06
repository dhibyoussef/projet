<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno] $errstr in $errfile on line $errline");
    $_SESSION['error_message'] = $errstr;
    $_SESSION['error_animation'] = 'windowShake';
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Custom exception handler
set_exception_handler(function($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage());
    $_SESSION['error_message'] = $exception->getMessage();
    $_SESSION['error_animation'] = 'windowShake';
    http_response_code(500);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
});

// Shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal Error: {$error['message']}");
        $_SESSION['error_message'] = $error['message'];
        $_SESSION['error_animation'] = 'windowShake';
        http_response_code(500);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
});

class WorkoutModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function validateCSRFToken($token) {
        if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
            $_SESSION['error_message'] = 'CSRF token validation failed';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('CSRF token validation failed');
        }
    }

    public function getWorkoutsByUserId($userId) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'User not authenticated';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM workouts WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getWorkoutsByUserId: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to retrieve workouts';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('Failed to retrieve workouts');
        }
    }

    public function createWorkout($data, $csrfToken) {
        $this->validateCSRFToken($csrfToken);
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'User not authenticated';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("INSERT INTO workouts (user_id, workout_name, description, duration) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([
                $_SESSION['user_id'],
                htmlspecialchars($data['workout_name']),
                htmlspecialchars($data['description']),
                filter_var($data['duration'], FILTER_VALIDATE_INT)
            ]);
            if ($result) {
                $_SESSION['success_message'] = 'Workout created successfully!';
                $_SESSION['success_animation'] = 'windowSlideIn';
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in createWorkout: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to create workout';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('Failed to create workout');
        }
    }

    public function updateWorkout($id, $data, $csrfToken) {
        $this->validateCSRFToken($csrfToken);
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'User not authenticated';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("UPDATE workouts SET workout_name = ?, description = ?, duration = ? WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([
                htmlspecialchars($data['workout_name']),
                htmlspecialchars($data['description']),
                filter_var($data['duration'], FILTER_VALIDATE_INT),
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
            if ($result) {
                $_SESSION['success_message'] = 'Workout updated successfully!';
                $_SESSION['success_animation'] = 'windowSlideIn';
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in updateWorkout: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to update workout';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('Failed to update workout');
        }
    }

    public function deleteWorkout($id, $csrfToken) {
        $this->validateCSRFToken($csrfToken);
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'User not authenticated';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('User not authenticated');
        }
        try {
            $stmt = $this->pdo->prepare("DELETE FROM workouts WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([
                filter_var($id, FILTER_VALIDATE_INT),
                $_SESSION['user_id']
            ]);
            if ($result) {
                $_SESSION['success_message'] = 'Workout deleted successfully!';
                $_SESSION['success_animation'] = 'windowSlideIn';
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in deleteWorkout: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to delete workout';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('Failed to delete workout');
        }
    }

    public function getWorkoutById($id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'User not authenticated';
            $_SESSION['error_animation'] = 'windowShake';
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
            $_SESSION['error_message'] = 'Failed to retrieve workout';
            $_SESSION['error_animation'] = 'windowShake';
            throw new Exception('Failed to retrieve workout');
        }
    }
}
?>