<?php
session_start();
require_once '../includes/auth.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/functions.php';
$orders = getOrders();
$stats = calculateOrderStats($orders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - POLITE MEAT SUPPLIERS</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h1>POLITE MEAT</h1>
                <p>Admin Dashboard</p>
            </div>
            
            <nav class="admin-menu">
                <ul>
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="orders.php">
                            <i class="fas fa-shopping-bag"></i> Orders
                        </a>
                    </li>
                    <li>
                        <a href="add-order.php">
                            <i class="fas fa-plus-circle"></i> Add Order
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="admin-logout">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>
        
        <main class="admin-content">
            <header class="admin-header">
                <h2>Dashboard Overview</h2>
                <div class="admin-user">
                    <span>Welcome, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></span>
                </div>
            </header>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total_orders'] ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-pound-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>£<?= number_format($stats['total_revenue'], 2) ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['pending_orders'] ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['completed_orders'] ?></h3>
                        <p>Completed Orders</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-sections">
                <section class="recent-orders">
                    <h3>Recent Orders</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['orderId']) ?></td>
                                    <td><?= htmlspecialchars($order['customer']['name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($order['orderDate'])) ?></td>
                                    <td>£<?= number_format($order['total'], 2) ?></td>
                                    <td class="status-<?= htmlspecialchars($order['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                    </td>
                                    <td><a href="orders.php?action=view&id=<?= $order['orderId'] ?>" class="btn btn-sm">View</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="orders.php" class="btn view-all">View All Orders</a>
                </section>
            </div>
        </main>
    </div>
</body>
</html>