<?php
header('Content-Type: application/json');

$adminFile = '../data/admin.json';

// Handle POST request - admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (file_exists($adminFile)) {
        $admins = json_decode(file_get_contents($adminFile), true);
        
        $found = false;
        foreach ($admins['admins'] as $admin) {
            if ($admin['username'] === $input['username'] && password_verify($input['password'], $admin['password'])) {
                $found = true;
                
                // In a real app, you would generate a secure token
                $token = bin2hex(random_bytes(32));
                
                // Also set secure, HttpOnly cookie in production
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'admin' => [
                        'username' => $admin['username'],
                        'name' => $admin['name'],
                        'email' => $admin['email']
                    ]
                ]);
                exit;
            }
        }
        
        if (!$found) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Admin data not available']);
    }
}

// Handle GET request - check admin session
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // In a real app, you would verify the token from headers or cookies
    
    if (isset($_GET['check'])) {
        echo json_encode(['authenticated' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
    }
}
?>