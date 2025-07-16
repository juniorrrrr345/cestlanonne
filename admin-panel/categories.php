<?php
require_once 'config.php';
$admin_name = validateAdminSession();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Gestion des Catégories - Panel Admin</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>Admin</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <span class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.5rem; color: #fff;">
                            <i class="fa fa-user-shield"></i>
                        </span>
                    </div>
                    <div class="ms-3">
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="categories.php" class="nav-item nav-link active"><i class="fa fa-tags me-2"></i>Catégories</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Rechercher...">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center me-lg-2" style="width: 40px; height: 40px; font-size: 1.5rem; color: #fff;">
                                <i class="fa fa-user-shield"></i>
                            </span>
                            <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($admin_name); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="logout.php" class="dropdown-item">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Categories Management Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Gestion des Catégories</h6>
                        <button class="btn btn-success" id="addCategoryBtn">
                            <i class="fa fa-plus me-2"></i>Ajouter Catégorie
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0" id="categoriesTable">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">Nom</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Catégorie Parent</th>
                                    <th scope="col">Produits</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Ordre</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Categories will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Categories Management End -->

            <!-- Add/Edit Category Modal -->
            <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="categoryForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="categoryModalLabel">Ajouter Catégorie</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="categoryId">
                                
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Nom de la catégorie *</label>
                                    <input type="text" class="form-control" id="categoryName" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categoryDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="parentCategory" class="form-label">Catégorie parente</label>
                                    <select class="form-control" id="parentCategory" name="parent_id">
                                        <option value="">Aucune (catégorie principale)</option>
                                        <!-- Parent categories will be loaded here -->
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sortOrder" class="form-label">Ordre d'affichage</label>
                                            <input type="number" class="form-control" id="sortOrder" name="sort_order" value="0" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                                <label class="form-check-label" for="isActive">
                                                    Catégorie active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCategoryModalLabel">Supprimer Catégorie</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Êtes-vous sûr de vouloir supprimer cette catégorie ?
                            <input type="hidden" id="deleteCategoryId">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteCategoryBtn">Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script>
    $(document).ready(function() {
        let categories = [];
        let parentCategories = [];

        function loadCategories() {
            $.get('categories_api.php', function(data) {
                categories = data.categories || [];
                parentCategories = categories.filter(cat => !cat.parent_id);
                renderCategoriesTable();
                updateParentCategoryOptions();
            }, 'json').fail(function() {
                alert('Erreur lors du chargement des catégories');
            });
        }

        function renderCategoriesTable() {
            const tbody = $('#categoriesTable tbody');
            tbody.empty();
            
            if (!categories.length) {
                tbody.append('<tr><td colspan="7" class="text-center">Aucune catégorie trouvée.</td></tr>');
                return;
            }
            
            categories.forEach(category => {
                const parentName = category.parent_id ? 
                    categories.find(c => c.id == category.parent_id)?.name || 'N/A' : 
                    'Catégorie principale';
                
                const statusBadge = category.is_active == 1 ? 
                    '<span class="badge bg-success">Active</span>' : 
                    '<span class="badge bg-secondary">Inactive</span>';
                
                tbody.append(`
                    <tr>
                        <td>${category.name}</td>
                        <td>${category.description || '-'}</td>
                        <td>${parentName}</td>
                        <td><span class="badge bg-info">${category.product_count || 0}</span></td>
                        <td>${statusBadge}</td>
                        <td>${category.sort_order || 0}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editCategoryBtn" data-id="${category.id}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteCategoryBtn" data-id="${category.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function updateParentCategoryOptions() {
            const select = $('#parentCategory');
            select.find('option:not(:first)').remove();
            
            parentCategories.forEach(category => {
                select.append(`<option value="${category.id}">${category.name}</option>`);
            });
        }

        // Add Category button
        $('#addCategoryBtn').click(function() {
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
            $('#categoryModalLabel').text('Ajouter Catégorie');
            $('#isActive').prop('checked', true);
            var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        });

        // Edit Category button
        $(document).on('click', '.editCategoryBtn', function() {
            const id = $(this).data('id');
            const category = categories.find(c => c.id == id);
            
            if (category) {
                $('#categoryId').val(category.id);
                $('#categoryName').val(category.name);
                $('#categoryDescription').val(category.description || '');
                $('#parentCategory').val(category.parent_id || '');
                $('#sortOrder').val(category.sort_order || 0);
                $('#isActive').prop('checked', category.is_active == 1);
                $('#categoryModalLabel').text('Modifier Catégorie');
                
                var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                modal.show();
            }
        });

        // Delete Category button
        $(document).on('click', '.deleteCategoryBtn', function() {
            const id = $(this).data('id');
            $('#deleteCategoryId').val(id);
            var modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
            modal.show();
        });

        // Confirm delete
        $('#confirmDeleteCategoryBtn').click(function() {
            const id = $('#deleteCategoryId').val();
            $.ajax({
                url: 'categories_api.php',
                type: 'DELETE',
                data: { id },
                success: function(res) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                    if (modal) modal.hide();
                    loadCategories();
                    alert('Catégorie supprimée avec succès');
                },
                error: function(xhr) {
                    const error = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Erreur lors de la suppression';
                    alert(error);
                }
            });
        });

        // Category form submit
        $('#categoryForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('is_active', $('#isActive').is(':checked') ? '1' : '0');
            
            $.ajax({
                url: 'categories_api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                    if (modal) modal.hide();
                    loadCategories();
                    alert(res.message || 'Catégorie sauvegardée avec succès');
                },
                error: function(xhr) {
                    const error = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Erreur lors de la sauvegarde';
                    alert(error);
                }
            });
        });

        // Initial load
        loadCategories();
    });
    </script>
</body>
</html>