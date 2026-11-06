<?php
require_once __DIR__ . '/customer-dashboard-data.php';

$clientFullName = $customerInfo['customer_name'] ?? 'Client Partner';
$nameParts = explode(' ', trim($clientFullName));
$initials = (count($nameParts) >= 2) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($clientFullName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Client Portal Dashboard</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/dashboard-calendar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/CUSTOMER DASHBOARD/customer-sidebar.css">

</head>
<body>
  <div class="app-shell">
    
    <?php include __DIR__ . '/customer-sidebar.php'; ?>

    <main class="main-content">
      
      <!-- TOP HEADER BAR -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / CLIENT PORTAL</p>
          <h1>Welcome back, <?php echo htmlspecialchars($clientFullName); ?>!</h1>
          <p class="welcome">Here is the live status of your custom signage, orders, and schedule.</p>
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

      <!-- 4 Summary Stat Cards (Including Unpaid Downpayment) -->
      <section class="summary-grid" style="grid-template-columns: repeat(4, minmax(0, 1fr));" aria-label="Client summary">
        <article class="stat-card">
          <div class="stat-top">
            <span class="stat-icon green">▣</span>
            <span class="stat-tag tag-green">Ongoing</span>
          </div>
          <p class="stat-label">Active Projects</p>
          <div class="stat-value"><?php echo count($clientProjects); ?></div>
          <p class="stat-foot">In production & installation</p>
        </article>

        <article class="stat-card">
          <div class="stat-top">
            <span class="stat-icon orange">▤</span>
            <span class="stat-tag tag-orange">Review</span>
          </div>
          <p class="stat-label">Pending Orders</p>
          <div class="stat-value"><?php echo count($pendingOrders); ?></div>
          <p class="stat-foot">Awaiting approval or quote</p>
        </article>

        <article class="stat-card">
          <div class="stat-top">
            <span class="stat-icon purple" style="background:#fef2f2; color:#cd6554;">💳</span>
            <span class="stat-tag" style="background:#fef2f2; color:#cd6554;">Action Needed</span>
          </div>
          <p class="stat-label">Unpaid Downpayments</p>
          <div class="stat-value" style="color:#cd6554;"><?php echo count($unpaidDownpayments); ?></div>
          <p class="stat-foot">Required for project kickoff</p>
        </article>

        <article class="stat-card">
          <div class="stat-top">
            <span class="stat-icon blue">◷</span>
            <span class="stat-tag tag-blue">Scheduled</span>
          </div>
          <p class="stat-label">Upcoming Site Visits</p>
          <div class="stat-value"><?php echo count($siteVisits); ?></div>
          <p class="stat-foot">Inspection & measurements</p>
        </article>
      </section>

      <!-- NEW PANEL: UNPAID DOWNPAYMENTS BREAKDOWN -->
      <section class="panel orders-panel" style="margin-bottom: 17px; border-left: 4px solid #cd6554;">
        <div class="panel-heading">
          <div>
            <p class="eyebrow" style="color:#cd6554;">PAYMENT REQUIRED</p>
            <h2>Pending Downpayment Invoices</h2>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ORDER / PROJECT ID</th>
                <th>SERVICE TYPE</th>
                <th>DATE PLACED</th>
                <th>TOTAL AMOUNT</th>
                <th>STATUS</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($unpaidDownpayments)): ?>
                <tr>
                  <td colspan="6" style="text-align: center; color: var(--muted); padding: 18px;">No unpaid downpayments. All accounts are up to date!</td>
                </tr>
              <?php else: ?>
                <?php foreach ($unpaidDownpayments as $down): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($down['order_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($down['order_type'] ?? 'Custom Signage'); ?></td>
                    <td><?php echo htmlspecialchars($down['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($down['amount'] ?? '₱0.00'); ?></td>
                    <td><span class="status-pill" style="background: #fef2f2; color: #cd6554;">Unpaid Downpayment</span></td>
                    <td><a href="customer-orders.php?order_id=<?php echo urlencode($down['order_id']); ?>" class="text-link" style="font-weight:700;">Pay Now →</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- CONTENT GRID: PENDING ORDERS & INTEGRATED CALENDAR -->
      <section class="content-grid">

        <!-- Pending Orders Panel -->
        <article class="panel orders-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">ACTION REQUIRED</p>
              <h2>Pending orders</h2>
            </div>
            <a class="text-link" href="customer-orders.php">View all <span>→</span></a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ORDER ID</th>
                  <th>SERVICE TYPE</th>
                  <th>DATE</th>
                  <th>AMOUNT</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($pendingOrders)): ?>
                  <tr>
                    <td colspan="5" style="text-align: center; color: var(--muted); padding: 18px;">No pending orders.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($pendingOrders as $order): ?>
                    <tr>
                      <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                      <td><?php echo htmlspecialchars($order['order_type'] ?? 'Custom Signage'); ?></td>
                      <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                      <td><?php echo htmlspecialchars($order['amount'] ?? '₱0.00'); ?></td>
                      <td><span class="status-pill pending"><?php echo htmlspecialchars($order['status']); ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </article>

        <!-- INTEGRATED CLIENT CALENDAR PANEL -->
        <article class="panel calendar-panel">
          <div class="panel-heading calendar-heading">
            <div>
              <p class="eyebrow">SCHEDULE & DEADLINES</p>
              <h2><?php echo htmlspecialchars($currentMonthYearHeading); ?></h2>
            </div>
            <div class="calendar-controls">
              <button aria-label="Previous month">‹</button>
              <button aria-label="Next month">›</button>
            </div>
          </div>
          <div class="calendar-toolbar">
            <span class="today-label"><i></i> <?php echo htmlspecialchars($todayToolbarLabel); ?></span>
            <a href="calendar.php">Client Timeline →</a>
          </div>

          <div class="calendar-grid">
            <div class="weekday">SUN</div>
            <div class="weekday">MON</div>
            <div class="weekday">TUE</div>
            <div class="weekday">WED</div>
            <div class="weekday">THU</div>
            <div class="weekday">FRI</div>
            <div class="weekday">SAT</div>

            <?php
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
              echo '<div class="day muted"><span></span></div>';
            }

            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
              $dateString = sprintf("%s-%s-%02d", $currentYear, $currentMonth, $d);
              $isToday = ($d === $currentDay);
              $hasEvent = isset($calendarEvents[$dateString]) && !empty($calendarEvents[$dateString]);

              $classes = ['day'];
              if ($isToday) $classes[] = 'selected';
              if ($hasEvent) $classes[] = 'has-event';

              echo '<div class="' . implode(' ', $classes) . '"';
              if ($hasEvent) {
                echo ' data-date="' . $dateString . '" tabindex="0" aria-label="Scheduled event on ' . $dateString . '"';
              }
              echo '>';

              echo '<span>' . $d . '</span>';

              if ($hasEvent) {
                $firstEventPhase = $calendarEvents[$dateString][0]['phase'] ?? '';
                $dotClass = getDotClass($firstEventPhase);
                echo '<i class="event-dot ' . $dotClass . '"></i>';
              }

              echo '</div>';
            }
            ?>
          </div>

          <div class="calendar-legend">
            <span><i class="green-dot"></i> Installation</span>
            <span><i class="orange-dot"></i> Planning</span>
            <span><i class="purple-dot"></i> Designing</span>
            <span><i class="blue-dot"></i> Development</span>
            <span><i class="gold-dot"></i> Delivery</span>
            <span><i class="red-dot"></i> Site visit</span>
          </div>
        </article>
      </section>

      <!-- UPCOMING SITE VISITS SCHEDULE -->
      <section class="panel orders-panel" style="margin-top: 17px;">
        <div class="panel-heading">
          <div>
            <p class="eyebrow">APPOINTMENTS & INSPECTIONS</p>
            <h2>Upcoming Site Visits & Surveys</h2>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>PROJECT NAME</th>
                <th>SCHEDULED DATE</th>
                <th>VISIT TYPE / PHASE</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($siteVisits)): ?>
                <tr>
                  <td colspan="4" style="text-align: center; color: var(--muted); padding: 18px;">No site visits currently scheduled. Our team will coordinate with you via messages.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($siteVisits as $visit): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($visit['project_name']); ?></strong><small><?php echo htmlspecialchars($visit['project_id']); ?></small></td>
                    <td><?php echo htmlspecialchars($visit['event_date']); ?></td>
                    <td><?php echo htmlspecialchars($visit['event_type'] ?? 'On-site Inspection'); ?></td>
                    <td><span class="status-pill delayed" style="background: #fef2f2; color: #cd6554;">Scheduled</span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Active Projects Table -->
      <section class="panel orders-panel" style="margin-top: 17px;">
        <div class="panel-heading">
          <div>
            <p class="eyebrow">PROJECT TRACKER</p>
            <h2>Your active projects</h2>
          </div>
          <a class="text-link" href="customer-projects.php">View details <span>→</span></a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>PROJECT ID & NAME</th>
                <th>SERVICE TYPE</th>
                <th>PROGRESS</th>
                <th>DEADLINE</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($clientProjects)): ?>
                <tr>
                  <td colspan="5" style="text-align: center; color: var(--muted); padding: 18px;">No active projects currently found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($clientProjects as $project): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($project['project_id'] . ' · ' . $project['project_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($project['service_type'] ?? 'Signage / Fit-out'); ?></td>
                    <td>
                      <div class="progress-cell">
                        <span><?php echo (int)$project['progress']; ?>%</span>
                        <div class="progress"><i style="width:<?php echo (int)$project['progress']; ?>%"></i></div>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($project['deadline']); ?></td>
                    <td><span class="status-pill on-track"><?php echo htmlspecialchars($project['status'] ?? 'Ongoing'); ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <footer class="footer">© 2026 SERT Enterprise. Client Portal.</footer>
    </main>
  </div>

  <script>
    window.projectEvents = <?php echo json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  </script>
  <script src="../../JS/customer-dashboard.js"></script>
</body>
</html>