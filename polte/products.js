document.addEventListener('DOMContentLoaded', function() {
    // Load products from localStorage instead of hardcoded
    const products = JSON.parse(localStorage.getItem('products')) || [
        // Default products if none exist
        {
            id: '1',
            name: 'Premium Beef Steak',
            price: 12.99,
            category: 'beef',
            stock: 20,
            description: 'Juicy and tender beef steak, perfect for grilling',
            image: 'images/beef-steak.jpg'
        },
        {
            id: '2',
            name: 'Organic Chicken Breast',
            price: 8.99,
            category: 'chicken',
            stock: 30,
            description: 'Fresh organic chicken breast, great for healthy meals',
            image: 'images/chicken-breast.jpg'
        },
        {
            id: '3',
            name: 'Lamb Chops',
            price: 14.99,
            category: 'lamb',
            stock: 15,
            description: 'Tender lamb chops, ideal for special occasions',
            image: 'images/lamb-chops.jpg'
        }
    ];

    // Save default products if none exist
    if (localStorage.getItem('products') === null) {
        localStorage.setItem('products', JSON.stringify(products));
    }

    // Display products
    displayProducts(products.slice(0, 3), 'featured-products');
    
    // Add to cart functionality would remain the same
    // ...
});

function displayProducts(products, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    products.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'product-card';
        productElement.innerHTML = `
            <img src="${product.image}" alt="${product.name}">
            <h3>${product.name}</h3>
            <p class="product-description">${product.description}</p>
            <p class="product-price">£${product.price.toFixed(2)}</p>
            <button class="add-to-cart" data-id="${product.id}">Add to Cart</button>
        `;
        container.appendChild(productElement);
    });
    
    // Add event listeners to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId);
        });
    });
}