// Panel Admin JavaScript pour Ma Boutique
$(document).ready(function() {
    let currentPage = 1;
    let productsPerPage = 10;
    let allProducts = [];
    let filteredProducts = [];

    // Initialisation
    init();

    function init() {
        loadDashboardStats();
        loadProducts();
        loadCategories();
        setupEventListeners();
        setupSearchFunctionality();
    }

    // Chargement des statistiques du dashboard
    function loadDashboardStats() {
        // Simulation des statistiques (à remplacer par des appels API réels)
        $('#totalProducts').text('0');
        $('#totalOrders').text('0');
        $('#totalCustomers').text('0');
        $('#totalRevenue').text('0€');

        // Appel API pour les vraies statistiques
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
                }
            },
            error: function() {
                console.log('Erreur lors du chargement des statistiques');
            }
        });
    }

    // Chargement des produits
    function loadProducts() {
        $.ajax({
            url: 'products_api.php',
            method: 'GET',
            data: { action: 'get_all' },
            success: function(response) {
                if (response.success) {
                    allProducts = response.products || [];
                    filteredProducts = [...allProducts];
                    displayProducts();
                    updatePagination();
                } else {
                    showAlert('Erreur lors du chargement des produits', 'danger');
                }
            },
            error: function() {
                showAlert('Erreur de connexion au serveur', 'danger');
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
                                <h6 class="mb-0">${product.product_name}</h6>
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
                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${product.description}">
                            ${product.description}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-info">${product.category_name || 'Non catégorisé'}</span>
                    </td>
                    <td>${product.country || 'N/A'}</td>
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
                            <option value="${category.id}">${category.name}</option>
                        `);
                    });
                }
            },
            error: function() {
                console.log('Erreur lors du chargement des catégories');
            }
        });
    }

    // Configuration des événements
    function setupEventListeners() {
        // Boutons d'ajout de produit
        $('#addProductBtn, #addProductBtn2').click(function() {
            resetProductForm();
            $('#productModalLabel').text('Ajouter un Produit');
            $('#productModal').modal('show');
        });

        // Soumission du formulaire produit
        $('#productForm').submit(function(e) {
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
        $('#confirmDeleteBtn').click(function() {
            const productId = $('#deleteProductId').val();
            deleteProduct(productId);
        });

        // Pagination
        $(document).on('click', '.pagination .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page && page > 0) {
                currentPage = page;
                displayProducts();
                updatePagination();
            }
        });

        // Navigation sidebar
        $('#ordersLink').click(function(e) {
            e.preventDefault();
            showAlert('Fonctionnalité Commandes à venir', 'info');
        });

        $('#customersLink').click(function(e) {
            e.preventDefault();
            showAlert('Fonctionnalité Clients à venir', 'info');
        });

        $('#analyticsLink').click(function(e) {
            e.preventDefault();
            showAlert('Fonctionnalité Analytics à venir', 'info');
        });

        $('#settingsLink').click(function(e) {
            e.preventDefault();
            showAlert('Fonctionnalité Paramètres à venir', 'info');
        });

        // Capture de média
        setupMediaCapture();
    }

    // Configuration de la recherche
    function setupSearchFunctionality() {
        let searchTimeout;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = $(this).val().toLowerCase();
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
                product.product_name.toLowerCase().includes(searchTerm) ||
                product.description.toLowerCase().includes(searchTerm) ||
                (product.category_name && product.category_name.toLowerCase().includes(searchTerm))
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
                    showAlert('Produit sauvegardé avec succès', 'success');
                    $('#productModal').modal('hide');
                    loadProducts();
                    loadDashboardStats();
                } else {
                    showAlert(response.message || 'Erreur lors de la sauvegarde', 'danger');
                }
            },
            error: function() {
                showAlert('Erreur de connexion au serveur', 'danger');
            }
        });
    }

    // Édition d'un produit
    function editProduct(productId) {
        $.ajax({
            url: 'products_api.php',
            method: 'GET',
            data: { action: 'get', id: productId },
            success: function(response) {
                if (response.success) {
                    const product = response.product;
                    $('#productId').val(product.id);
                    $('#productName').val(product.product_name);
                    $('#price').val(product.price);
                    $('#description').val(product.description);
                    $('#category').val(product.category_id);
                    $('#country').val(product.country);
                    $('#weight').val(product.weight);
                    $('#stock').val(product.stock || 0);
                    
                    if (product.media) {
                        $('#mediaPreview').html(`
                            <img src="${product.media}" alt="Preview" style="max-width: 100%; max-height: 200px;" class="rounded">
                        `);
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
            method: 'POST',
            data: { action: 'delete', id: productId },
            success: function(response) {
                if (response.success) {
                    showAlert('Produit supprimé avec succès', 'success');
                    $('#deleteModal').modal('hide');
                    loadProducts();
                    loadDashboardStats();
                } else {
                    showAlert(response.message || 'Erreur lors de la suppression', 'danger');
                }
            },
            error: function() {
                showAlert('Erreur de connexion au serveur', 'danger');
            }
        });
    }

    // Réinitialisation du formulaire produit
    function resetProductForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#mediaPreview').empty();
        $('#capturePreview').empty();
        $('#captureVideo').addClass('d-none');
        $('#captureCanvas').addClass('d-none');
        $('#captureControls').addClass('d-none');
    }

    // Configuration de la capture de média
    function setupMediaCapture() {
        let stream = null;

        // Capture vidéo
        $('#captureVideoBtn').click(function() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    const video = $('#captureVideo')[0];
                    video.srcObject = stream;
                    video.classList.remove('d-none');
                    $('#captureControls').removeClass('d-none');
                })
                .catch(function(err) {
                    showAlert('Erreur d\'accès à la caméra', 'danger');
                });
        });

        // Capture photo
        $('#capturePhotoBtn').click(function() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    const video = $('#captureVideo')[0];
                    video.srcObject = stream;
                    video.classList.remove('d-none');
                    $('#captureControls').removeClass('d-none');
                })
                .catch(function(err) {
                    showAlert('Erreur d\'accès à la caméra', 'danger');
                });
        });

        // Upload de fichier
        $('#uploadFileBtn').click(function() {
            $('#media').click();
        });

        $('#media').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#mediaPreview').html(`
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px;" class="rounded">
                    `);
                };
                reader.readAsDataURL(file);
            }
        });

        // Sauvegarde de la capture
        $('#saveCaptureBtn').click(function() {
            const canvas = $('#captureCanvas')[0];
            const video = $('#captureVideo')[0];
            const context = canvas.getContext('2d');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);
            
            canvas.toBlob(function(blob) {
                const file = new File([blob], 'capture.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                $('#media')[0].files = dt.files;
                
                $('#capturePreview').html(`
                    <img src="${canvas.toDataURL()}" alt="Capture" style="max-width: 100%; max-height: 200px;" class="rounded">
                `);
                
                stopCapture();
            }, 'image/jpeg');
        });

        // Annulation de la capture
        $('#cancelCaptureBtn').click(function() {
            stopCapture();
        });

        function stopCapture() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            $('#captureVideo').addClass('d-none');
            $('#captureCanvas').addClass('d-none');
            $('#captureControls').addClass('d-none');
        }
    }

    // Affichage d'alertes
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Supprimer les anciennes alertes
        $('.alert').remove();
        
        // Ajouter la nouvelle alerte
        $('.container-fluid').first().prepend(alertHtml);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Mise à jour automatique des statistiques toutes les 30 secondes
    setInterval(loadDashboardStats, 30000);
});