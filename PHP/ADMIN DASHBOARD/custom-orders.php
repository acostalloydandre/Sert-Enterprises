<?php
require_once __DIR__ . '/custom-orders-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Custom Orders & Services</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-custom-orders.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
</head>
<body>
  <div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
      
      <!-- TOP HEADER -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / WORKSPACE</p>
          <h1>Custom Orders & Services</h1>
          <p class="welcome">Manage active fabrication requests, site visits, and material quotes.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search custom requests..." aria-label="Search">
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

      <!-- TOP BAR: STATUS PILLS (LEFT) & CATEGORY DROPDOWN (RIGHT) -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; margin-bottom: 20px; gap: 16px; flex-wrap: wrap;">
        
        <!-- STATUS PILLS BAR -->
        <nav class="category-pills-bar" style="margin: 0; flex-grow: 1;">
          <?php 
          $statusFilters = ['All', 'Pending', 'Site Visit Scheduled', 'For Quotation', 'Quotation Sent'];
          $displayLabels = [
              'All' => 'All Active Requests',
              'Pending' => 'Pending Approval',
              'Site Visit Scheduled' => 'Pending Site Visit',
              'For Quotation' => 'Pending Quotation',
              'Quotation Sent' => 'Pending Downpayment'
          ];
          
          foreach ($statusFilters as $status): 
            $isActiveStatus = ($selectedStatus === $status);
            $label = $displayLabels[$status] ?? $status;
          ?>
            <a href="custom-orders.php?status=<?php echo urlencode($status); ?>&category=<?php echo urlencode($selectedCategory); ?>" class="category-pill <?php echo $isActiveStatus ? 'active' : ''; ?>">
              <?php echo htmlspecialchars($label); ?>
            </a>
          <?php endforeach; ?>
        </nav>

        <!-- CATEGORY DROPDOWN ON THE RIGHT -->
        <form method="GET" action="custom-orders.php" style="display: flex; align-items: center; min-width: 220px;">
          <input type="hidden" name="status" value="<?php echo htmlspecialchars($selectedStatus); ?>">
          <select name="category" onchange="this.form.submit()" class="workflow-input" style="height: 40px; font-weight: 600; cursor: pointer; border-radius: 9999px; padding: 0 16px; background: #fff;">
            <option value="All">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <?php if ($cat === 'All') continue; ?>
              <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($selectedCategory === $cat) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>

      </div>

      <!-- MASTER-DETAIL SIDE-BY-SIDE LAYOUT -->
      <div class="orders-master-layout">
        
        <!-- LEFT PANEL: Directory -->
        <article class="panel custom-directory-panel">
          <div class="custom-directory-header">
            <p class="custom-directory-eyebrow">STATUS: <?php echo strtoupper(htmlspecialchars($selectedStatus)); ?></p>
            <h3 class="custom-directory-title">Active Clients</h3>
          </div>
          <p class="custom-directory-subtitle">Select a client to view specifications.</p>

          <div class="custom-client-list-wrap">
            <?php if (empty($clients)): ?>
              <p style="color: var(--muted); font-size: 12px; padding: 12px 0;">No active requests found.</p>
            <?php else: ?>
              <?php foreach ($clients as $c): ?>
                <?php $isActiveClient = ($selectedClientName === $c['customer_name']); ?>
                <a href="custom-orders.php?status=<?php echo urlencode($selectedStatus); ?>&category=<?php echo urlencode($selectedCategory); ?>&client=<?php echo urlencode($c['customer_name']); ?>" class="custom-client-link <?php echo $isActiveClient ? 'active' : ''; ?>">
                  <div>
                    <strong><?php echo htmlspecialchars($c['customer_name']); ?></strong>
                    <small><?php echo $c['total_requests']; ?> request(s)</small>
                  </div>
                  <span>→</span>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </article>

        <!-- RIGHT PANEL: Customer Detail Workspace -->
        <article class="panel custom-detail-panel">
          <?php if (!empty($successMessage)): ?>
            <div class="alert-banner-success"><?php echo htmlspecialchars($successMessage); ?></div>
          <?php endif; ?>

          <?php if (!$selectedClientName || empty($selectedClientCustomOrders)): ?>
            <div class="custom-empty-state">
              <h3>No Active Custom Orders Found</h3>
              <p>There are no active orders matching your filter.</p>
            </div>
          <?php else: ?>
            <div class="custom-record-header">
              <p class="custom-directory-eyebrow">CLIENT SPECIFICATION RECORD</p>
              <h2><?php echo htmlspecialchars($selectedClientName); ?></h2>
            </div>

            <div class="custom-active-spec-card">
              <?php $latestReq = $selectedClientCustomOrders[0]; ?>
              <div class="custom-active-spec-details">
                <strong>Request ID: <?php echo htmlspecialchars($latestReq['custom_order_id']); ?> · [<?php echo htmlspecialchars($latestReq['category']); ?>]</strong>
                <p><?php echo htmlspecialchars($latestReq['specifications']); ?> · Date: <?php echo htmlspecialchars($latestReq['request_date']); ?></p>
              </div>
              <div class="custom-active-spec-pricing">
                <span class="amount-text"><?php echo htmlspecialchars($latestReq['estimated_quote'] ?? 'Pending Quote'); ?></span>
                <span class="status-pill tag-purple" style="margin-top: 4px; display: inline-block;"><?php echo htmlspecialchars($latestReq['status']); ?></span>
              </div>
            </div>

            <!-- WORKFLOW ACTION PANEL -->
            <div class="workflow-action-box">
              <h4 class="workflow-action-heading">Workflow Action Panel</h4>
              <?php $currentStatus = strtolower(trim($latestReq['status'])); ?>

              <?php if ($currentStatus === 'pending' || $currentStatus === ''): ?>
                <form action="custom-orders.php?status=<?php echo urlencode($selectedStatus); ?>&category=<?php echo urlencode($selectedCategory); ?>&client=<?php echo urlencode($selectedClientName); ?>" method="POST">
                  <input type="hidden" name="action" value="approve_and_schedule">
                  <input type="hidden" name="custom_order_id" value="<?php echo htmlspecialchars($latestReq['custom_order_id']); ?>">
                  <div class="workflow-form-group">
                    <label>SET SITE VISIT SCHEDULE</label>
                    <input type="datetime-local" name="site_visit_date" required class="workflow-input">
                  </div>
                  <div class="workflow-btn-row">
                    <button type="submit" class="workflow-btn-primary">Approve & Schedule Site Visit</button>
                    <button type="submit" name="action" value="reject_order" class="workflow-btn-danger" onclick="return confirm('Reject this order?');">Reject Order</button>
                  </div>
                </form>

              <?php elseif (strpos($currentStatus, 'site visit scheduled') !== false): ?>
                <p style="font-size: 12px; margin-bottom: 10px;"><strong>Site Visit Date:</strong> <?php echo htmlspecialchars($latestReq['site_visit_date']); ?></p>
                <form action="custom-orders.php?status=<?php echo urlencode($selectedStatus); ?>&category=<?php echo urlencode($selectedCategory); ?>&client=<?php echo urlencode($selectedClientName); ?>" method="POST">
                  <input type="hidden" name="action" value="mark_visit_successful">
                  <input type="hidden" name="custom_order_id" value="<?php echo htmlspecialchars($latestReq['custom_order_id']); ?>">
                  <button type="submit" class="workflow-btn-success">Mark Site Visit Successful (Pending Quotation)</button>
                </form>

              <?php elseif (strpos($currentStatus, 'for quotation') !== false): ?>
                <form action="custom-orders.php?status=<?php echo urlencode($selectedStatus); ?>&category=<?php echo urlencode($selectedCategory); ?>&client=<?php echo urlencode($selectedClientName); ?>" method="POST">
                  <input type="hidden" name="action" value="issue_quotation">
                  <input type="hidden" name="custom_order_id" value="<?php echo htmlspecialchars($latestReq['custom_order_id']); ?>">
                  <div class="workflow-form-group">
                    <label>QUOTATION AMOUNT (PHP)</label>
                    <input type="text" name="estimated_quote" placeholder="e.g. ₱25,000.00" required class="workflow-input">
                  </div>
                  <button type="submit" class="workflow-btn-warning">Issue Quotation & Await Downpayment</button>
                </form>

              <?php elseif (strpos($currentStatus, 'quotation sent') !== false): ?>
                <p style="font-size: 12px; margin-bottom: 10px; color: var(--muted, #64748b);">Quotation sent. Awaiting 50% downpayment confirmation.</p>
                <form action="custom-orders.php?status=<?php echo urlencode($selectedStatus); ?>&category=<?php echo urlencode($selectedCategory); ?>&client=<?php echo urlencode($selectedClientName); ?>" method="POST">
                  <input type="hidden" name="action" value="confirm_downpayment_and_graduate">
                  <input type="hidden" name="custom_order_id" value="<?php echo htmlspecialchars($latestReq['custom_order_id']); ?>">
                  <button type="submit" class="workflow-btn-info">Confirm 50% Downpayment & Graduate to Projects</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </article>
      </div>
    </main>
  </div>
  <script src="../../JS/admin-dashboard.js"></script>
</body>
</html>