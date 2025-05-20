<?php
header('Content-Type: application/json');

// In a real app, this would connect to a database
// For this demo, we'll use the JSON file approach

$productsFile = '../data/products.json';

// Handle GET request - get all products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($productsFile)) {
        $products = json_decode(file_get_contents($productsFile), true);
        echo json_encode($products);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Products not found']);
    }
}

// Handle POST request - add new product (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app, you would verify admin authentication here
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (file_exists($productsFile)) {
        $products = json_decode(file_get_contents($productsFile), true);
        
        // Generate new product ID
        $newId = max(array_column($products['products'], 'id')) + 1;
        
        $newProduct = [
            'id' => $newId,
            'name' => $input['name'],
            'description' => $input['description'],
            'price' => (float)$input['price'],
            'weight' => $input['weight'],
            'category' => $input['category'],
            'image' => $input['image'],
            'available' => true
        ];
        
        $products['products'][] = $newProduct;
        
        file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
        
        echo json_encode($newProduct);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to save product']);
    }
}

// Handle PUT request - update product (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // In a real app, you would verify admin authentication here
    
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = (int)$_GET['id'];
    
    if (file_exists($productsFile)) {
        $products = json_decode(file_get_contents($productsFile), true);
        
        $found = false;
        foreach ($products['products'] as &$product) {
            if ($product['id'] === $productId) {
                $product['name'] = $input['name'] ?? $product['name'];
                $product['description'] = $input['description'] ?? $product['description'];
                $product['price'] = $input['price'] ?? $product['price'];
                $product['weight'] = $input['weight'] ?? $product['weight'];
                $product['category'] = $input['category'] ?? $product['category'];
                $product['image'] = $input['image'] ?? $product['image'];
                $product['available'] = $input['available'] ?? $product['available'];
                
                $found = true;
                break;
            }
        }
        
        if ($found) {
            file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to update product']);
    }
}

// Handle DELETE request - delete product (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // In a real app, you would verify admin authentication here
    
    $productId = (int)$_GET['id'];
    
    if (file_exists($productsFile)) {
        $products = json_decode(file_get_contents($productsFile), true);
        
        $found = false;
        foreach ($products['products'] as $key => $product) {
            if ($product['id'] === $productId) {
                unset($products['products'][$key]);
                $found = true;
                break;
            }
        }
        
        if ($found) {
            $products['products'] = array_values($products['products']); // Reindex array
            file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to delete product']);
    }
}
?>