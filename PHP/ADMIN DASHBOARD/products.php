<?php
require_once __DIR__ . '/products-data.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SERT Enterprise | Seller Central - Product Manager</title>

    <!-- External Stylesheets -->
    <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
    <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
    <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
    <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
    <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-products.css">
</head>

<body>

    <div class="dashboard-container">

        <!-- SIDEBAR COMPONENT -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- MAIN CONTENT AREA -->
        <main class="main-content">

            <!-- TOP HEADER -->
            <header class="topbar">
                <div class="page-heading">
                    <p class="eyebrow">SERT ENTERPRISE / SELLER CENTRAL</p>
                    <h1>Product Catalog & Specifications</h1>
                    <p class="welcome">Manage sign specs, dimensions, base pricing, and service catalog media.</p>
                </div>
                <div class="topbar-actions">
                    <label class="search-box">
                        <span>⌕</span>
                        <input type="search" placeholder="Search catalog..." aria-label="Search">
                        <kbd>⌘ K</kbd>
                    </label>
                    <button class="icon-button notification-button" aria-label="Notifications">♧<i></i></button>
                    <button class="profile-button">
                        <span class="avatar avatar-admin">LA</span>
                        <span><strong>Lloyd Acosta</strong><small>Admin</small></span>
                        <span class="chevron">⌄</span>
                    </button>
                </div>
            </header>

            <!-- CATEGORY FILTER & ADD NEW PRODUCT BUTTON -->
            <div class="seller-header-actions">
                <form method="GET" action="products.php" style="display: flex; gap: 10px; align-items: center;">
                    <select name="category" class="seller-select" onchange="this.form.submit()">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($selectedCategory === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <!-- Triggers Modal Popup -->
                <button type="button" class="btn-primary-action" onclick="openProductModal()">
                    <span>+</span> List New Product / Service
                </button>
            </div>

            <!-- NOTIFICATIONS -->
            <?php if (!empty($successMessage)): ?>
                <div class="alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <!-- SELLER CENTRAL SPLIT LAYOUT -->
            <div class="products-layout">

                <!-- LEFT PANEL: Product Inventory Directory (360px) -->
                <article class="panel directory-panel">
                    <div class="directory-header">
                        <p class="directory-eyebrow">CATALOG DIRECTORY</p>
                        <h3 class="directory-title">Active Listings (<?php echo count($productsList); ?>)</h3>
                    </div>
                    <p class="directory-subtitle">Select a product to view or edit details.</p>

                    <div class="client-list-wrap">
                        <?php if (empty($productsList)): ?>
                            <p style="color: var(--muted); font-size: 12px; padding: 12px 0;">No products listed under this
                                category yet.</p>
                        <?php else: ?>
                            <?php foreach ($productsList as $prod): ?>
                                <?php $isActiveProd = ((string) $selectedProductId === (string) $prod['product_id']); ?>
                                <a href="products.php?category=<?php echo urlencode($selectedCategory); ?>&product_id=<?php echo $prod['product_id']; ?>"
                                    class="client-item-link <?php echo $isActiveProd ? 'active' : ''; ?>">
                                    <div>
                                        <strong><?php echo htmlspecialchars($prod['product_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($prod['category']); ?></small>
                                    </div>
                                    <span>₱<?php echo number_format((float) ($prod['price'] ?? 0), 2); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </article>

                <!-- RIGHT PANEL: Details View (1fr) -->
                <article class="panel content-detail-panel">
                    <?php if (!$productDetails || $selectedProductId === 'new'): ?>
                        <div class="empty-orders-state">
                            <h3>Catalog Overview</h3>
                            <p>Select an item from the directory on the left to inspect specifications, or click <strong>"+
                                    List New Product"</strong> above to add a new listing.</p>
                        </div>
                    <?php else: ?>
                        <div class="client-record-header">
                            <p class="directory-eyebrow">PRODUCT SPECIFICATION RECORD</p>
                            <h2><?php echo htmlspecialchars($productDetails['product_name']); ?></h2>
                        </div>

                        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 24px;">
                            <?php if (!empty($productDetails['image_path'])): ?>
                                <img src="../../<?php echo htmlspecialchars($productDetails['image_path']); ?>" alt="Product"
                                    style="width: 140px; height: 140px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <?php endif; ?>
                            <div>
                                <span
                                    class="status-pill tag-green"><?php echo htmlspecialchars($productDetails['status']); ?></span>
                                <h3 style="font-size: 18px; margin-top: 8px; color: var(--text-main);">
                                    <?php echo htmlspecialchars($productDetails['product_name']); ?></h3>
                                <p style="font-size: 14px; font-weight: bold; color: var(--text-main); margin-top: 4px;">
                                    ₱<?php echo number_format((float) $productDetails['price'], 2); ?></p>
                                <p style="font-size: 12px; color: var(--muted); margin-top: 6px;">Category:
                                    <?php echo htmlspecialchars($productDetails['category']); ?></p>
                            </div>
                        </div>

                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ATTRIBUTE</th>
                                        <th>SPECIFICATION DETAILS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Materials to be Used</strong></td>
                                        <td><?php echo htmlspecialchars($productDetails['materials'] ?: 'No specific materials listed.'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dimensions</strong></td>
                                        <td><?php echo htmlspecialchars($productDetails['dimensions'] ?: 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Height / Thickness</strong></td>
                                        <td><?php echo htmlspecialchars($productDetails['height'] ?: 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Description & Notes</strong></td>
                                        <td><?php echo htmlspecialchars($productDetails['description'] ?: 'No additional description provided.'); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </article>

            </div>

        </main>
    </div>

    <!-- LOAD MODAL COMPONENT -->
    <?php include __DIR__ . '/product-modal.php'; ?>

    <!-- EXTERNAL JS -->
    <script src="../../JS/admin-products.js"></script>
</body>

</html>