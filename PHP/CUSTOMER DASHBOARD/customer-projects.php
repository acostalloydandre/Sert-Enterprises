<?php
require_once __DIR__ . '/customer-projects-data.php';

$clientFullName = $customerInfo['customer_name'] ?? 'Client Partner';
$nameParts = explode(' ', trim($clientFullName));
$initials = (count($nameParts) >= 2) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($clientFullName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Project Tracker</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/customer-portal.css">
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
          <h1>Project Tracker</h1>
          <p class="welcome">Monitor real-time milestones, fabrication phases, and target completion deadlines.</p>
        </div>
        <div class="topbar-actions">
          <a href="request-project.php" class="text-link" style="padding: 8px 14px; background: var(--green, #123c2d); color: #fff; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">+ Request New Project</a>

          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search projects..." aria-label="Search">
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

      <!-- PROJECTS PIPELINE TABLE PANEL -->
      <section class="panel orders-panel">
        <div class="panel-heading">
          <div>
            <p class="eyebrow">ACTIVE & PAST PROJECTS</p>
            <h2>All Projects (<?php echo count($clientProjects); ?>)</h2>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>PROJECT ID & NAME</th>
                <th>SERVICE TYPE</th>
                <th>CURRENT PHASE</th>
                <th>PROGRESS</th>
                <th>TARGET DEADLINE</th>
                <th>ASSIGNED LEAD</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($clientProjects)): ?>
                <tr>
                  <td colspan="7" style="text-align: center; color: var(--muted); padding: 32px;">No projects found. Once your custom project request is approved and kicked off, it will appear here.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($clientProjects as $proj): ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($proj['project_id'] . ' · ' . $proj['project_name']); ?></strong>
                      <small><?php echo htmlspecialchars($proj['dimensions'] ?? 'Standard Specs'); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($proj['service_type'] ?? 'Custom Signage'); ?></td>
                    <td>
                      <span class="status-pill <?php echo (strtolower($proj['status'] ?? '') === 'completed') ? 'on-track' : 'pending'; ?>">
                        <?php echo htmlspecialchars($proj['current_phase'] ?? $proj['status'] ?? 'Planning'); ?>
                      </span>
                    </td>
                    <td>
                      <div class="progress-cell">
                        <span><?php echo (int)$proj['progress']; ?>%</span>
                        <div class="progress"><i style="width:<?php echo (int)$proj['progress']; ?>%"></i></div>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($proj['deadline']); ?></td>
                    <td><?php echo htmlspecialchars($proj['assigned_employee'] ?? 'Production Team'); ?></td>
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