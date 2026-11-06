<?php
require_once __DIR__ . '/request-project-data.php';

$clientFullName = $customerInfo['customer_name'] ?? 'Client Partner';
$nameParts = explode(' ', trim($clientFullName));
$initials = (count($nameParts) >= 2) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($clientFullName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Request a Project</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/CUSTOMER DASHBOARD/customer-portal.css">
</head>
<body>
  <div class="app-shell">
    
    <!-- Client Portal Sidebar -->
    <?php include __DIR__ . '/customer-sidebar.php'; ?>

    <main class="main-content">
      
      <!-- TOP HEADER BAR -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / CLIENT PORTAL</p>
          <h1>Request a New Project / Quotation</h1>
          <p class="welcome">Submit your exact specifications, sizing, and design references for evaluation.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search orders..." aria-label="Search">
            <kbd>⌘ K</kbd>
          </label>
          <button class="icon-button notification-button" aria-label="Notifications">♧<i></i></button>
          <button class="profile-button">
            <span class="avatar avatar-admin"><?php echo htmlspecialchars($initials); ?></span>
            <span><strong><?php echo htmlspecialchars($clientFullName); ?></strong><small>Client</small></span>
            <span class="chevron">⌄</span>
          </button>
        </div>
      </header>

      <?php if (!empty($successMessage)): ?>
        <div class="alert-success">
          <?php echo htmlspecialchars($successMessage); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMessage)): ?>
        <div class="alert-error">
          <?php echo htmlspecialchars($errorMessage); ?>
        </div>
      <?php endif; ?>

      <!-- PROJECT REQUEST FORM PANEL -->
      <article class="panel request-form-panel">
        <div class="panel-heading request-form-heading">
          <div>
            <p class="eyebrow">SPECIFICATION INTAKE FORM</p>
            <h2>Project Requirements & Desired Schedule</h2>
          </div>
        </div>

        <form action="request-project.php" method="POST" enctype="multipart/form-data">
          
          <div class="form-group">
            <label class="form-label">PROJECT / INQUIRY TITLE *</label>
            <input type="text" name="project_name" placeholder="e.g., Main Lobby Acrylic Logo & Reception Backdrop" required class="form-input">
          </div>

          <div class="form-row-grid">
            <div>
              <label class="form-label">SERVICE CATEGORY *</label>
              <select name="service_type" required class="form-select">
                <option value="" disabled selected>Select official service...</option>
                <option value="Large Format Printing">Large Format Printing</option>
                <option value="Panaflex Signage">Panaflex Signage</option>
                <option value="Acrylic Works">Acrylic Works</option>
                <option value="Metal Works">Metal Works</option>
                <option value="Wood Works">Wood Works</option>
                <option value="Window Blinds">Window Blinds</option>
                <option value="Carpets">Carpets</option>
                <option value="Office Furniture & Partitions">Office Furniture & Partitions</option>
              </select>
            </div>
            <div>
              <label class="form-label">TARGET / DESIRED COMPLETION DATE <small>(Admin can adjust upon approval)</small></label>
              <input type="date" name="target_date" class="form-input">
            </div>
          </div>

          <div class="form-row-grid">
            <div>
              <label class="form-label">ESTIMATED DIMENSIONS (Width x Height)</label>
              <input type="text" name="dimensions" placeholder="e.g., 8ft x 3ft or 120cm x 50cm" class="form-input">
            </div>
            <div>
              <label class="form-label">QUANTITY</label>
              <input type="number" name="quantity" value="1" min="1" class="form-input">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">DETAILED SPECIFICATIONS & NOTES</label>
            <textarea name="notes" placeholder="Describe specific materials, lighting choices (front-lit/back-lit), color pegs, or site installation conditions..." class="form-textarea"></textarea>
          </div>

          <div class="file-input-wrap">
            <label class="form-label">UPLOAD DESIGN ARTWORK OR REFERENCE FILES</label>
            <input type="file" name="attachment" class="file-input">
            <small class="file-help-text">Accepted formats: AI, PSD, PDF, PNG, JPG, ZIP (Max 25MB)</small>
          </div>

          <button type="submit" name="submit_request" class="submit-btn">Submit Project Request</button>
        </form>
      </article>

      <footer class="footer">© 2026 SERT Enterprise. Client Portal.</footer>
    </main>
  </div>
  <script src="../../JS/customer-dashboard.js"></script>
</body>
</html>