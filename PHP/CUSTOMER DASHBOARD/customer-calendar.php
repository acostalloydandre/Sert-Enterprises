<?php
require_once __DIR__ . '/customer-calendar-data.php';

$clientFullName = $customerInfo['customer_name'] ?? 'Client Partner';
$nameParts = explode(' ', trim($clientFullName));
$initials = (count($nameParts) >= 2) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($clientFullName, 0, 2));

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
$startDayOfWeek = date('w', $firstDayTimestamp); 
$monthName = date('F Y', $firstDayTimestamp);

$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Schedule Calendar</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/CUSTOMER DASHBOARD/customer-portal.css">
  <link rel="stylesheet" href="../../CSS/CUSTOMER DASHBOARD/customer-calendar.css">
</head>
<body>
  <div class="app-shell">
    
    <?php include __DIR__ . '/customer-sidebar.php'; ?>

    <main class="main-content">
      
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / CLIENT PORTAL</p>
          <h1>Calendar</h1>
          <p class="welcome">Track your upcoming site visits, order placements, and project completion milestones.</p>
        </div>
        <div class="topbar-actions">
          <a href="request-project.php" class="calendar-action-btn">+ Request New Project</a>

          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search calendar..." aria-label="Search">
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

      <article class="panel calendar-panel">
        <div class="calendar-nav-bar">
          <h2><?php echo htmlspecialchars($monthName); ?></h2>
          <div class="calendar-nav-btns">
            <a href="calendar.php?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="cal-btn">‹ Prev</a>
            <a href="calendar.php" class="cal-btn">Today</a>
            <a href="calendar.php?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="cal-btn">Next ›</a>
          </div>
        </div>

        <div class="calendar-grid">
          <div class="cal-header-day">Sun</div>
          <div class="cal-header-day">Mon</div>
          <div class="cal-header-day">Tue</div>
          <div class="cal-header-day">Wed</div>
          <div class="cal-header-day">Thu</div>
          <div class="cal-header-day">Fri</div>
          <div class="cal-header-day">Sat</div>

          <?php for ($i = 0; $i < $startDayOfWeek; $i++): ?>
            <div class="cal-cell other-month"></div>
          <?php endfor; ?>

          <?php 
          for ($day = 1; $day <= $daysInMonth; $day++):
            $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $hasEvents = isset($calendarEvents[$currentDateStr]);
            $isToday = ($currentDateStr === date('Y-m-d'));
          ?>
            <div class="cal-cell <?php echo $isToday ? 'today' : ''; ?>">
              <span class="cal-day-num"><?php echo $day; ?></span>
              <?php if ($hasEvents): ?>
                <div class="cal-events-list">
                  <?php foreach ($calendarEvents[$currentDateStr] as $ev): ?>
                    <div class="cal-event-pill" title="<?php echo htmlspecialchars($ev['title']); ?>">
                      <?php echo htmlspecialchars($ev['title']); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endfor; ?>
        </div>
      </article>

      <footer class="footer">© 2026 SERT Enterprise. Client Portal.</footer>
    </main>
  </div>
  <script src="../../JS/customer-dashboard.js"></script>
</body>
</html>