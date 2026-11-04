<?php
// Load data logic
require_once __DIR__ . './dashboard-data.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Admin Dashboard</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/dashboard-calendar.css">
</head>

<body>
  <div class="app-shell">

    <!-- Load Reusable Sidebar Component -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / WORKSPACE</p>
          <h1>Dashboard</h1>
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

      <!-- Summary Stat Cards -->
      <section class="summary-grid" aria-label="Business summary">
        <?php foreach ($summaryCards as $card): ?>
          <article class="stat-card">
            <div class="stat-top">
              <span
                class="stat-icon <?php echo htmlspecialchars($card['icon_color']); ?>"><?php echo $card['icon']; ?></span>
              <span
                class="stat-tag <?php echo htmlspecialchars($card['tag_class']); ?>"><?php echo htmlspecialchars($card['tag']); ?></span>
            </div>
            <p class="stat-label"><?php echo htmlspecialchars($card['label']); ?></p>
            <div class="stat-value"><?php echo htmlspecialchars($card['value']); ?></div>
            <p class="stat-foot"><?php echo $card['foot']; ?></p>
          </article>
        <?php endforeach; ?>
      </section>

      <section class="content-grid">

        <!-- Attention / Priority Panel -->
        <article class="panel attention-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">PRIORITY TASKS</p>
              <h2>Needs attention</h2>
            </div>
            <a class="text-link" href="#">View all <span>→</span></a>
          </div>
          <div class="attention-list">
            <?php if (empty($attentionItems)): ?>
              <div style="padding: 16px; color: var(--muted); font-size: 9px;">No pending task.</div>
            <?php else: ?>
              <?php foreach ($attentionItems as $item): ?>
                <div class="attention-item">
                  <span
                    class="attention-symbol <?php echo htmlspecialchars($item['symbol_class']); ?>"><?php echo htmlspecialchars($item['symbol']); ?></span>
                  <div class="attention-copy">
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                    <small><?php echo htmlspecialchars($item['meta']); ?></small>
                  </div>
                  <span
                    class="status-pill <?php echo htmlspecialchars($item['status_class']); ?>"><?php echo htmlspecialchars($item['status_text']); ?></span>
                  <a href="<?php echo htmlspecialchars($item['url']); ?>" class="row-arrow" aria-label="View item">→</a>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </article>

        <!-- CALENDAR PANEL -->
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
            <a href="#">View calendar →</a>
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
            // Output empty offset slots before the 1st of the month
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
              echo '<div class="day muted"><span></span></div>';
            }

            // Loop through all days of the current active month automatically
            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
              $dateString = sprintf("%s-%s-%02d", $currentYear, $currentMonth, $d);
              $isToday = ($d === $currentDay);
              $hasEvent = isset($calendarEvents[$dateString]) && !empty($calendarEvents[$dateString]);

              $classes = ['day'];
              if ($isToday)
                $classes[] = 'selected';
              if ($hasEvent)
                $classes[] = 'has-event';

              echo '<div class="' . implode(' ', $classes) . '"';
              if ($hasEvent) {
                echo ' data-date="' . $dateString . '" tabindex="0" aria-label="Scheduled project on ' . $dateString . '"';
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

      <section class="content-grid lower-grid">

        <!-- Active Projects -->
        <article class="panel projects-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">PROJECT TRACKER</p>
              <h2>Active projects</h2>
            </div>
            <a class="text-link" href="#">All projects <span>→</span></a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>PROJECT / CUSTOMER</th>
                  <th>SERVICE</th>
                  <th>PROGRESS</th>
                  <th>DEADLINE</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($activeProjects)): ?>
                  <tr>
                    <td colspan="5" style="text-align: center; color: var(--muted); padding: 18px;">No projects yet.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($activeProjects as $project): ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($project['project_id'] . ' · ' . $project['project_name']); ?></strong><small><?php echo htmlspecialchars($project['customer_name']); ?></small>
                      </td>
                      <td><?php echo htmlspecialchars($project['service_type']); ?></td>
                      <td>
                        <div class="progress-cell">
                          <span><?php echo $project['progress']; ?>%</span>
                          <div class="progress"><i class="<?php echo htmlspecialchars($project['risk_class'] ?? ''); ?>"
                              style="width:<?php echo $project['progress']; ?>%"></i></div>
                        </div>
                      </td>
                      <td><?php echo htmlspecialchars($project['deadline']); ?></td>
                      <td><span class="status-pill <?php echo htmlspecialchars($project['status']); ?>">On track</span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </article>

        <!-- Messages -->
        <article class="panel messages-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">CUSTOMER COMMUNICATION</p>
              <h2>Recent messages <span class="heading-count"><?php echo count($messages); ?></span></h2>
            </div>
            <a class="text-link" href="#">Inbox <span>→</span></a>
          </div>
          <div class="message-list">
            <?php if (empty($messages)): ?>
              <div style="padding: 12px 0; color: var(--muted); font-size: 9px;">No message from customers.</div>
            <?php else: ?>
              <?php foreach ($messages as $msg): ?>
                <a class="message-item" href="<?php echo htmlspecialchars($msg['url']); ?>">
                  <span
                    class="avatar <?php echo htmlspecialchars($msg['avatar_class']); ?>"><?php echo htmlspecialchars($msg['initials']); ?></span>
                  <span class="message-copy">
                    <strong><?php echo htmlspecialchars($msg['sender_name']); ?>     <?php if ($msg['is_unread']): ?><b
                          class="unread-dot"></b><?php endif; ?></strong>
                    <small><?php echo htmlspecialchars($msg['message_snippet']); ?></small>
                  </span>
                  <time><?php echo htmlspecialchars($msg['time_sent']); ?></time>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <a href="#" class="compose-link">Open messaging center <span>→</span></a>
        </article>
      </section>

      <!-- Recent Orders Table -->
      <section class="panel orders-panel">
        <div class="panel-heading">
          <div>
            <p class="eyebrow">LATEST CUSTOMER ACTIVITY</p>
            <h2>Recent orders</h2>
          </div>
          <a class="text-link" href="#">View all orders <span>→</span></a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ORDER ID</th>
                <th>CUSTOMER</th>
                <th>ORDER / SERVICE</th>
                <th>DATE</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentOrders)): ?>
                <tr>
                  <td colspan="7" style="text-align: center; color: var(--muted); padding: 18px;">No Recent Order.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($order['amount']); ?></td>
                    <td><span
                        class="status-pill <?php echo htmlspecialchars($order['status_class']); ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                    </td>
                    <td><a href="#" class="row-arrow">→</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <footer class="footer">© 2026 SERT Enterprise. Admin Management System.</footer>
    </main>
  </div>

  <!-- Inject Calendar Data into JS -->
  <script>
    window.projectEvents = <?php echo json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  </script>
  <script src="../../JS/admin-dashboard.js"></script>
</body>

</html>