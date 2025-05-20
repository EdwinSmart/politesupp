// Admin functionality
document.addEventListener('DOMContentLoaded', function() {
  // Admin credentials (in production, use proper authentication)
  const ADMIN_CREDENTIALS = {
    username: "admin",
    password: "password123" // In real app, use proper hashed passwords
  };

  // DOM Elements
  const adminModal = document.getElementById('admin-modal');
  const adminDashboard = document.getElementById('admin-dashboard');
  const adminLoginForm = document.getElementById('admin-login');
  const productForm = document.getElementById('product-form');
  const adminProductList = document.getElementById('admin-product-list');
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  
  // Open admin login (add this link somewhere in your admin section)
  document.querySelector('a[href="admin/login.html"]').addEventListener('click', function(e) {
    e.preventDefault();
    adminModal.style.display = 'block';
  });

  // Close modals
  document.querySelectorAll('.close').forEach(closeBtn => {
    closeBtn.addEventListener('click', function() {
      adminModal.style.display = 'none';
      adminDashboard.style.display = 'none';
    });
  });

  // Tab switching
  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      const tabId = this.getAttribute('data-tab');
      
      // Update active tab button
      tabButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      // Show corresponding tab content
      tabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === tabId) {
          content.classList.add('active');
        }
      });
    });
  });

  // Admin login
  adminLoginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('admin-username').value;
    const password = document.getElementById('admin-password').value;
    
    if (username === ADMIN_CREDENTIALS.username && password === ADMIN_CREDENTIALS.password) {
      adminModal.style.display = 'none';
      adminDashboard.style.display = 'block';
      loadProductsForAdmin();
    } else {
      alert('Invalid credentials');
    }
  });

  // Product form submission
  productForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const product = {
      id: Date.now().toString(),
      name: document.getElementById('product-name').value,
      price: parseFloat(document.getElementById('product-price').value),
      category: document.getElementById('product-category').value,
      stock: parseInt(document.getElementById('product-stock').value),
      description: document.getElementById('product-description').value,
      image: document.getElementById('product-image').value || 'images/default-meat.jpg'
    };
    
    saveProduct(product);
    productForm.reset();
    loadProductsForAdmin();
    alert('Product added successfully!');
    
    // Switch to manage products tab
    document.querySelector('.tab-btn[data-tab="manage-products"]').click();
  });

  // Save product to localStorage
  function saveProduct(product) {
    let products = JSON.parse(localStorage.getItem('products')) || [];
    products.push(product);
    localStorage.setItem('products', JSON.stringify(products));
  }

  // Load products for admin management
  function loadProductsForAdmin() {
    const products = JSON.parse(localStorage.getItem('products')) || [];
    adminProductList.innerHTML = '';
    
    if (products.length === 0) {
      adminProductList.innerHTML = '<p>No products found.</p>';
      return;
    }
    
    products.forEach(product => {
      const productItem = document.createElement('div');
      productItem.className = 'admin-product-item';
      productItem.innerHTML = `
        <div class="product-info">
          <img src="${product.image}" alt="${product.name}">
          <div>
            <h4>${product.name}</h4>
            <p>£${product.price.toFixed(2)} | ${product.stock} in stock</p>
          </div>
        </div>
        <div class="admin-product-actions">
          <button class="edit-btn" data-id="${product.id}">Edit</button>
          <button class="delete-btn" data-id="${product.id}">Delete</button>
        </div>
      `;
      adminProductList.appendChild(productItem);
    });
    
    // Add event listeners to new buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-id');
        deleteProduct(productId);
      });
    });
    
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-id');
        editProduct(productId);
      });
    });
  }

  function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
      let products = JSON.parse(localStorage.getItem('products')) || [];
      products = products.filter(p => p.id !== productId);
      localStorage.setItem('products', JSON.stringify(products));
      loadProductsForAdmin();
    }
  }

  function editProduct(productId) {
    const products = JSON.parse(localStorage.getItem('products')) || [];
    const product = products.find(p => p.id === productId);
    
    if (product) {
      // Fill the form with product data
      document.getElementById('product-name').value = product.name;
      document.getElementById('product-price').value = product.price;
      document.getElementById('product-category').value = product.category;
      document.getElementById('product-stock').value = product.stock;
      document.getElementById('product-description').value = product.description;
      document.getElementById('product-image').value = product.image;
      
      // Change form to update mode
      productForm.dataset.editId = productId;
      productForm.querySelector('button').textContent = 'Update Product';
      
      // Switch to add product tab
      document.querySelector('.tab-btn[data-tab="add-product"]').click();
    }
  }

  // Update product form handler
  productForm.addEventListener('submit', function(e) {
    if (this.dataset.editId) {
      e.preventDefault();
      updateProduct(this.dataset.editId);
    }
  });

  function updateProduct(productId) {
    let products = JSON.parse(localStorage.getItem('products')) || [];
    const index = products.findIndex(p => p.id === productId);
    
    if (index !== -1) {
      products[index] = {
        id: productId,
        name: document.getElementById('product-name').value,
        price: parseFloat(document.getElementById('product-price').value),
        category: document.getElementById('product-category').value,
        stock: parseInt(document.getElementById('product-stock').value),
        description: document.getElementById('product-description').value,
        image: document.getElementById('product-image').value || 'images/default-meat.jpg'
      };
      
      localStorage.setItem('products', JSON.stringify(products));
      productForm.reset();
      delete productForm.dataset.editId;
      productForm.querySelector('button').textContent = 'Add Product';
      loadProductsForAdmin();
      alert('Product updated successfully!');
    }
  }
});