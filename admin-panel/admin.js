// Panel Admin JavaScript pour Ma Boutique - Version 2.0
$(document).ready(function() {
    let currentPage = 1;
    let productsPerPage = 10;
    let allProducts = [];
    let filteredProducts = [];
    let isLoading = false;

    // Initialisation
    init();

    function init() {
        loadDashboardStats();
        loadProducts();
        loadCategories();
        setupEventListeners();
        setupSearchFunctionality();
        loadRecentActivity();
    }

    // Chargement des statistiques du dashboard
    function loadDashboardStats() {
        $.ajax({
            url: 'products_api.php',
            method: 'GET',
            data: { action: 'get_stats' },
            success: function(response) {
                if (response.success) {
                    $('#totalProducts').text(response.stats.total_products || 0);
                    $('#totalOrders').text(response.stats.total_orders || 0);
                    $('#totalCustomers').text(response.stats.total_customers || 0);
                    $('#totalRevenue').text((response.stats.total_revenue || 0) + '€');
                } else {
                    console.log('Erreur lors du chargement des statistiques:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Erreur lors du chargement des statistiques:', error);
                // Utiliser des valeurs par défaut
                $('#totalProducts').text('0');
                $('#totalOrders').text('0');
                $('#totalCustomers').text('0');
                $('#totalRevenue').text('0€');
            }
        });
    }

    // Chargement des produits
    function loadProducts() {
        if (isLoading) return;
        
        isLoading = true;
        const tbody = $('#productsTable tbody');
        tbody.html(`
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                    <br>Chargement des produits...
                </td>
            </tr>
        `);

        $.ajax({
            url: 'products_api.php',
            method: 'GET',
            data: { action: 'get_all' },
            success: function(response) {
                isLoading = false;
                if (response.success) {
                    allProducts = response.products || [];
                    filteredProducts = [...allProducts];
                    displayProducts();
                    updatePagination();
                } else {
                    showAlert('Erreur lors du chargement des produits: ' + (response.message || 'Erreur inconnue'), 'danger');
                    tbody.html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                                <br>Erreur lors du chargement
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr, status, error) {
                isLoading = false;
                console.log('Erreur de connexion au serveur:', error);
                showAlert('Erreur de connexion au serveur', 'danger');
                tbody.html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger">
                            <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                            <br>Erreur de connexion
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Affichage des produits
    function displayProducts() {
        const startIndex = (currentPage - 1) * productsPerPage;
        const endIndex = startIndex + productsPerPage;
        const productsToShow = filteredProducts.slice(startIndex, endIndex);

        const tbody = $('#productsTable tbody');
        tbody.empty();

        if (productsToShow.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <i class="fa fa-box-open fa-2x mb-2"></i>
                        <br>Aucun produit trouvé
                    </td>
                </tr>
            `);
            return;
        }

        productsToShow.forEach(product => {
            const mediaPreview = product.media ? 
                `<img src="${product.media}" alt="${product.product_name}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">` :
                '<i class="fa fa-image text-muted"></i>';

            const row = `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            ${mediaPreview}
                            <div class="ms-3">
                                <h6 class="mb-0">${escapeHtml(product.product_name)}</h6>
                                <small class="text-muted">ID: ${product.id}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-success">${product.price}€</span></td>
                    <td>
                        ${product.media ? 
                            `<a href="${product.media}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-eye"></i>
                            </a>` : 
                            '<span class="text-muted">Aucun média</span>'
                        }
                    </td>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${escapeHtml(product.description)}">
                            ${escapeHtml(product.description)}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-info">${escapeHtml(product.category_name || 'Non catégorisé')}</span>
                    </td>
                    <td>${escapeHtml(product.country || 'N/A')}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary edit-product" data-id="${product.id}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-product" data-id="${product.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Mise à jour de la pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
        const pagination = $('#pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        let paginationHtml = '<ul class="pagination pagination-sm mb-0">';
        
        // Bouton précédent
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fa fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Bouton suivant
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fa fa-chevron-right"></i>
                </a>
            </li>
        `;

        paginationHtml += '</ul>';
        pagination.html(paginationHtml);
    }

    // Chargement des catégories
    function loadCategories() {
        $.ajax({
            url: 'categories_api.php',
            method: 'GET',
            data: { action: 'get_all' },
            success: function(response) {
                if (response.success) {
                    const categorySelect = $('#category');
                    categorySelect.find('option:not(:first)').remove();
                    
                    response.categories.forEach(category => {
                        categorySelect.append(`
                            <option value="${category.id}">${escapeHtml(category.name)}</option>
                        `);
                    });
                } else {
                    console.log('Erreur lors du chargement des catégories:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Erreur lors du chargement des catégories:', error);
            }
        });
    }

    // Chargement de l'activité récente
    function loadRecentActivity() {
        $.ajax({
            url: 'products_api.php',
            method: 'GET',
            data: { action: 'get_recent_activity' },
            success: function(response) {
                if (response.success && response.activities) {
                    displayRecentActivity(response.activities);
                } else {
                    displayDefaultActivity();
                }
            },
            error: function() {
                displayDefaultActivity();
            }
        });
    }

    // Affichage de l'activité récente
    function displayRecentActivity(activities) {
        const container = $('#recentActivity');
        container.empty();

        if (activities.length === 0) {
            container.html(`
                <div class="text-center text-muted">
                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                    <br>Aucune activité récente
                </div>
            `);
            return;
        }

        activities.forEach(activity => {
            const activityHtml = `
                <div class="d-flex align-items-center border-bottom py-3">
                    <i class="fa ${activity.icon} text-${activity.color} me-3"></i>
                    <div class="w-100 ms-3">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-0">${escapeHtml(activity.title)}</h6>
                            <small>${activity.time}</small>
                        </div>
                        <span>${escapeHtml(activity.description)}</span>
                    </div>
                </div>
            `;
            container.append(activityHtml);
        });
    }

    // Affichage de l'activité par défaut
    function displayDefaultActivity() {
        const container = $('#recentActivity');
        container.html(`
            <div class="d-flex align-items-center border-bottom py-3">
                <i class="fa fa-info-circle text-info me-3"></i>
                <div class="w-100 ms-3">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-0">Bienvenue dans votre panel admin</h6>
                        <small>Maintenant</small>
                    </div>
                    <span>Commencez par ajouter vos premiers produits</span>
                </div>
            </div>
        `);
    }

    // Configuration des événements
    function setupEventListeners() {
        // Boutons d'ajout de produit
        $('#addProductBtn, #addProductBtn2').on('click', function() {
            resetProductForm();
            $('#productModalLabel').text('Ajouter un Produit');
            $('#productModal').modal('show');
        });

        // Soumission du formulaire de produit
        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            saveProduct();
        });

        // Édition de produit
        $(document).on('click', '.edit-product', function() {
            const productId = $(this).data('id');
            editProduct(productId);
        });

        // Suppression de produit
        $(document).on('click', '.delete-product', function() {
            const productId = $(this).data('id');
            $('#deleteProductId').val(productId);
            $('#deleteModal').modal('show');
        });

        // Confirmation de suppression
        $('#confirmDeleteBtn').on('click', function() {
            const productId = $('#deleteProductId').val();
            deleteProduct(productId);
            $('#deleteModal').modal('hide');
        });

        // Pagination
        $(document).on('click', '.pagination .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page && page > 0 && page <= Math.ceil(filteredProducts.length / productsPerPage)) {
                currentPage = page;
                displayProducts();
                updatePagination();
            }
        });

        // Prévisualisation des médias
        $('#media').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#mediaPreview').html(`
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    `);
                };
                reader.readAsDataURL(file);
            } else {
                $('#mediaPreview').empty();
            }
        });

        // Boutons d'action rapide
        $('#viewAnalyticsBtn').on('click', function() {
            showAlert('Fonctionnalité analytics en cours de développement', 'info');
        });

        $('#settingsBtn').on('click', function() {
            showAlert('Fonctionnalité paramètres en cours de développement', 'info');
        });
    }

    // Configuration de la recherche
    function setupSearchFunctionality() {
        let searchTimeout;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val().trim();
            
            searchTimeout = setTimeout(function() {
                filterProducts(searchTerm);
            }, 300);
        });
    }

    // Filtrage des produits
    function filterProducts(searchTerm) {
        if (!searchTerm) {
            filteredProducts = [...allProducts];
        } else {
            filteredProducts = allProducts.filter(product => 
                product.product_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                product.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (product.category_name && product.category_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (product.country && product.country.toLowerCase().includes(searchTerm.toLowerCase()))
            );
        }
        
        currentPage = 1;
        displayProducts();
        updatePagination();
    }

    // Sauvegarde d'un produit
    function saveProduct() {
        const formData = new FormData($('#productForm')[0]);
        
        $.ajax({
            url: 'products_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#productModal').modal('hide');
                    loadProducts();
                    loadDashboardStats();
                } else {
                    showAlert('Erreur: ' + (response.message || 'Erreur inconnue'), 'danger');
                }
            },
            error: function(xhr, status, error) {
                showAlert('Erreur de connexion au serveur', 'danger');
            }
        });
    }

    // Édition d'un produit
    function editProduct(productId) {
        $.ajax({
            url: 'products_api.php',
            method: 'GET',
            data: { id: productId },
            success: function(response) {
                if (response.success && response.products && response.products.length > 0) {
                    const product = response.products[0];
                    
                    $('#productId').val(product.id);
                    $('#productName').val(product.product_name);
                    $('#price').val(product.price);
                    $('#description').val(product.description);
                    $('#category').val(product.category_id || '');
                    $('#country').val(product.country || '');
                    $('#weight').val(product.weight || '');
                    $('#stock').val(product.stock || 0);
                    
                    if (product.media) {
                        $('#mediaPreview').html(`
                            <img src="${product.media}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        `);
                    } else {
                        $('#mediaPreview').empty();
                    }
                    
                    $('#productModalLabel').text('Modifier le Produit');
                    $('#productModal').modal('show');
                } else {
                    showAlert('Erreur lors du chargement du produit', 'danger');
                }
            },
            error: function() {
                showAlert('Erreur de connexion au serveur', 'danger');
            }
        });
    }

    // Suppression d'un produit
    function deleteProduct(productId) {
        $.ajax({
            url: 'products_api.php',
            method: 'DELETE',
            data: { id: productId },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    loadProducts();
                    loadDashboardStats();
                } else {
                    showAlert('Erreur: ' + (response.message || 'Erreur inconnue'), 'danger');
                }
            },
            error: function() {
                showAlert('Erreur de connexion au serveur', 'danger');
            }
        });
    }

    // Réinitialisation du formulaire de produit
    function resetProductForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#mediaPreview').empty();
        $('#productModalLabel').text('Ajouter un Produit');
    }

    // Affichage d'alertes
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-dismiss après 5 secondes
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    // Fonction d'échappement HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});