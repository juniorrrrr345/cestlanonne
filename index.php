<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header('Location: ../login.html');
    exit();
}
$admin_name = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
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

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
    
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>Admin</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <!-- Admin icon instead of image -->
                        <span class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.5rem; color: #fff;">
                            <i class="fa fa-user-shield"></i>
                        </span>
                    </div>
                    <div class="ms-3">
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="index.html" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
              
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Search">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <!-- Admin icon instead of image -->
                            <span class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center me-lg-2" style="width: 40px; height: 40px; font-size: 1.5rem; color: #fff;">
                                <i class="fa fa-user-shield"></i>
                            </span>
                            <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($admin_name); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    
                            <a href="logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->






            <!-- Product Management Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Products</h6>
                        <button class="btn btn-success" id="addProductBtn">Add Product</button>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search by product name">
                        <div id="pagination" class=""></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0" id="productsTable">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Video/Photos</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Weight</th>
                                    <th scope="col">Country</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Product rows will be inserted here by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Product Management End -->

            <!-- Add/Edit Product Modal -->
            <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="productForm" enctype="multipart/form-data">
                    <div class="modal-header">
                      <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="id" id="productId">
                      <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="product_name" required>
                      </div>
                      <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                      </div>
                      <div class="mb-3">
                        <label for="media" class="form-label">Video/Photo</label>
                        <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                        <div id="mediaPreview" class="mt-2"></div>
                      </div>
                      <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" required>
                      </div>
                      <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" name="country" required>
                      </div>
                      <div class="mb-3">
    <label for="weight" class="form-label">Weight</label>
    <input type="text" class="form-control" id="weight" name="weight" required>
</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save</button>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete this product?
                    <input type="hidden" id="deleteProductId">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script>
    $(document).ready(function() {
        let currentPage = 1;
        let currentSearch = '';
        const limit = 15;

        function fetchProducts(page = 1, search = '') {
            $.get('products_api.php', { page, limit, search }, function(data) {
                renderTable(data.products);
                renderPagination(data.total, data.page, data.limit);
            }, 'json');
        }

        function renderTable(products) {
            const tbody = $('#productsTable tbody');
            tbody.empty();
            if (!products.length) {
                tbody.append('<tr><td colspan="7">No products found.</td></tr>');
                return;
            }
            products.forEach(product => {
                let mediaHtml = '';
                if (product.media) {
                    if (product.media.match(/\.(jpg|jpeg|png|gif)$/i)) {
                        mediaHtml = `<img src="${product.media}" style="max-width:60px;max-height:60px;">`;
                    } else if (product.media.match(/\.(mp4|webm|ogg)$/i)) {
                        mediaHtml = `<video src="${product.media}" style="max-width:60px;max-height:60px;" controls></video>`;
                    } else {
                        mediaHtml = `<a href="${product.media}" target="_blank">View</a>`;
                    }
                }
                tbody.append(`
                    <tr>
                        <td>${product.product_name}</td>
                        <td>${product.price}</td>
                        <td>${mediaHtml}</td>
                        <td>${product.description}</td>
                        <td>${product.category}</td>
                        <td>${product.weight || ''}</td>
                        <td>${product.country}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editBtn" data-id="${product.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${product.id}">Delete</button>
                        </td>
                    </tr>
                `);
            });
        }

        function renderPagination(total, page, limit) {
            const totalPages = Math.ceil(total / limit);
            let html = '';
            if (totalPages <= 1) { $('#pagination').html(''); return; }
            for (let i = 1; i <= totalPages; i++) {
                html += `<button class="btn btn-sm ${i === page ? 'btn-primary' : 'btn-outline-primary'} mx-1 pageBtn" data-page="${i}">${i}</button>`;
            }
            $('#pagination').html(html);
        }

        // Pagination click
        $(document).on('click', '.pageBtn', function() {
            currentPage = parseInt($(this).data('page'));
            fetchProducts(currentPage, currentSearch);
        });

        // Search
        $('#searchInput').on('input', function() {
            currentSearch = $(this).val();
            currentPage = 1;
            fetchProducts(currentPage, currentSearch);
        });

        // Add Product button
        $('#addProductBtn').click(function() {
            $('#productForm')[0].reset();
            $('#productId').val('');
            $('#mediaPreview').html('');
            $('#productModalLabel').text('Add Product');
            var modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        });

        // Edit Product button
        $(document).on('click', '.editBtn', function() {
            const id = $(this).data('id');
            $.get('products_api.php', { id }, function(data) {
                const product = data.products && data.products.length ? data.products[0] : null;
                if (product) {
                    $('#productId').val(product.id);
                    $('#productName').val(product.product_name);
                    $('#price').val(product.price);
                    $('#description').val(product.description);
                    $('#category').val(product.category);
                    $('#weight').val(product.weight || '');
                    $('#country').val(product.country);
                    if (product.media) {
                        let preview = '';
                        if (product.media.match(/\.(jpg|jpeg|png|gif)$/i)) {
                            preview = `<img src="${product.media}" style="max-width:100px;max-height:100px;">`;
                        } else if (product.media.match(/\.(mp4|webm|ogg)$/i)) {
                            preview = `<video src="${product.media}" style="max-width:100px;max-height:100px;" controls></video>`;
                        } else {
                            preview = `<a href="${product.media}" target="_blank">View</a>`;
                        }
                        $('#mediaPreview').html(preview);
                    } else {
                        $('#mediaPreview').html('');
                    }
                    $('#productModalLabel').text('Edit Product');
                    var modal = new bootstrap.Modal(document.getElementById('productModal'));
                    modal.show();
                } else {
                    alert('Product not found.');
                }
            }, 'json').fail(function() {
                alert('Failed to fetch product data.');
            });
        });

        // Delete Product button
        $(document).on('click', '.deleteBtn', function() {
            const id = $(this).data('id');
            $('#deleteProductId').val(id);
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });

        // Confirm delete
        $('#confirmDeleteBtn').click(function() {
            const id = $('#deleteProductId').val();
            $.ajax({
                url: 'products_api.php',
                type: 'DELETE',
                data: { id },
                success: function(res) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    if (modal) modal.hide();
                    fetchProducts(currentPage, currentSearch);
                }
            });
        });

        // Add/Edit Product form submit
        $('#productForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: 'products_api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
                    if (modal) modal.hide();
                    fetchProducts(1, currentSearch);
                },
                error: function(xhr) {
                    alert('Failed to save product: ' + (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Unknown error'));
                }
            });
        });

        // Media preview on file select
        $('#media').on('change', function() {
            const file = this.files[0];
            if (!file) return $('#mediaPreview').html('');
            const url = URL.createObjectURL(file);
            let preview = '';
            if (file.type.startsWith('image/')) {
                preview = `<img src="${url}" style="max-width:100px;max-height:100px;">`;
            } else if (file.type.startsWith('video/')) {
                preview = `<video src="${url}" style="max-width:100px;max-height:100px;" controls></video>`;
            }
            $('#mediaPreview').html(preview);
        });

        // Initial fetch
        fetchProducts();
    });
    </script>
</body>

</html>