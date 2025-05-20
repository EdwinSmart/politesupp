// User Authentication Functions
function isLoggedIn() {
    return localStorage.getItem('currentUser') !== null;
}

function getCurrentUser() {
    return JSON.parse(localStorage.getItem('currentUser'));
}

function loginUser(email, password) {
    const users = JSON.parse(localStorage.getItem('users')) || [];
    const user = users.find(u => u.email === email && u.password === password);
    
    if (user) {
        localStorage.setItem('currentUser', JSON.stringify(user));
        return true;
    }
    return false;
}

function registerUser(name, email, password) {
    const users = JSON.parse(localStorage.getItem('users')) || [];
    
    // Check if user already exists
    if (users.some(u => u.email === email)) {
        return { success: false, message: 'Email already registered' };
    }
    
    const newUser = {
        id: Date.now().toString(),
        name,
        email,
        password, // Note: In production, you should NEVER store plain text passwords
        joined: new Date().toISOString()
    };
    
    users.push(newUser);
    localStorage.setItem('users', JSON.stringify(users));
    localStorage.setItem('currentUser', JSON.stringify(newUser));
    
    return { success: true, user: newUser };
}

function logoutUser() {
    localStorage.removeItem('currentUser');
}
// Global variables
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// DOM Elements
const cartCountElement = document.getElementById('cart-count');

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Highlight current page in navigation
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('nav ul li a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});

// Update cart count in header
function updateCartCount() {
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

// Format price
function formatPrice(price) {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(price);
}

// Add to cart function
function addToCart(productId, quantity = 1) {
    // In a real app, we would fetch the product details from the server
    // For this demo, we'll use a simplified approach
    
    // Check if product already in cart
    const existingItem = cart.find(item => item.productId === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        // For demo purposes, we'll just store minimal product info
        cart.push({
            productId: productId,
            quantity: quantity
        });
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count
    updateCartCount();
    
    // Show success message
    alert('Product added to cart!');
}

// Remove from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.productId !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    renderCartItems(); // This would be in cart.js
}

// Update quantity in cart
function updateCartQuantity(productId, newQuantity) {
    const item = cart.find(item => item.productId === productId);
    if (item) {
        item.quantity = parseInt(newQuantity);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        renderCartItems(); // This would be in cart.js
    }
}

// Generate a simple order ID
function generateOrderId() {
    return 'ORD-' + Math.floor(Math.random() * 1000000);
}