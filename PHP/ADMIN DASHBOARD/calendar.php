<?php
require_once __DIR__ . '/calendar-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Master Schedule Calendar</title>

  <!-- External Stylesheets -->
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/dashboard-calendar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-calendar.css">
</head>
<body>
  <div class="dashboard-container">
    
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
      
      <!-- TOP HEADER (Preserved Fully) -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / WORKSPACE</p>
          <h1>Calendar</h1>
          <p class="welcome">Inspect ongoing project timelines, site visits, and fulfillment phases.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search schedule..." aria-label="Search">
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

      <!-- FULL-PAGE CALENDAR PANEL -->
      <article class="panel full-calendar-panel">
        <div class="panel-heading calendar-heading">
          <div>
            <p class="eyebrow">SCHEDULES & DEADLINES</p>
            <h2><?php echo htmlspecialchars($currentMonthYearHeading); ?></h2>
          </div>
          <div class="calendar-controls">
            <a href="calendar.php?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" style="text-decoration:none;"><button aria-label="Previous month">‹</button></a>
            <a href="calendar.php?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" style="text-decoration:none;"><button aria-label="Next month">›</button></a>
          </div>
        </div>
        
        <div class="calendar-toolbar">
          <span class="today-label"><i></i> <?php echo htmlspecialchars($todayToolbarLabel); ?></span>
          <a href="calendar.php">Current Month</a>
        </div>

        <div class="full-calendar-grid">
          <div class="weekday">SUNDAY</div>
          <div class="weekday">MONDAY</div>
          <div class="weekday">TUESDAY</div>
          <div class="weekday">WEDNESDAY</div>
          <div class="weekday">THURSDAY</div>
          <div class="weekday">FRIDAY</div>
          <div class="weekday">SATURDAY</div>

          <?php
          for ($i = 0; $i < $firstDayOfWeek; $i++) {
              echo '<div class="day muted"><span></span></div>';
          }

          for ($d = 1; $d <= $totalDaysInMonth; $d++) {
              $dateString = sprintf("%s-%02d-%02d", $currentYear, $currentMonth, $d);
              $isToday = ($d === (int)date('j') && $currentMonth === (int)date('n') && $currentYear === (int)date('Y'));
              $hasEvent = isset($calendarEvents[$dateString]) && !empty($calendarEvents[$dateString]);

              $classes = ['day'];
              if ($isToday) $classes[] = 'selected';
              if ($hasEvent) $classes[] = 'has-event';

              echo '<div class="' . implode(' ', $classes) . '"';
              if ($hasEvent) {
                  echo ' data-date="' . $dateString . '" tabindex="0"';
              }
              echo '>';

              echo '<span>' . $d . '</span>';

              // Render clickable preview badges and indicators inside the cell
              if ($hasEvent) {
                  foreach ($calendarEvents[$dateString] as $ev) {
                      $phaseLower = strtolower($ev['phase']);
                      $badgeClass = 'planning';
                      if (strpos($phaseLower, 'design') !== false) $badgeClass = 'designing';
                      elseif (strpos($phaseLower, 'develop') !== false) $badgeClass = 'development';
                      elseif (strpos($phaseLower, 'site visit') !== false) $badgeClass = 'site-visit';
                      elseif (strpos($phaseLower, 'installation') !== false) $badgeClass = 'installation';
                      elseif (strpos($phaseLower, 'delivery') !== false) $badgeClass = 'delivery';

                      // Clicking this badge instantly routes straight to the project details page!
                      echo '<a href="projects.php?project_id=' . urlencode($ev['id']) . '" class="calendar-cell-event ' . $badgeClass . '" title="Click to open project ' . htmlspecialchars($ev['id']) . '">';
                      echo htmlspecialchars($ev['name']);
                      echo '</a>';
                  }
              }

              echo '</div>';
          }
          ?>
        </div>

        <!-- CALENDAR LEGEND -->
        <div class="calendar-legend" style="margin-top: 16px;">
          <span><i class="green-dot"></i> Installation</span>
          <span><i class="orange-dot"></i> Planning</span>
          <span><i class="purple-dot"></i> Designing</span>
          <span><i class="blue-dot"></i> Development</span>
          <span><i class="gold-dot"></i> Delivery</span>
          <span><i class="red-dot"></i> Site visit</span>
        </div>
      </article>

    </main>
  </div>

  <!-- Inject Calendar Data into JS Tooltips -->
  <script>
    window.projectEvents = <?php echo json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  </script>
  <script src="../../JS/admin-dashboard.js"></script>
</body>
</html>