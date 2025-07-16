<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header('Location: signin.html');
    exit();
}
$admin_name = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Panel Admin - Ma Boutique</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-shopping-cart me-2"></i>Ma Boutique</h3>
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
                    <a href="index.php" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Tableau de bord</a>
                    <a href="categories.php" class="nav-item nav-link"><i class="fa fa-tags me-2"></i>Catégories</a>
                    <a href="#" class="nav-item nav-link" id="ordersLink"><i class="fa fa-shopping-bag me-2"></i>Commandes</a>
                    <a href="#" class="nav-item nav-link" id="customersLink"><i class="fa fa-users me-2"></i>Clients</a>
                    <a href="#" class="nav-item nav-link" id="analyticsLink"><i class="fa fa-chart-line me-2"></i>Analytics</a>
                    <a href="#" class="nav-item nav-link" id="settingsLink"><i class="fa fa-cog me-2"></i>Paramètres</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-shopping-cart"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Rechercher produits, commandes...">
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

            <!-- Dashboard Start -->
            <div class="container-fluid pt-4 px-4">
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-shopping-cart fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Produits</p>
                                <h6 class="mb-0" id="totalProducts">0</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-shopping-bag fa-3x text-success"></i>
                            <div class="ms-3">
                                <p class="mb-2">Commandes</p>
                                <h6 class="mb-0" id="totalOrders">0</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-users fa-3x text-info"></i>
                            <div class="ms-3">
                                <p class="mb-2">Clients</p>
                                <h6 class="mb-0" id="totalCustomers">0</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-euro-sign fa-3x text-warning"></i>
                            <div class="ms-3">
                                <p class="mb-2">Chiffre d'affaires</p>
                                <h6 class="mb-0" id="totalRevenue">0€</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Quick Actions -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="bg-light rounded p-4">
                            <h6 class="mb-4">Activité Récente</h6>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <i class="fa fa-plus-circle text-success me-3"></i>
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Nouveau produit ajouté</h6>
                                        <small>Il y a 3 minutes</small>
                                    </div>
                                    <span>Produit "Nom du produit" a été ajouté au catalogue</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <i class="fa fa-shopping-bag text-primary me-3"></i>
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Nouvelle commande</h6>
                                        <small>Il y a 15 minutes</small>
                                    </div>
                                    <span>Commande #12345 reçue - 150€</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center py-3">
                                <i class="fa fa-user-plus text-info me-3"></i>
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Nouveau client</h6>
                                        <small>Il y a 1 heure</small>
                                    </div>
                                    <span>Client "Jean Dupont" s'est inscrit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-light rounded p-4">
                            <h6 class="mb-4">Actions Rapides</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" id="addProductBtn">
                                    <i class="fa fa-plus me-2"></i>Ajouter un produit
                                </button>
                                <button class="btn btn-success">
                                    <i class="fa fa-tags me-2"></i>Gérer les catégories
                                </button>
                                <button class="btn btn-info">
                                    <i class="fa fa-chart-bar me-2"></i>Voir les analytics
                                </button>
                                <button class="btn btn-warning">
                                    <i class="fa fa-cog me-2"></i>Paramètres boutique
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Management Section -->
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Gestion des Produits</h6>
                        <button class="btn btn-success" id="addProductBtn2">
                            <i class="fa fa-plus me-2"></i>Ajouter un produit
                        </button>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <input type="text" id="searchInput" class="form-control w-25" placeholder="Rechercher un produit...">
                        <div id="pagination" class=""></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0" id="productsTable">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">Produit</th>
                                    <th scope="col">Prix</th>
                                    <th scope="col">Média</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Catégorie</th>
                                    <th scope="col">Pays</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Product rows will be inserted here by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Dashboard End -->

            <!-- Add/Edit Product Modal -->
            <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <form id="productForm" enctype="multipart/form-data">
                    <div class="modal-header">
                      <h5 class="modal-title" id="productModalLabel">Ajouter un Produit</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="id" id="productId">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="productName" class="form-label">Nom du Produit</label>
                            <input type="text" class="form-control" id="productName" name="product_name" required>
                          </div>
                          <div class="mb-3">
                            <label for="price" class="form-label">Prix (€)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                          </div>
                          <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-control" id="category" name="category_id">
                              <option value="">Sélectionner une catégorie</option>
                              <!-- Categories will be loaded here -->
                            </select>
                          </div>
                          <div class="mb-3">
                            <label for="country" class="form-label">Pays d'origine</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="media" class="form-label">Photos/Vidéos</label>
                            <div class="btn-group w-100 mb-2" role="group">
                              <button type="button" class="btn btn-outline-primary" id="captureVideoBtn">
                                <i class="fa fa-video me-2"></i>Vidéo
                              </button>
                              <button type="button" class="btn btn-outline-primary" id="capturePhotoBtn">
                                <i class="fa fa-camera me-2"></i>Photo
                              </button>
                              <button type="button" class="btn btn-outline-secondary" id="uploadFileBtn">
                                <i class="fa fa-upload me-2"></i>Fichier
                              </button>
                            </div>
                            <input type="file" class="form-control d-none" id="media" name="media" accept="image/*,video/*">
                            <div id="mediaPreview" class="mt-2"></div>
                            <div id="capturePreview" class="mt-2"></div>
                            <video id="captureVideo" class="d-none" autoplay muted style="max-width: 100%; max-height: 200px;"></video>
                            <canvas id="captureCanvas" class="d-none"></canvas>
                            <div id="captureControls" class="mt-2 d-none">
                              <button type="button" class="btn btn-success btn-sm" id="saveCaptureBtn">
                                <i class="fa fa-save me-1"></i>Sauvegarder
                              </button>
                              <button type="button" class="btn btn-danger btn-sm" id="cancelCaptureBtn">
                                <i class="fa fa-times me-1"></i>Annuler
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="weight" class="form-label">Poids</label>
                            <input type="text" class="form-control" id="weight" name="weight" placeholder="ex: 500g">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="0">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                      <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-2"></i>Enregistrer
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Supprimer le Produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce produit ?
                    <input type="hidden" id="deleteProductId">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                      <i class="fa fa-trash me-2"></i>Supprimer
                    </button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin Panel JavaScript -->
    <script src="admin.js"></script>
</body>

</html>