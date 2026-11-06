<?php
require_once __DIR__ . '/customers-data.php';

$selectedCustomerId = $selectedCustomerId ?? $_GET['customer_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Customer Directory</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-projects.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-customers.css">
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
          <p class="eyebrow">SERT ENTERPRISE / CLIENT DIRECTORY</p>
          <h1>Customer & Design Accounts</h1>
          <p class="welcome">Manage verified client profiles and track active orders.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search customers..." aria-label="Search">
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

      <!-- FULL-WIDTH WORKSPACE AREA -->
      <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 24px;">
        
        <?php if ($selectedCustomerId && !empty($customerDetails)): ?>
          <!-- PROFILE DETAIL VIEW -->
          <article class="panel" style="padding: 24px;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px;">
              <div>
                <p class="eyebrow">CUSTOMER PROFILE RECORD</p>
                <h2><?php echo htmlspecialchars($customerDetails['customer_name']); ?></h2>
              </div>
              <a href="customers.php" style="background: #e2e8f0; color: #1e293b; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700;">← Back to All Accounts</a>
            </div>

            <!-- CUSTOMER DETAILS SUMMARY TABLE -->
            <div style="margin-bottom: 24px;">
              <h4 class="section-label-heading">Contact & Business Information</h4>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>ATTRIBUTE</th>
                      <th>DETAILS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><strong>Company / Organization</strong></td>
                      <td><?php echo htmlspecialchars($customerDetails['company'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                      <td><strong>Email Address</strong></td>
                      <td><?php echo htmlspecialchars($customerDetails['email'] ?? 'Not provided'); ?></td>
                    </tr>
                    <tr>
                      <td><strong>Phone Number</strong></td>
                      <td><?php echo htmlspecialchars($customerDetails['phone'] ?? 'Not provided'); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- ASSOCIATED ORDERS & PROJECTS -->
            <div>
              <h4 class="section-label-heading">Active Projects Pipeline (<?php echo count($customerProjects); ?>)</h4>
              <div class="table-wrap">
                <?php if (empty($customerProjects)): ?>
                  <p style="padding: 16px; font-size: 13px; color: var(--muted);">No active projects or orders recorded for this client yet.</p>
                <?php else: ?>
                  <table>
                    <thead>
                      <tr>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Type</th>
                        <th>Stage / Phase</th>
                        <th>Progress</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($customerProjects as $proj): ?>
                        <tr>
                          <td><strong><?php echo htmlspecialchars($proj['project_id']); ?></strong></td>
                          <td><?php echo htmlspecialchars($proj['project_name']); ?></td>
                          <td><span class="type-badge badge-custom" style="font-size: 9px; padding: 3px 6px;"><?php echo htmlspecialchars($proj['order_type'] ?? 'Custom'); ?></span></td>
                          <td><?php echo htmlspecialchars($proj['current_phase'] ?? 'Planning'); ?></td>
                          <td><strong><?php echo (int)($proj['progress'] ?? 0); ?>%</strong></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php endif; ?>
              </div>
            </div>

          </article>
          
        <?php else: ?>
          <!-- MASTER LIST VIEW (Shows when no customer is clicked) -->
          <article class="panel" style="padding: 24px;">
            <div style="margin-bottom: 20px;">
              <p class="eyebrow">CLIENT DIRECTORY</p>
              <h2>Registered Accounts</h2>
            </div>
            
            <div class="table-wrap">
              <?php if (empty($customersList)): ?>
                <p style="padding: 24px; text-align: center; color: var(--muted); font-size: 13px;">No registered clients found in the system.</p>
              <?php else: ?>
                <table>
                  <thead>
                    <tr>
                      <th>CUSTOMER NAME</th>
                      <th>COMPANY</th>
                      <th>EMAIL</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($customersList as $cust): ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($cust['customer_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($cust['company'] ?? 'Direct Client'); ?></td>
                        <td><?php echo htmlspecialchars($cust['email'] ?? 'Not provided'); ?></td>
                        <td>
                          <a href="customers.php?customer_id=<?php echo urlencode($cust['customer_id']); ?>" style="background: #123c2d; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 700;">
                            View Profile
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endif; ?>
            </div>
          </article>
        <?php endif; ?>

      </div>

    </main>
  </div>
  <script src="../../JS/admin-dashboard.js"></script>           
</body>
</html>