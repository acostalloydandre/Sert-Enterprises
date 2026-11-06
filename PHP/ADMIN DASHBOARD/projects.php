<?php
require_once __DIR__ . '/projects-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Custom Project Tracker</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-projects.css">
</head>
<body>
  <div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
      
      <!-- TOP HEADER -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / WORKSPACE</p>
          <h1>Custom Project Tracker</h1>
          <p class="welcome">Manage fulfillment phases for graduated custom orders.</p>
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

      <!-- TOP PHASE FILTER PILLS BAR -->
      <nav class="status-pills-bar">
        <?php 
          $phaseFilters = ['All', 'Planning', 'Designing', 'Development', 'Installation', 'Delivery Complete'];
          $phaseColors = [
              'Planning' => '#cc8534', 'Designing' => '#8b71cc', 'Development' => '#518cca', 'Installation' => '#459871', 'Delivery Complete' => '#d69e2e'
          ];
          foreach ($phaseFilters as $phase):
            $isActive = ($selectedPhaseFilter === $phase);
            $dotColor = $phaseColors[$phase] ?? '#94a3b8';
            $phaseClass = getPhaseClass($phase);
        ?>
          <a href="projects.php?phase=<?php echo urlencode($phase); ?>" class="status-pill-btn <?php echo $isActive ? 'active' : ''; ?>">
            <span class="status-pill-dot" style="color: <?php echo $dotColor; ?>;">●</span>
            <?php echo htmlspecialchars($phase); ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <?php if (!empty($successMessage)): ?>
        <div class="alert-banner-success"><?php echo htmlspecialchars($successMessage); ?></div>
      <?php endif; ?>

      <!-- FULL-WIDTH WORKSPACE AREA -->
      <div class="projects-workspace-container">

        <?php if ($selectedProjectId && !empty($selectedProjectDetails)): ?>
          <?php 
            $currentPhase = $selectedProjectDetails['current_phase'] ?? 'Planning';
            $phaseClass = getPhaseClass($currentPhase);
          ?>
          
          <!-- INSPECTION & MANAGEMENT VIEW -->
          <article class="panel panel-container-padded">
            <div class="panel-inspection-header">
              <div>
                <p class="eyebrow">PROJECT MANAGEMENT INSPECTION</p>
                <h2><?php echo htmlspecialchars($selectedProjectDetails['project_id'] . ' - ' . $selectedProjectDetails['project_name']); ?></h2>
              </div>
              <a href="projects.php?phase=<?php echo urlencode($selectedPhaseFilter); ?>" class="back-to-projects-btn">← Back to All Projects</a>
            </div>

            <div class="inspection-grid-split">
              <div class="inspection-card-box">
                <p class="inspection-card-label">Client Information</p>
                <p class="inspection-card-value"><?php echo htmlspecialchars($selectedProjectDetails['customer_name']); ?></p>
                <p class="inspection-card-subtext">Target Deadline: <?php echo htmlspecialchars($selectedProjectDetails['deadline']); ?></p>
              </div>

              <div class="inspection-card-box">
                <p class="inspection-card-label">Current Stage & Progress</p>
                <p class="inspection-card-value phase-<?php echo $phaseClass; ?>">
                  <?php echo htmlspecialchars($currentPhase); ?> (<?php echo (int)$selectedProjectDetails['progress']; ?>%)
                </p>
                <div class="project-progress-bar-container">
                  <div class="project-progress-fill fill-<?php echo $phaseClass; ?>" style="width: <?php echo (int)$selectedProjectDetails['progress']; ?>%;"></div>
                </div>
              </div>
            </div>

            <div class="inspection-grid-split no-margin">
              <!-- ADVANCE STAGE MODULE -->
              <div class="team-assignment-module no-margin">
                <h4>Advance Project Phase</h4>
                <form action="projects.php?phase=<?php echo urlencode($selectedPhaseFilter); ?>&project_id=<?php echo urlencode($selectedProjectId); ?>" method="POST">
                  <input type="hidden" name="action" value="update_phase">
                  <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($selectedProjectId); ?>">
                  
                  <select name="current_phase" class="team-assignment-select">
                    <?php foreach ($customPhaseMap as $phaseName => $pct): ?>
                      <option value="<?php echo htmlspecialchars($phaseName); ?>" <?php echo ($currentPhase === $phaseName) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($phaseName); ?> (<?php echo $pct; ?>%)
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="team-assignment-btn">Update Phase & Progress</button>
                </form>
              </div>

              <!-- ASSIGN PERSONNEL MODULE -->
              <div class="team-assignment-module no-margin">
                <h4>Assign Personnel</h4>
                <form action="projects.php?phase=<?php echo urlencode($selectedPhaseFilter); ?>&project_id=<?php echo urlencode($selectedProjectId); ?>" method="POST">
                  <input type="hidden" name="action" value="assign_employee">
                  <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($selectedProjectId); ?>">
                  
                  <select name="assigned_employee" class="team-assignment-select">
                    <?php 
                      $currentAssignee = $selectedProjectDetails['assigned_employee'] ?? 'Unassigned';
                      foreach ($teamMembers as $member): 
                    ?>
                      <option value="<?php echo htmlspecialchars($member); ?>" <?php echo ($currentAssignee === $member) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($member); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="team-assignment-btn">Assign Staff</button>
                </form>
              </div>
            </div>
          </article>

        <?php else: ?>
          <!-- GRID OF ALL PROJECTS -->
          <article class="panel panel-container-padded">
            <div style="margin-bottom: 20px;">
              <p class="eyebrow">PROJECT PIPELINE</p>
              <h2>Active Custom Projects (<?php echo htmlspecialchars($selectedPhaseFilter); ?>)</h2>
            </div>

            <?php if (empty($projectsList)): ?>
              <div class="empty-orders-state">
                <h3>No Projects Found</h3>
                <p>There are no custom projects under this stage filter. Once downpayments are confirmed in Custom Orders, they will graduate here automatically.</p>
              </div>
            <?php else: ?>
              <div class="projects-grid-layout">
                <?php foreach ($projectsList as $proj): 
                  $projPhase = $proj['current_phase'] ?? 'Planning';
                  $phaseClass = getPhaseClass($projPhase);
                ?>
                  <a href="projects.php?phase=<?php echo urlencode($selectedPhaseFilter); ?>&project_id=<?php echo urlencode($proj['project_id']); ?>" class="project-card-item">
                    
                    <div class="project-card-header">
                      <div>
                        <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;"><?php echo htmlspecialchars($proj['project_id']); ?></span>
                        <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 2px;"><?php echo htmlspecialchars($proj['project_name']); ?></h3>
                      </div>
                      
                      <!-- DYNAMIC COLOR BADGE VIA CSS CLASS -->
                      <span class="type-badge badge-<?php echo $phaseClass; ?>">
                        <?php echo htmlspecialchars($projPhase); ?>
                      </span>
                    </div>

                    <p style="font-size: 12px; color: #475569; margin-bottom: 6px;">Client: <strong><?php echo htmlspecialchars($proj['customer_name']); ?></strong></p>
                    <p style="font-size: 11px; color: #64748b; margin-bottom: 12px;">👤 Staff: <strong><?php echo htmlspecialchars($proj['assigned_employee'] ?? 'Unassigned'); ?></strong></p>

                    <!-- DYNAMIC COLORED PROGRESS BAR VIA CSS CLASS -->
                    <div>
                      <div class="project-progress-meta phase-<?php echo $phaseClass; ?>">
                        <span>Stage Progress</span>
                        <span><?php echo (int)$proj['progress']; ?>%</span>
                      </div>
                      <div class="project-progress-bar-container">
                        <div class="project-progress-fill fill-<?php echo $phaseClass; ?>" style="width: <?php echo (int)$proj['progress']; ?>%;"></div>
                      </div>
                    </div>

                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </article>
        <?php endif; ?>

      </div>
    </main>
  </div>
  <script src="../../JS/admin-dashboard.js"></script>
</body>
</html>