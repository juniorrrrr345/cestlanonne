// Configuration
const API_BASE_URL = './api';
const ADMIN_CREDENTIALS = {
    username: 'admin',
    password: 'admin123'
};

// État global de l'application
let currentUser = null;
let products = [];
let categories = [];
let currentEditingProduct = null;
let currentEditingCategory = null;

// Éléments DOM
const loginScreen = document.getElementById('loginScreen');
const dashboard = document.getElementById('dashboard');
const loginForm = document.getElementById('loginForm');
const logoutBtn = document.getElementById('logoutBtn');
const productModal = document.getElementById('productModal');
const categoryModal = document.getElementById('categoryModal');
const deleteModal = document.getElementById('deleteModal');
const productForm = document.getElementById('productForm');
const categoryForm = document.getElementById('categoryForm');
const productSearch = document.getElementById('productSearch');
const productsGrid = document.getElementById('productsGrid');
const categoriesGrid = document.getElementById('categoriesGrid');

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
});

function initializeApp() {
    // Vérifier si l'utilisateur est déjà connecté
    const savedUser = localStorage.getItem('adminUser');
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
        showDashboard();
        loadCategories();
        loadProducts();
        updateStats();
    }
}

function setupEventListeners() {
    // Login
    loginForm.addEventListener('submit', handleLogin);
    logoutBtn.addEventListener('click', handleLogout);

    // Navigation
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.addEventListener('click', () => switchSection(btn.dataset.section));
    });

    // Produits
    document.getElementById('addProductBtn').addEventListener('click', () => openProductModal());
    productSearch.addEventListener('input', debounce(handleProductSearch, 300));
    productForm.addEventListener('submit', handleProductSubmit);

    // Catégories
    document.getElementById('addCategoryBtn').addEventListener('click', () => openCategoryModal());
    categoryForm.addEventListener('submit', handleCategorySubmit);

    // Modals
    document.getElementById('closeModal').addEventListener('click', closeProductModal);
    document.getElementById('cancelProduct').addEventListener('click', closeProductModal);
    document.getElementById('closeCategoryModal').addEventListener('click', closeCategoryModal);
    document.getElementById('cancelCategory').addEventListener('click', closeCategoryModal);
    document.getElementById('closeDeleteModal').addEventListener('click', closeDeleteModal);
    document.getElementById('cancelDelete').addEventListener('click', closeDeleteModal);
    document.getElementById('confirmDelete').addEventListener('click', confirmDeleteItem);

    // Prix multiples
    document.getElementById('addPriceBtn').addEventListener('click', addPriceField);

    // Media upload
    document.getElementById('productMedia').addEventListener('change', handleMediaUpload);

    // Fermer les modals en cliquant à l'extérieur
    window.addEventListener('click', (e) => {
        if (e.target === productModal) closeProductModal();
        if (e.target === categoryModal) closeCategoryModal();
        if (e.target === deleteModal) closeDeleteModal();
    });
}

// Authentification
function handleLogin(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorElement = document.getElementById('loginError');

    if (username === ADMIN_CREDENTIALS.username && password === ADMIN_CREDENTIALS.password) {
        currentUser = { username, role: 'admin' };
        localStorage.setItem('adminUser', JSON.stringify(currentUser));
        showDashboard();
        loadCategories();
        loadProducts();
        updateStats();
        errorElement.style.display = 'none';
    } else {
        errorElement.textContent = 'Nom d\'utilisateur ou mot de passe incorrect';
        errorElement.style.display = 'block';
    }
}

function handleLogout() {
    currentUser = null;
    localStorage.removeItem('adminUser');
    showLoginScreen();
}

function showLoginScreen() {
    loginScreen.style.display = 'flex';
    dashboard.style.display = 'none';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
}

function showDashboard() {
    loginScreen.style.display = 'none';
    dashboard.style.display = 'flex';
}

// Navigation
function switchSection(sectionName) {
    // Mettre à jour les boutons de navigation
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');

    // Afficher la section correspondante
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(sectionName).classList.add('active');

    // Charger les données si nécessaire
    if (sectionName === 'products' && products.length === 0) {
        loadProducts();
    } else if (sectionName === 'categories' && categories.length === 0) {
        loadCategories();
    } else if (sectionName === 'stats') {
        updateStats();
    }
}

// Gestion des catégories
async function loadCategories() {
    try {
        // Simulation des données de catégories
        categories = [
            { id: 1, name: 'MOUNTAINS GIANT', description: 'Produits de montagne', parent_id: null, sort_order: 1, product_count: 5 },
            { id: 2, name: 'Z HASH', description: 'Produits premium', parent_id: null, sort_order: 2, product_count: 3 },
            { id: 3, name: 'STATIC MAROC 🇲🇦', description: 'Produits du Maroc', parent_id: null, sort_order: 3, product_count: 2 }
        ];
        
        renderCategories(categories);
        populateCategorySelects();
    } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
        showNotification('Erreur lors du chargement des catégories', 'error');
    }
}

function renderCategories(categoriesToRender) {
    categoriesGrid.innerHTML = '';

    if (categoriesToRender.length === 0) {
        categoriesGrid.innerHTML = '<p class="placeholder-text">Aucune catégorie trouvée</p>';
        return;
    }

    categoriesToRender.forEach(category => {
        const card = createCategoryCard(category);
        categoriesGrid.appendChild(card);
    });
}

function createCategoryCard(category) {
    const card = document.createElement('div');
    card.className = 'category-card';
    
    card.innerHTML = `
        <div class="category-header">
            <h3 class="category-name">${category.name}</h3>
            <div class="category-actions">
                <button class="action-btn edit-btn" onclick="editCategory(${category.id})">
                    <i class="ri-edit-line"></i>
                </button>
                <button class="action-btn delete-btn" onclick="deleteCategory(${category.id}, '${category.name}')">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
        <div class="category-description">${category.description || 'Aucune description'}</div>
        <div class="category-stats">
            <span>${category.product_count || 0} produits</span>
            <span>Ordre: ${category.sort_order || 0}</span>
        </div>
    `;

    return card;
}

function populateCategorySelects() {
    const productCategorySelect = document.getElementById('productCategory');
    const categoryParentSelect = document.getElementById('categoryParent');
    
    // Vider les options existantes (sauf la première)
    productCategorySelect.innerHTML = '<option value="">Sélectionner une catégorie</option>';
    categoryParentSelect.innerHTML = '<option value="">Aucune (catégorie principale)</option>';
    
    categories.forEach(category => {
        const option1 = document.createElement('option');
        option1.value = category.id;
        option1.textContent = category.name;
        productCategorySelect.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = category.id;
        option2.textContent = category.name;
        categoryParentSelect.appendChild(option2);
    });
}

// Gestion des produits
async function loadProducts() {
    try {
        // Simulation des données de produits
        products = [
            {
                id: 1,
                product_name: 'Z HASH Premium',
                category_id: 2,
                category_name: 'Z HASH',
                price: 110,
                weight: '5',
                country: 'STATIC MAROC 🇲🇦',
                description: 'Produit premium de haute qualité',
                stock: 10,
                media: './hModuTC.jpeg',
                prices: [
                    { price: 110, type: 'regular', currency: 'EUR' },
                    { price: 95, type: 'sale', currency: 'EUR' }
                ]
            },
            {
                id: 2,
                product_name: 'Mountain Giant Special',
                category_id: 1,
                category_name: 'MOUNTAINS GIANT',
                price: 85,
                weight: '3',
                country: 'STATIC MAROC 🇲🇦',
                description: 'Produit spécial de montagne',
                stock: 15,
                media: './hModuTC.jpeg',
                prices: [
                    { price: 85, type: 'regular', currency: 'EUR' }
                ]
            }
        ];
        
        renderProducts(products);
    } catch (error) {
        console.error('Erreur lors du chargement des produits:', error);
        showNotification('Erreur lors du chargement des produits', 'error');
    }
}

function renderProducts(productsToRender) {
    productsGrid.innerHTML = '';

    if (productsToRender.length === 0) {
        productsGrid.innerHTML = '<p class="placeholder-text">Aucun produit trouvé</p>';
        return;
    }

    productsToRender.forEach(product => {
        const card = createProductCard(product);
        productsGrid.appendChild(card);
    });
}

function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    
    const imageSrc = product.media && product.media !== '' 
        ? (product.media.startsWith('http') ? product.media : product.media)
        : './hModuTC.jpeg';

    const isVideo = imageSrc.match(/\.(mp4|webm|ogg)$/i);
    const mediaElement = isVideo 
        ? `<video src="${imageSrc}" muted loop></video>`
        : `<img src="${imageSrc}" alt="${product.product_name}" onerror="this.src='./hModuTC.jpeg'">`;

    const pricesHtml = product.prices ? product.prices.map(price => 
        `<span class="price-tag">${price.price}€ (${price.type})</span>`
    ).join('') : `<span class="price-tag">${product.price}€</span>`;

    card.innerHTML = `
        <div class="product-image">
            ${mediaElement}
            <div class="product-tag">
                <i class="ri-price-tag-3-line"></i>
                <span>${product.country || 'STATIC MAROC 🇲🇦'}</span>
            </div>
        </div>
        <div class="product-info">
            <h3 class="product-name">${product.product_name || 'Z HASH'}</h3>
            <div class="product-category">
                <i class="ri-home-5-line"></i>
                <span>${product.category_name || 'MOUNTAINS GIANT'}</span>
            </div>
            <div class="product-prices">
                ${pricesHtml}
            </div>
            <div class="product-price">${product.weight || '5'}g</div>
            <div class="product-actions">
                <button class="action-btn edit-btn" onclick="editProduct(${product.id})">
                    <i class="ri-edit-line"></i>
                    Modifier
                </button>
                <button class="action-btn delete-btn" onclick="deleteProduct(${product.id}, '${product.product_name}')">
                    <i class="ri-delete-bin-line"></i>
                    Supprimer
                </button>
            </div>
        </div>
    `;

    return card;
}

function handleProductSearch() {
    const searchTerm = productSearch.value.toLowerCase();
    const filteredProducts = products.filter(product => 
        product.product_name?.toLowerCase().includes(searchTerm) ||
        product.category_name?.toLowerCase().includes(searchTerm) ||
        product.country?.toLowerCase().includes(searchTerm) ||
        product.description?.toLowerCase().includes(searchTerm)
    );
    renderProducts(filteredProducts);
}

// Modal de produit
function openProductModal(productId = null) {
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('productForm');
    const mediaPreview = document.getElementById('mediaPreview');

    // Réinitialiser le formulaire
    form.reset();
    mediaPreview.innerHTML = '';
    document.getElementById('productId').value = '';
    resetPricesContainer();

    if (productId) {
        // Mode édition
        const product = products.find(p => p.id == productId);
        if (product) {
            currentEditingProduct = product;
            modalTitle.textContent = 'Modifier le produit';
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.product_name || '';
            document.getElementById('productCategory').value = product.category_id || '';
            document.getElementById('productWeight').value = product.weight || '';
            document.getElementById('productCountry').value = product.country || '';
            document.getElementById('productStock').value = product.stock || 0;
            document.getElementById('productDescription').value = product.description || '';

            // Charger les prix
            if (product.prices && product.prices.length > 0) {
                product.prices.forEach((price, index) => {
                    if (index === 0) {
                        // Utiliser le premier prix existant
                        document.querySelector('.price-input').value = price.price;
                        document.querySelector('.price-type').value = price.type;
                        document.querySelector('.price-currency').value = price.currency;
                    } else {
                        // Ajouter des champs supplémentaires
                        addPriceField(price.price, price.type, price.currency);
                    }
                });
            }

            if (product.media) {
                const mediaItem = createMediaPreview(product.media, product.media);
                mediaPreview.appendChild(mediaItem);
            }
        }
    } else {
        // Mode ajout
        currentEditingProduct = null;
        modalTitle.textContent = 'Ajouter un produit';
    }

    modal.style.display = 'block';
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
    currentEditingProduct = null;
}

// Gestion des prix multiples
function addPriceField(price = '', type = 'regular', currency = 'EUR') {
    const container = document.getElementById('pricesContainer');
    const priceItem = document.createElement('div');
    priceItem.className = 'price-item';
    
    priceItem.innerHTML = `
        <input type="number" placeholder="Prix (€)" step="0.01" class="price-input" value="${price}" required>
        <select class="price-type">
            <option value="regular" ${type === 'regular' ? 'selected' : ''}>Prix normal</option>
            <option value="sale" ${type === 'sale' ? 'selected' : ''}>Prix soldé</option>
            <option value="wholesale" ${type === 'wholesale' ? 'selected' : ''}>Prix gros</option>
            <option value="promotional" ${type === 'promotional' ? 'selected' : ''}>Prix promotionnel</option>
        </select>
        <input type="text" placeholder="Devise (EUR)" value="${currency}" class="price-currency">
        <button type="button" class="remove-price-btn" onclick="removePrice(this)">
            <i class="ri-close-line"></i>
        </button>
    `;
    
    container.appendChild(priceItem);
}

function removePrice(button) {
    const priceItem = button.closest('.price-item');
    if (document.querySelectorAll('.price-item').length > 1) {
        priceItem.remove();
    }
}

function resetPricesContainer() {
    const container = document.getElementById('pricesContainer');
    container.innerHTML = `
        <div class="price-item">
            <input type="number" placeholder="Prix (€)" step="0.01" class="price-input" required>
            <select class="price-type">
                <option value="regular">Prix normal</option>
                <option value="sale">Prix soldé</option>
                <option value="wholesale">Prix gros</option>
                <option value="promotional">Prix promotionnel</option>
            </select>
            <input type="text" placeholder="Devise (EUR)" value="EUR" class="price-currency">
            <button type="button" class="remove-price-btn" onclick="removePrice(this)">
                <i class="ri-close-line"></i>
            </button>
        </div>
    `;
}

// Gestion des médias
function handleMediaUpload(e) {
    const files = Array.from(e.target.files);
    const mediaPreview = document.getElementById('mediaPreview');
    
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const mediaItem = createMediaPreview(e.target.result, file.name);
            mediaPreview.appendChild(mediaItem);
        };
        reader.readAsDataURL(file);
    });
}

function createMediaPreview(src, filename) {
    const mediaItem = document.createElement('div');
    mediaItem.className = 'media-item';
    
    const isVideo = filename.match(/\.(mp4|webm|ogg)$/i);
    const mediaElement = isVideo 
        ? `<video src="${src}" muted loop></video>`
        : `<img src="${src}" alt="Media">`;
    
    mediaItem.innerHTML = `
        ${mediaElement}
        <button class="remove-media" onclick="removeMedia(this)">
            <i class="ri-close-line"></i>
        </button>
    `;
    
    return mediaItem;
}

function removeMedia(button) {
    button.closest('.media-item').remove();
}

async function handleProductSubmit(e) {
    e.preventDefault();

    const formData = new FormData();
    const productId = document.getElementById('productId').value;
    
    // Récupérer les prix
    const prices = [];
    document.querySelectorAll('.price-item').forEach(item => {
        const price = item.querySelector('.price-input').value;
        const type = item.querySelector('.price-type').value;
        const currency = item.querySelector('.price-currency').value;
        
        if (price) {
            prices.push({ price: parseFloat(price), type, currency });
        }
    });

    // Ajouter les données du formulaire
    formData.append('product_name', document.getElementById('productName').value);
    formData.append('category_id', document.getElementById('productCategory').value);
    formData.append('weight', document.getElementById('productWeight').value);
    formData.append('country', document.getElementById('productCountry').value);
    formData.append('stock', document.getElementById('productStock').value);
    formData.append('description', document.getElementById('productDescription').value);
    formData.append('prices', JSON.stringify(prices));

    if (productId) {
        formData.append('id', productId);
    }

    // Ajouter les médias
    const mediaFiles = document.getElementById('productMedia').files;
    for (let i = 0; i < mediaFiles.length; i++) {
        formData.append('media[]', mediaFiles[i]);
    }

    try {
        // Simulation de sauvegarde
        if (productId) {
            // Mise à jour
            const index = products.findIndex(p => p.id == productId);
            if (index !== -1) {
                products[index] = {
                    ...products[index],
                    product_name: document.getElementById('productName').value,
                    category_id: parseInt(document.getElementById('productCategory').value),
                    category_name: categories.find(c => c.id == document.getElementById('productCategory').value)?.name,
                    weight: document.getElementById('productWeight').value,
                    country: document.getElementById('productCountry').value,
                    stock: parseInt(document.getElementById('productStock').value),
                    description: document.getElementById('productDescription').value,
                    prices: prices
                };
            }
        } else {
            // Nouveau produit
            const newProduct = {
                id: Date.now(),
                product_name: document.getElementById('productName').value,
                category_id: parseInt(document.getElementById('productCategory').value),
                category_name: categories.find(c => c.id == document.getElementById('productCategory').value)?.name,
                weight: document.getElementById('productWeight').value,
                country: document.getElementById('productCountry').value,
                stock: parseInt(document.getElementById('productStock').value),
                description: document.getElementById('productDescription').value,
                media: './hModuTC.jpeg',
                prices: prices
            };
            products.push(newProduct);
        }

        showNotification(
            productId ? 'Produit modifié avec succès' : 'Produit ajouté avec succès',
            'success'
        );
        closeProductModal();
        loadProducts();
        updateStats();
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de l\'enregistrement', 'error');
    }
}

function editProduct(productId) {
    openProductModal(productId);
}

function deleteProduct(productId, productName) {
    openDeleteModal('product', productId, productName);
}

// Modal de catégorie
function openCategoryModal(categoryId = null) {
    const modal = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('categoryModalTitle');
    const form = document.getElementById('categoryForm');

    // Réinitialiser le formulaire
    form.reset();
    document.getElementById('categoryId').value = '';

    if (categoryId) {
        // Mode édition
        const category = categories.find(c => c.id == categoryId);
        if (category) {
            currentEditingCategory = category;
            modalTitle.textContent = 'Modifier la catégorie';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name || '';
            document.getElementById('categoryDescription').value = category.description || '';
            document.getElementById('categoryParent').value = category.parent_id || '';
            document.getElementById('categorySortOrder').value = category.sort_order || 0;
        }
    } else {
        // Mode ajout
        currentEditingCategory = null;
        modalTitle.textContent = 'Ajouter une catégorie';
    }

    modal.style.display = 'block';
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
    currentEditingCategory = null;
}

async function handleCategorySubmit(e) {
    e.preventDefault();

    const categoryId = document.getElementById('categoryId').value;
    const categoryData = {
        name: document.getElementById('categoryName').value,
        description: document.getElementById('categoryDescription').value,
        parent_id: document.getElementById('categoryParent').value || null,
        sort_order: parseInt(document.getElementById('categorySortOrder').value) || 0
    };

    try {
        if (categoryId) {
            // Mise à jour
            const index = categories.findIndex(c => c.id == categoryId);
            if (index !== -1) {
                categories[index] = { ...categories[index], ...categoryData };
            }
        } else {
            // Nouvelle catégorie
            const newCategory = {
                id: Date.now(),
                ...categoryData,
                product_count: 0
            };
            categories.push(newCategory);
        }

        showNotification(
            categoryId ? 'Catégorie modifiée avec succès' : 'Catégorie ajoutée avec succès',
            'success'
        );
        closeCategoryModal();
        loadCategories();
        updateStats();
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de l\'enregistrement', 'error');
    }
}

function editCategory(categoryId) {
    openCategoryModal(categoryId);
}

function deleteCategory(categoryId, categoryName) {
    openDeleteModal('category', categoryId, categoryName);
}

// Modal de suppression
function openDeleteModal(type, id, name) {
    const modal = document.getElementById('deleteModal');
    const itemNameElement = modal.querySelector('.delete-item-name');
    
    itemNameElement.textContent = name;
    modal.dataset.deleteType = type;
    modal.dataset.deleteId = id;
    modal.style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

async function confirmDeleteItem() {
    const modal = document.getElementById('deleteModal');
    const type = modal.dataset.deleteType;
    const id = modal.dataset.deleteId;

    try {
        if (type === 'product') {
            const index = products.findIndex(p => p.id == id);
            if (index !== -1) {
                products.splice(index, 1);
            }
        } else if (type === 'category') {
            const index = categories.findIndex(c => c.id == id);
            if (index !== -1) {
                categories.splice(index, 1);
            }
        }

        showNotification('Élément supprimé avec succès', 'success');
        closeDeleteModal();
        
        if (type === 'product') {
            loadProducts();
        } else if (type === 'category') {
            loadCategories();
        }
        updateStats();
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la suppression', 'error');
    }
}

// Statistiques
async function updateStats() {
    try {
        document.getElementById('totalProducts').textContent = products.length;
        document.getElementById('totalCategories').textContent = categories.length;
        document.getElementById('totalOrders').textContent = '0'; // À implémenter
        document.getElementById('totalRevenue').textContent = '0€'; // À implémenter
    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
    }
}

// Utilitaires
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Créer une notification temporaire
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        ${type === 'success' ? 'background: #28a745;' : ''}
        ${type === 'error' ? 'background: #dc3545;' : ''}
        ${type === 'info' ? 'background: #17a2b8;' : ''}
    `;

    document.body.appendChild(notification);

    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Styles CSS pour les animations de notification
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);