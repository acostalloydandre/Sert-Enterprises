<?php
require_once __DIR__ . '/projects-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Project Tracker</title>

  <!-- External Stylesheets -->
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-projects.css">
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
          <h1>Project Tracker</h1>
          <p class="welcome">Monitor active installations, service workflows, and client milestones.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search projects..." aria-label="Search">
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

      <!-- STATUS FILTER OVAL PILLS -->
      <nav class="status-pills-bar" aria-label="Project status filters">
        <?php foreach ($statuses as $st): ?>
          <?php 
            $isActiveStatus = ($selectedStatus === $st);
            $queryUrl = "projects.php?status=" . urlencode($st);
          ?>
          <a href="<?php echo $queryUrl; ?>" class="status-pill-btn <?php echo $isActiveStatus ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($st); ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <!-- MASTER-DETAIL SIDE-BY-SIDE LAYOUT -->
      <div class="orders-master-layout">
        
        <!-- LEFT PANEL: Directory (360px) -->
        <article class="panel directory-panel">
          <div class="directory-header">
            <p class="directory-eyebrow">PROJECT DIRECTORY (<?php echo strtoupper(htmlspecialchars($selectedStatus)); ?>)</p>
            <h3 class="directory-title">Active Projects</h3>
          </div>
          <p class="directory-subtitle">Select a project to inspect progress breakdown.</p>
          
          <div class="client-list-wrap">
            <?php if (empty($projectsDirectory)): ?>
              <p class="empty-directory-text">No projects found matching this filter.</p>
            <?php else: ?>
              <?php foreach ($projectsDirectory as $p): ?>
                <?php $isActiveProject = ($selectedProjectId === $p['project_id']); ?>
                <a href="projects.php?status=<?php echo urlencode($selectedStatus); ?>&project_id=<?php echo urlencode($p['project_id']); ?>" 
                   class="client-item-link <?php echo $isActiveProject ? 'active' : ''; ?>">
                  <div>
                    <strong><?php echo htmlspecialchars($p['project_id'] . ' · ' . $p['project_name']); ?></strong>
                    <small><?php echo htmlspecialchars($p['customer_name']); ?></small>
                  </div>
                  <span><?php echo $p['progress']; ?>%</span>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </article>

        <!-- RIGHT PANEL: Approved Project Detail (1fr) -->
        <article class="panel content-detail-panel">
          <?php if (!$selectedProjectId || empty($selectedProjectDetails)): ?>
            <div class="empty-orders-state">
              <h3>No Project Selected</h3>
              <p>Choose a project from the directory list on the left to view specifications.</p>
            </div>
          <?php else: ?>
            <div class="client-record-header">
              <p class="directory-eyebrow">APPROVED PROJECT OVERVIEW</p>
              <h2><?php echo htmlspecialchars($selectedProjectDetails['project_id'] . ' - ' . $selectedProjectDetails['project_name']); ?></h2>
            </div>
            
            <!-- Latest Approved Execution Status Card -->
            <div class="active-order-section">
              <h4 class="section-label-heading">Current Execution Status</h4>
              <div class="active-order-card">
                <div class="project-card-header">
                  <div>
                    <strong><?php echo htmlspecialchars($selectedProjectDetails['service_type']); ?></strong>
                    <p>Client: <?php echo htmlspecialchars($selectedProjectDetails['customer_name']); ?> · Deadline: <?php echo htmlspecialchars($selectedProjectDetails['deadline']); ?></p>
                  </div>
                  <span class="status-pill <?php echo htmlspecialchars($selectedProjectDetails['status_class'] ?? 'tag-green'); ?>">
                    <?php echo htmlspecialchars($selectedProjectDetails['status']); ?>
                  </span>
                </div>
                
                <!-- Progress Indicator -->
                <div>
                  <div class="project-progress-meta">
                    <span>Completion Progress</span>
                    <span><?php echo $selectedProjectDetails['progress']; ?>%</span>
                  </div>
                  <div class="project-progress-bar-container">
                    <div class="project-progress-fill" style="width: <?php echo (int)$selectedProjectDetails['progress']; ?>%;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detailed Specifications Table -->
            <div>
              <h4 class="section-label-heading">Project Specifications & Timeline</h4>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>FIELD / ATTRIBUTE</th>
                      <th>DETAILS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><strong>Project ID</strong></td>
                      <td><?php echo htmlspecialchars($selectedProjectDetails['project_id']); ?></td>
                    </tr>
                    <tr>
                      <td><strong>Client Name</strong></td>
                      <td><?php echo htmlspecialchars($selectedProjectDetails['customer_name']); ?></td>
                    </tr>
                    <tr>
                      <td><strong>Service Scope</strong></td>
                      <td><?php echo htmlspecialchars($selectedProjectDetails['service_type']); ?></td>
                    </tr>
                    <tr>
                      <td><strong>Target Deadline</strong></td>
                      <td><?php echo htmlspecialchars($selectedProjectDetails['deadline']); ?></td>
                    </tr>
                    <tr>
                      <td><strong>Current Health / Risk</strong></td>
                      <td><span class="status-pill <?php echo htmlspecialchars($selectedProjectDetails['risk_class'] ?? 'tag-green'); ?>">On track</span></td>
                    </tr>
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