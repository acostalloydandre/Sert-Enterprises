<?php
require_once __DIR__ . '/orders-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Premade Orders</title>

  <!-- Core Dashboard CSS & Orders CSS -->
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
</head>
<body>

  <div class="dashboard-container">
    
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
      
      <!-- TOP HEADER -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / WORKSPACE</p>
          <h1>Orders</h1>
          <p class="welcome">Welcome back! Here's what's happening with your business today.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search orders, projects..." aria-label="Search">
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

      <!-- ORDERS MASTER-DETAIL SIDE-BY-SIDE LAYOUT -->
      <div class="orders-master-layout">
        
        <!-- LEFT PANEL: Client Directory -->
        <article class="panel directory-panel">
          <div class="directory-header">
            <p class="directory-eyebrow">DIRECTORY</p>
            <h3 class="directory-title">Clients / Companies</h3>
          </div>
          <p class="directory-subtitle">Select a client to view their order records.</p>
          
          <div class="client-list-wrap">
            <?php if (empty($clients)): ?>
              <p style="color: var(--muted); font-size: 12px; padding: 12px 0;">No clients found.</p>
            <?php else: ?>
              <?php foreach ($clients as $c): ?>
                <?php $isActive = ($selectedClientName === $c['customer_name']); ?>
                <a href="orders.php?client=<?php echo urlencode($c['customer_name']); ?>" 
                   class="client-item-link <?php echo $isActive ? 'active' : ''; ?>">
                  <div>
                    <strong><?php echo htmlspecialchars($c['customer_name']); ?></strong>
                    <small><?php echo $c['total_orders']; ?> order(s)</small>
                  </div>
                  <span>→</span>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </article>

        <!-- RIGHT PANEL: Client Details & History -->
        <article class="panel content-detail-panel">
          <?php if (!$selectedClientName || empty($selectedClientOrders)): ?>
            <div class="empty-orders-state">
              <h3>No Orders Found</h3>
              <p>There are no current orders yet.</p>
            </div>
          <?php else: ?>
            <div class="client-record-header">
              <p class="directory-eyebrow">CLIENT RECORD (DEFAULT: LATEST ORDER)</p>
              <h2><?php echo htmlspecialchars($selectedClientName); ?></h2>
            </div>
            
            <!-- Active Order Highlight Box -->
            <div class="active-order-section">
              <h4 class="section-label-heading">Current Active Product Order</h4>
              <?php $latest = $selectedClientOrders[0]; ?>
              <div class="active-order-card">
                <div class="active-order-details">
                  <strong>Order ID: <?php echo htmlspecialchars($latest['order_id']); ?></strong>
                  <p>Item: <?php echo htmlspecialchars($latest['order_type']); ?> · Placed on: <?php echo htmlspecialchars($latest['order_date']); ?></p>
                </div>
                <div class="active-order-pricing">
                  <span class="amount-text"><?php echo htmlspecialchars($latest['amount']); ?></span>
                  <span class="status-pill <?php echo htmlspecialchars($latest['status_class'] ?? 'tag-orange'); ?>" style="margin-top: 4px; display: inline-block;"><?php echo htmlspecialchars($latest['status']); ?></span>
                </div>
              </div>
            </div>

            <!-- Full Order History Table -->
            <div>
              <h4 class="section-label-heading">Complete Order History</h4>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>ORDER ID</th>
                      <th>PRODUCT / TYPE</th>
                      <th>DATE</th>
                      <th>AMOUNT</th>
                      <th>STATUS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($selectedClientOrders as $order): ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['amount']); ?></td>
                        <td><span class="status-pill <?php echo htmlspecialchars($order['status_class'] ?? ''); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          <?php endif; ?>
        </article>

      </div>

    </main>
  </div>

</body>
</html>