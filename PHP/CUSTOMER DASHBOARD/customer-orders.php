<?php
require_once __DIR__ . '/customer-orders-data.php';

$clientFullName = $customerInfo['customer_name'] ?? 'Client Partner';
$nameParts = explode(' ', trim($clientFullName));
$initials = (count($nameParts) >= 2) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($clientFullName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | My Orders</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
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
          <h1>My Orders & Inquiries</h1>
          <p class="welcome">Track the review, site visits, quotations, and downpayment status of your requests.</p>
        </div>
        <div class="topbar-actions">
          <a href="request-project.php" class="text-link" style="padding: 8px 14px; background: var(--green, #123c2d); color: #fff; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">+ Request New Project</a>

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

      <!-- STATUS FILTER PILLS (Matching Admin Categories) -->
      <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="customer-orders.php?status=all" class="status-pill-btn <?php echo ($currentFilter === 'all') ? 'active' : ''; ?>" style="padding: 8px 14px; background: <?php echo ($currentFilter === 'all') ? 'var(--green)' : '#fff'; ?>; color: <?php echo ($currentFilter === 'all') ? '#fff' : 'var(--ink)'; ?>; border: 1px solid var(--line); border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">All Orders</a>
        
        <a href="customer-orders.php?status=Pending Approval" class="status-pill-btn <?php echo ($currentFilter === 'Pending Approval') ? 'active' : ''; ?>" style="padding: 8px 14px; background: <?php echo ($currentFilter === 'Pending Approval') ? 'var(--green)' : '#fff'; ?>; color: <?php echo ($currentFilter === 'Pending Approval') ? '#fff' : 'var(--ink)'; ?>; border: 1px solid var(--line); border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">Pending Approval</a>
        
        <a href="customer-orders.php?status=Pending Site Visit" class="status-pill-btn <?php echo ($currentFilter === 'Pending Site Visit') ? 'active' : ''; ?>" style="padding: 8px 14px; background: <?php echo ($currentFilter === 'Pending Site Visit') ? 'var(--green)' : '#fff'; ?>; color: <?php echo ($currentFilter === 'Pending Site Visit') ? '#fff' : 'var(--ink)'; ?>; border: 1px solid var(--line); border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">Pending Site Visit</a>
        
        <a href="customer-orders.php?status=Pending Quotation" class="status-pill-btn <?php echo ($currentFilter === 'Pending Quotation') ? 'active' : ''; ?>" style="padding: 8px 14px; background: <?php echo ($currentFilter === 'Pending Quotation') ? 'var(--green)' : '#fff'; ?>; color: <?php echo ($currentFilter === 'Pending Quotation') ? '#fff' : 'var(--ink)'; ?>; border: 1px solid var(--line); border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">Pending Quotation</a>
        
        <a href="customer-orders.php?status=Pending Downpayment" class="status-pill-btn <?php echo ($currentFilter === 'Pending Downpayment') ? 'active' : ''; ?>" style="padding: 8px 14px; background: <?php echo ($currentFilter === 'Pending Downpayment') ? 'var(--green)' : '#fff'; ?>; color: <?php echo ($currentFilter === 'Pending Downpayment') ? '#fff' : 'var(--ink)'; ?>; border: 1px solid var(--line); border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">Pending Downpayment</a>
      </div>

      <!-- ORDERS TABLE PANEL -->
      <section class="panel orders-panel">
        <div class="panel-heading">
          <div>
            <p class="eyebrow">ORDER DIRECTORY</p>
            <h2>Filtered Orders (<?php echo count($customerOrders); ?>)</h2>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ORDER ID</th>
                <th>SERVICE / ORDER TYPE</th>
                <th>DATE PLACED</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($customerOrders)): ?>
                <tr>
                  <td colspan="6" style="text-align: center; color: var(--muted); padding: 24px;">No orders found under this status category.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($customerOrders as $ord): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($ord['order_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($ord['order_type'] ?? 'Custom Signage & Print'); ?></td>
                    <td><?php echo htmlspecialchars($ord['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($ord['amount'] ?? '₱0.00'); ?></td>
                    <td><span class="status-pill pending"><?php echo htmlspecialchars($ord['status']); ?></span></td>
                    <td><a href="#" class="row-arrow">→</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <footer class="footer" style="margin-top: 40px;">© 2026 SERT Enterprise. Client Portal.</footer>
    </main>
  </div>
  <script src="../../JS/customer-dashboard.js"></script>
</body>
</html>