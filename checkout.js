// DOM Elements
const checkoutForm = document.getElementById('checkout-form');
const checkoutItemsList = document.getElementById('checkout-items');
const checkoutSubtotalElement = document.getElementById('checkout-subtotal');
const checkoutTotalElement = document.getElementById('checkout-total');
const checkoutDeliveryElement = document.getElementById('checkout-delivery');

// Sample products data (same as in cart.js)
const products = [
    {
        id: 1,
        name: "Premium Halal Beef - Ribeye Steak",
        description: "Tender, well-marbled ribeye steak, hand-cut",
        price: 24.99,
        weight: "500g",
        image: "images/ribeye.jpg"
    },
    {
        id: 2,
        name: "Halal Chicken Breast",
        description: "Boneless, skinless chicken breast",
        price: 12.99,
        weight: "1kg",
        image: "images/chicken-breast.jpg"
    },
    {
        id: 3,
        name: "Halal Lamb Chops",
        description: "Fresh lamb chops, perfect for grilling",
        price: 18.99,
        weight: "500g",
        image: "images/lamb-chops.jpg"
    }
];

// Display order summary in checkout
function renderCheckoutItems() {
    if (cart.length === 0) {
        window.location.href = 'products.html';
        return;
    }
    
    // Calculate totals
    const subtotal = cart.reduce((total, item) => {
        const product = products.find(p => p.id === item.productId);
        return total + (product ? product.price * item.quantity : 0);
    }, 0);
    
    const delivery = 4.99;
    const total = subtotal + delivery;
    
    // Update totals
    checkoutSubtotalElement.textContent = formatPrice(subtotal);
    checkoutDeliveryElement.textContent = formatPrice(delivery);
    checkoutTotalElement.textContent = formatPrice(total);
    
    // Render checkout items
    checkoutItemsList.innerHTML = cart.map(item => {
        const product = products.find(p => p.id === item.productId);
        if (!product) return '';
        
        return `
            <div class="order-item">
                <div class="order-item-info">
                    <img src="${product.image}" alt="${product.name}" class="order-item-img">
                    <div class="order-item-details">
                        <h4>${product.name}</h4>
                        <p>${product.weight}</p>
                    </div>
                </div>
                <div class="order-item-price">
                    ${formatPrice(product.price * item.quantity)}
                </div>
            </div>
        `;
    }).join('');
}

// Handle form submission
function handleCheckoutSubmit(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(checkoutForm);
    const customer = {
        name: formData.get('full-name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        address: formData.get('address')
    };
    
    // Create order
    const order = {
        orderId: generateOrderId(),
        customer: customer,
        items: cart.map(item => {
            const product = products.find(p => p.id === item.productId);
            return {
                productId: item.productId,
                name: product ? product.name : '',
                quantity: item.quantity,
                unitPrice: product ? product.price : 0
            };
        }),
        deliveryDate: formData.get('delivery-date'),
        deliveryTime: formData.get('delivery-time'),
        notes: formData.get('notes'),
        paymentMethod: formData.get('payment'),
        status: 'pending',
        orderDate: new Date().toISOString()
    };
    
    // In a real app, we would send this to the server
    console.log('Order submitted:', order);
    
    // Clear cart
    cart = [];
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    
    // Redirect to confirmation page with order ID
    window.location.href = `order-confirmation.html?orderId=${order.orderId}`;
}

// Initialize checkout page
document.addEventListener('DOMContentLoaded', function() {
    renderCheckoutItems();
    
    // Set minimum delivery date to tomorrow
    const deliveryDateInput = document.getElementById('delivery-date');
    if (deliveryDateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const formattedDate = tomorrow.toISOString().split('T')[0];
        deliveryDateInput.setAttribute('min', formattedDate);
        
        // If no value set, default to tomorrow
        if (!deliveryDateInput.value) {
            deliveryDateInput.value = formattedDate;
        }
    }
    
    // Add form submit event
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', handleCheckoutSubmit);
    }
});