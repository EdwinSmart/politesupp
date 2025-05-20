<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$products = getProducts();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and prepare order data
        $orderData = [
            'customer' => [
                'name' => $_POST['customer_name'] ?? '',
                'email' => $_POST['customer_email'] ?? '',
                'phone' => $_POST['customer_phone'] ?? '',
                'address' => $_POST['customer_address'] ?? ''
            ],
            'items' => [],
            'total' => 0,
            'deliveryDate' => $_POST['delivery_date'] ?? '',
            'paymentMethod' => $_POST['payment_method'] ?? 'cash',
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // Process items
        foreach ($_POST['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) continue;
            
            $product = getProductById($item['product_id']);
            if (!$product) continue;
            
            $orderData['items'][] = [
                'productId' => $product['id'],
                'name' => $product['name'],
                'quantity' => (int)$item['quantity'],
                'unitPrice' => (float)$product['price']
            ];
            
            $orderData['total'] += $product['price'] * (int)$item['quantity'];
        }
        
        // Add delivery fee
        $orderData['total'] += 4.99; // Fixed delivery fee
        
        // Save the order
        $orderId = saveOrder($orderData);
        $success = "Order #$orderId has been successfully created!";
        
    } catch (Exception $e) {
        $error = "Error creating order: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Order - POLITE MEAT SUPPLIERS</title>
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <!-- Same sidebar as dashboard.php -->
        </aside>
        
        <main class="admin-content">
            <header class="admin-header">
                <h2>Add New Order</h2>
                <div class="admin-user">
                    <span>Welcome, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></span>
                </div>
            </header>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="order-form">
                <div class="form-columns">
                    <div class="form-column">
                        <h3>Customer Information</h3>
                        
                        <div class="form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_email">Email Address *</label>
                            <input type="email" id="customer_email" name="customer_email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_phone">Phone Number *</label>
                            <input type="tel" id="customer_phone" name="customer_phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_address">Delivery Address *</label>
                            <textarea id="customer_address" name="customer_address" rows="4" required></textarea>
                        </div>
                        
                        <h3>Delivery Information</h3>
                        
                        <div class="form-group">
                            <label for="delivery_date">Delivery Date *</label>
                            <input type="date" id="delivery_date" name="delivery_date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_method">Payment Method *</label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="cash">Cash on Delivery</option>
                                <option value="card">Credit/Debit Card</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Order Notes</label>
                            <textarea id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-column">
                        <h3>Order Items</h3>
                        
                        <div id="order-items-container">
                            <!-- Items will be added here via JavaScript -->
                        </div>
                        
                        <button type="button" id="add-item-btn" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                        
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal-display">£0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Delivery:</span>
                                <span>£4.99</span>
                            </div>
                            <div class="summary-row grand-total">
                                <span>Total:</span>
                                <span id="total-display">£4.99</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Create Order</button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
    $(document).ready(function() {
        // Add first item by default
        addOrderItem();
        
        // Add item button click handler
        $('#add-item-btn').click(addOrderItem);
        
        // Calculate totals when anything changes
        $(document).on('change', '.order-item select, .order-item input', calculateTotals);
        
        // Initial calculation
        calculateTotals();
        
        // Set minimum delivery date to tomorrow
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const formattedDate = tomorrow.toISOString().split('T')[0];
        $('#delivery_date').attr('min', formattedDate);
        if (!$('#delivery_date').val()) {
            $('#delivery_date').val(formattedDate);
        }
    });
    
    function addOrderItem() {
        const itemCount = $('.order-item').length;
        const newItemId = 'item_' + itemCount;
        
        const itemHtml = `
            <div class="order-item" id="${newItemId}">
                <div class="form-group">
                    <label>Product</label>
                    <select name="items[${itemCount}][product_id]" class="product-select" required>
                        <option value="">Select a product</option>
                        <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                            <?= htmlspecialchars($product['name']) ?> - <?= htmlspecialchars($product['weight']) ?> (£<?= number_format($product['price'], 2) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="items[${itemCount}][quantity]" min="1" value="1" class="quantity-input" required>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="text" class="price-display" readonly>
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <input type="text" class="item-total-display" readonly>
                </div>
                <button type="button" class="btn btn-danger remove-item-btn" onclick="removeOrderItem('${newItemId}')">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;
        
        $('#order-items-container').append(itemHtml);
        calculateTotals();
    }
    
    function removeOrderItem(itemId) {
        $('#' + itemId).remove();
        calculateTotals();
    }
    
    function calculateTotals() {
        let subtotal = 0;
        
        $('.order-item').each(function() {
            const productSelect = $(this).find('.product-select');
            const quantityInput = $(this).find('.quantity-input');
            const priceDisplay = $(this).find('.price-display');
            const itemTotalDisplay = $(this).find('.item-total-display');
            
            if (productSelect.val()) {
                const price = parseFloat(productSelect.find('option:selected').data('price'));
                const quantity = parseInt(quantityInput.val()) || 0;
                const itemTotal = price * quantity;
                
                priceDisplay.val('£' + price.toFixed(2));
                itemTotalDisplay.val('£' + itemTotal.toFixed(2));
                subtotal += itemTotal;
            } else {
                priceDisplay.val('');
                itemTotalDisplay.val('');
            }
        });
        
        const delivery = 4.99;
        const total = subtotal + delivery;
        
        $('#subtotal-display').text('£' + subtotal.toFixed(2));
        $('#total-display').text('£' + total.toFixed(2));
    }
    </script>
</body>
</html>