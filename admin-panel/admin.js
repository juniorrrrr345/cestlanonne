// Admin Panel JavaScript
$(document).ready(function() {
    // Initialisation
    initAdminPanel();
    
    // Gestion du toggle sidebar
    $('.sidebar-toggler').on('click', function() {
        $('.sidebar').toggleClass('open');
        $('.content').toggleClass('open');
    });
    
    // Gestion des produits
    initProductManagement();
    
    // Gestion des catégories
    initCategoryManagement();
    
    // Vérification de session périodique
    setInterval(checkSession, 300000); // Toutes les 5 minutes
});

function initAdminPanel() {
    // Masquer le spinner
    $('#spinner').hide();
    
    // Initialiser les tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialiser les popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Gestion de la déconnexion
    $('.logout-btn').on('click', function(e) {
        e.preventDefault();
        logout();
    });
}

function initProductManagement() {
    // Charger les produits au démarrage
    loadProducts();
    
    // Gestion du bouton d'ajout de produit
    $('#addProductBtn').on('click', function() {
        $('#productModalLabel').text('Ajouter un produit');
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#productModal').modal('show');
    });
    
    // Gestion du formulaire de produit
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        saveProduct();
    });
    
    // Gestion de la recherche
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterProducts(searchTerm);
    });
    
    // Gestion de la capture vidéo/photo
    initMediaCapture();
}

function initCategoryManagement() {
    // Charger les catégories
    loadCategories();
    
    // Gestion du bouton d'ajout de catégorie
    $('#addCategoryBtn').on('click', function() {
        $('#categoryModalLabel').text('Ajouter une catégorie');
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        $('#categoryModal').modal('show');
    });
    
    // Gestion du formulaire de catégorie
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        saveCategory();
    });
}

function loadProducts(page = 1) {
    $.ajax({
        url: 'products_api.php',
        type: 'GET',
        data: { action: 'get_products', page: page },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProducts(response.data);
                updatePagination(response.pagination);
            } else {
                showAlert('Erreur lors du chargement des produits', 'danger');
            }
        },
        error: function() {
            showAlert('Erreur de connexion au serveur', 'danger');
        }
    });
}

function displayProducts(products) {
    const tbody = $('#productsTable tbody');
    tbody.empty();
    
    products.forEach(product => {
        const row = `
            <tr>
                <td>${escapeHtml(product.product_name)}</td>
                <td>${formatPrice(product.price)}</td>
                <td>
                    ${product.media ? `<img src="${product.media}" alt="${product.product_name}" style="max-width: 50px; max-height: 50px;" class="img-thumbnail">` : 'Aucun média'}
                </td>
                <td>${escapeHtml(product.description)}</td>
                <td>${escapeHtml(product.category_name || 'Non catégorisé')}</td>
                <td>${product.weight || 'N/A'}</td>
                <td>${escapeHtml(product.country || 'N/A')}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-product" data-id="${product.id}">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-product" data-id="${product.id}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Gestion des boutons d'édition
    $('.edit-product').on('click', function() {
        const productId = $(this).data('id');
        editProduct(productId);
    });
    
    // Gestion des boutons de suppression
    $('.delete-product').on('click', function() {
        const productId = $(this).data('id');
        deleteProduct(productId);
    });
}

function saveProduct() {
    const formData = new FormData($('#productForm')[0]);
    formData.append('action', 'save_product');
    
    $.ajax({
        url: 'products_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('Produit sauvegardé avec succès', 'success');
                $('#productModal').modal('hide');
                loadProducts();
            } else {
                showAlert(response.message || 'Erreur lors de la sauvegarde', 'danger');
            }
        },
        error: function() {
            showAlert('Erreur de connexion au serveur', 'danger');
        }
    });
}

function editProduct(productId) {
    $.ajax({
        url: 'products_api.php',
        type: 'GET',
        data: { action: 'get_product', id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const product = response.data;
                $('#productModalLabel').text('Modifier le produit');
                $('#productId').val(product.id);
                $('#productName').val(product.product_name);
                $('#price').val(product.price);
                $('#description').val(product.description);
                $('#category').val(product.category_id);
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

function deleteProduct(productId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        $.ajax({
            url: 'products_api.php',
            type: 'POST',
            data: { action: 'delete_product', id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('Produit supprimé avec succès', 'success');
                    loadProducts();
                } else {
                    showAlert(response.message || 'Erreur lors de la suppression', 'danger');
                }
            },
            error: function() {
                showAlert('Erreur de connexion au serveur', 'danger');
            }
        });
    }
}

function loadCategories() {
    $.ajax({
        url: 'categories_api.php',
        type: 'GET',
        data: { action: 'get_categories' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateCategorySelect(response.data);
            }
        },
        error: function() {
            console.error('Erreur lors du chargement des catégories');
        }
    });
}

function updateCategorySelect(categories) {
    const select = $('#category');
    select.find('option:not(:first)').remove();
    
    categories.forEach(category => {
        select.append(`<option value="${category.id}">${escapeHtml(category.name)}</option>`);
    });
}

function saveCategory() {
    const formData = new FormData($('#categoryForm')[0]);
    formData.append('action', 'save_category');
    
    $.ajax({
        url: 'categories_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('Catégorie sauvegardée avec succès', 'success');
                $('#categoryModal').modal('hide');
                loadCategories();
            } else {
                showAlert(response.message || 'Erreur lors de la sauvegarde', 'danger');
            }
        },
        error: function() {
            showAlert('Erreur de connexion au serveur', 'danger');
        }
    });
}

function initMediaCapture() {
    // Gestion de la capture vidéo
    $('#captureVideoBtn').on('click', function() {
        startVideoCapture();
    });
    
    // Gestion de la capture photo
    $('#capturePhotoBtn').on('click', function() {
        startPhotoCapture();
    });
    
    // Gestion de l'upload de fichier
    $('#uploadFileBtn').on('click', function() {
        $('#media').click();
    });
    
    // Gestion de la sélection de fichier
    $('#media').on('change', function() {
        const file = this.files[0];
        if (file) {
            previewFile(file);
        }
    });
}

function startVideoCapture() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            const video = $('#captureVideo')[0];
            video.srcObject = stream;
            video.style.display = 'block';
            $('#captureControls').removeClass('d-none');
        })
        .catch(function(err) {
            showAlert('Erreur d\'accès à la caméra', 'danger');
        });
}

function startPhotoCapture() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            const video = $('#captureVideo')[0];
            video.srcObject = stream;
            video.style.display = 'block';
            $('#captureControls').removeClass('d-none');
        })
        .catch(function(err) {
            showAlert('Erreur d\'accès à la caméra', 'danger');
        });
}

function previewFile(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = $('#mediaPreview');
        if (file.type.startsWith('image/')) {
            preview.html(`<img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">`);
        } else if (file.type.startsWith('video/')) {
            preview.html(`<video src="${e.target.result}" controls style="max-width: 200px;"></video>`);
        }
    };
    reader.readAsDataURL(file);
}

function filterProducts(searchTerm) {
    $('#productsTable tbody tr').each(function() {
        const productName = $(this).find('td:first').text().toLowerCase();
        if (productName.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function updatePagination(pagination) {
    const container = $('#pagination');
    container.empty();
    
    if (pagination.total_pages > 1) {
        const ul = $('<ul class="pagination"></ul>');
        
        // Bouton précédent
        if (pagination.current_page > 1) {
            ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">Précédent</a></li>`);
        }
        
        // Pages
        for (let i = 1; i <= pagination.total_pages; i++) {
            const active = i === pagination.current_page ? 'active' : '';
            ul.append(`<li class="page-item ${active}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }
        
        // Bouton suivant
        if (pagination.current_page < pagination.total_pages) {
            ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">Suivant</a></li>`);
        }
        
        container.append(ul);
        
        // Gestion des clics sur la pagination
        $('.page-link').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadProducts(page);
        });
    }
}

function checkSession() {
    $.ajax({
        url: 'auth.php',
        type: 'POST',
        data: { action: 'check_session' },
        dataType: 'json',
        success: function(response) {
            if (!response.valid) {
                showAlert('Session expirée, redirection...', 'warning');
                setTimeout(function() {
                    window.location.href = 'signin.html';
                }, 2000);
            }
        }
    });
}

function logout() {
    $.ajax({
        url: 'auth.php',
        type: 'POST',
        data: { action: 'logout' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = 'signin.html';
            }
        }
    });
}

function showAlert(message, type) {
    const alert = $(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('.content').prepend(alert);
    
    // Auto-dismiss après 5 secondes
    setTimeout(function() {
        alert.alert('close');
    }, 5000);
}

function showMessage(message, type) {
    const alert = $(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('.content').prepend(alert);
    
    // Auto-dismiss après 5 secondes
    setTimeout(function() {
        alert.alert('close');
    }, 5000);
}

// Fonctions utilitaires
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}