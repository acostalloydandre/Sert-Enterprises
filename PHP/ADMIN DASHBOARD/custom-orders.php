<?php
require_once __DIR__ . '/custom-orders-data.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Custom Orders & Services</title>

  <!-- Core Dashboard CSS & Orders Stylesheet -->
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-custom-orders.css">
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
          <h1>Custom Orders & Services</h1>
          <p class="welcome">Manage bespoke fabrication requests, signage, and material quotes.</p>
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

      <!-- CATEGORY OVAL PILLS BAR -->
      <nav class="category-pills-bar" aria-label="Custom order categories">
        <?php foreach ($categories as $cat): ?>
          <?php
          $isActiveCat = ($selectedCategory === $cat);
          $queryUrl = "custom-orders.php?category=" . urlencode($cat);
          ?>
          <a href="<?php echo $queryUrl; ?>" class="category-pill <?php echo $isActiveCat ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($cat); ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <!-- MASTER-DETAIL SIDE-BY-SIDE LAYOUT -->
      <div class="orders-master-layout">

        <!-- LEFT PANEL: Directory (360px) -->
        <article class="panel directory-panel">
          <div class="directory-header">
            <p class="directory-eyebrow">DIRECTORY (<?php echo strtoupper(htmlspecialchars($selectedCategory)); ?>)</p>
            <h3 class="directory-title">Clients / Requesters</h3>
          </div>
          <p class="directory-subtitle">Select a client to view project specifications.</p>

          <div class="client-list-wrap">
            <?php if (empty($clients)): ?>
              <p style="color: var(--muted); font-size: 12px; padding: 12px 0;">No client requests found in this category.
              </p>
            <?php else: ?>
              <?php foreach ($clients as $c): ?>
                <?php $isActiveClient = ($selectedClientName === $c['customer_name']); ?>
                <a href="custom-orders.php?category=<?php echo urlencode($selectedCategory); ?>&client=<?php echo urlencode($c['customer_name']); ?>"
                  class="client-item-link <?php echo $isActiveClient ? 'active' : ''; ?>">
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

        <!-- RIGHT PANEL: Customer Detail Workspace (1fr) -->
        <article class="panel content-detail-panel">
          <?php if (!$selectedClientName || empty($selectedClientCustomOrders)): ?>
            <div class="empty-orders-state">
              <h3>No Custom Orders Found</h3>
              <p>There are no current custom orders yet.</p>
            </div>
          <?php else: ?>
            <div class="client-record-header">
              <p class="directory-eyebrow">CLIENT SPECIFICATION RECORD</p>
              <h2><?php echo htmlspecialchars($selectedClientName); ?></h2>
            </div>

            <!-- Active Specification Highlight Box -->
            <div class="active-order-section">
              <h4 class="section-label-heading">Latest Custom Design Spec</h4>
              <?php $latestReq = $selectedClientCustomOrders[0]; ?>
              <div class="active-order-card">
                <div class="active-order-details">
                  <strong>Request ID: <?php echo htmlspecialchars($latestReq['custom_order_id']); ?> ·
                    [<?php echo htmlspecialchars($latestReq['category']); ?>]</strong>
                  <p>
                    <?php echo htmlspecialchars($latestReq['specifications'] ?? 'Custom dimensions & branding requirements applied.'); ?>
                    · Date: <?php echo htmlspecialchars($latestReq['request_date']); ?></p>
                </div>
                <div class="active-order-pricing">
                  <span
                    class="amount-text"><?php echo htmlspecialchars($latestReq['estimated_quote'] ?? 'Pending Quote'); ?></span>
                  <span class="status-pill <?php echo htmlspecialchars($latestReq['status_class'] ?? 'tag-purple'); ?>"
                    style="margin-top: 4px; display: inline-block;"><?php echo htmlspecialchars($latestReq['status']); ?></span>
                </div>
              </div>
            </div>

            <!-- Full Request & Quote History Table -->
            <div>
              <h4 class="section-label-heading">Full Custom History & Quotations</h4>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>CATEGORY</th>
                      <th>SPECIFICATIONS / NOTES</th>
                      <th>DATE</th>
                      <th>EST. QUOTE</th>
                      <th>STATUS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($selectedClientCustomOrders as $req): ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($req['custom_order_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($req['category']); ?></td>
                        <td><?php echo htmlspecialchars($req['specifications']); ?></td>
                        <td><?php echo htmlspecialchars($req['request_date']); ?></td>
                        <td><?php echo htmlspecialchars($req['estimated_quote'] ?? 'TBD'); ?></td>
                        <td><span
                            class="status-pill <?php echo htmlspecialchars($req['status_class'] ?? ''); ?>"><?php echo htmlspecialchars($req['status']); ?></span>
                        </td>
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