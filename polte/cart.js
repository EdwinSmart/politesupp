// DOM Elements
const cartEmptyElement = document.getElementById('cart-empty');
const cartContentElement = document.getElementById('cart-content');
const cartItemsList = document.getElementById('cart-items-list');
const cartSubtotalElement = document.getElementById('cart-subtotal');
const cartTotalElement = document.getElementById('cart-total');
const cartDeliveryElement = document.getElementById('cart-delivery');

// Sample products data (in a real app, this would come from an API)
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

// Display cart items
function renderCartItems() {
    if (cart.length === 0) {
        cartEmptyElement.style.display = 'block';
        cartContentElement.style.display = 'none';
        return;
    }
    
    cartEmptyElement.style.display = 'none';
    cartContentElement.style.display = 'block';
    
    // Calculate totals
    const subtotal = cart.reduce((total, item) => {
        const product = products.find(p => p.id === item.productId);
        return total + (product ? product.price * item.quantity : 0);
    }, 0);
    
    const delivery = 4.99;
    const total = subtotal + delivery;
    
    // Update totals
    cartSubtotalElement.textContent = formatPrice(subtotal);
    cartDeliveryElement.textContent = formatPrice(delivery);
    cartTotalElement.textContent = formatPrice(total);
    
    // Render cart items
    cartItemsList.innerHTML = cart.map(item => {
        const product = products.find(p => p.id === item.productId);
        if (!product) return '';
        
        return `
            <tr>
                <td>
                    <div class="cart-item-info">
                        <img src="${product.image}" alt="${product.name}" class="cart-item-img">
                        <div>
                            <h4 class="cart-item-name">${product.name}</h4>
                            <p>${product.weight}</p>
                        </div>
                    </div>
                </td>
                <td>${formatPrice(product.price)}</td>
                <td>
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="updateCartQuantity(${product.id}, ${item.quantity - 1})">-</button>
                        <input type="number" class="quantity-input" value="${item.quantity}" min="1" onchange="updateCartQuantity(${product.id}, this.value)">
                        <button class="quantity-btn" onclick="updateCartQuantity(${product.id}, ${item.quantity + 1})">+</button>
                    </div>
                </td>
                <td>${formatPrice(product.price * item.quantity)}</td>
                <td><i class="fas fa-trash cart-item-remove" onclick="removeFromCart(${product.id})"></i></td>
            </tr>
        `;
    }).join('');
}

// Initialize cart page
document.addEventListener('DOMContentLoaded', function() {
    renderCartItems();
});