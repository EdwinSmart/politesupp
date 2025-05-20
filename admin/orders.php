<?php
header('Content-Type: application/json');

$ordersFile = '../data/orders.json';

// Handle GET request - get orders
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // In a real app, you might filter by status or other parameters
    
    if (file_exists($ordersFile)) {
        $orders = json_decode(file_get_contents($ordersFile), true);
        
        // Filter by status if provided
        if (isset($_GET['status'])) {
            $filtered = array_filter($orders['orders'], function($order) {
                return $order['status'] === $_GET['status'];
            });
            echo json_encode(['orders' => array_values($filtered)]);
        } else {
            echo json_encode($orders);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Orders not found']);
    }
}

// Handle POST request - create new order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (file_exists($ordersFile)) {
        $orders = json_decode(file_get_contents($ordersFile), true);
        
        // Generate order ID
        $orderId = 'ORD-' . (count($orders['orders']) + 1001);
        
        $newOrder = [
            'orderId' => $orderId,
            'customer' => $input['customer'],
            'items' => $input['items'],
            'total' => $input['total'],
            'orderDate' => date('c'),
            'status' => 'pending',
            'deliveryDate' => $input['deliveryDate'],
            'paymentMethod' => $input['paymentMethod'],
            'notes' => $input['notes'] ?? ''
        ];
        
        $orders['orders'][] = $newOrder;
        
        file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
        
        echo json_encode(['orderId' => $orderId]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to save order']);
    }
}

// Handle PUT request - update order status (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // In a real app, you would verify admin authentication here
    
    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = $_GET['id'];
    
    if (file_exists($ordersFile)) {
        $orders = json_decode(file_get_contents($ordersFile), true);
        
        $found = false;
        foreach ($orders['orders'] as &$order) {
            if ($order['orderId'] === $orderId) {
                $order['status'] = $input['status'];
                $found = true;
                break;
            }
        }
        
        if ($found) {
            file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to update order']);
    }
}

// Handle GET request for single order
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    if (file_exists($ordersFile)) {
        $orders = json_decode(file_get_contents($ordersFile), true);
        
        foreach ($orders['orders'] as $order) {
            if ($order['orderId'] === $orderId) {
                echo json_encode($order);
                exit;
            }
        }
        
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Orders not found']);
    }
}
?>