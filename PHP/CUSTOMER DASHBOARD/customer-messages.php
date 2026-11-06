<?php
require_once __DIR__ . '/customer-messages-data.php';

$clientFullName = $customerInfo['customer_name'] ?? 'Client Partner';
$nameParts = explode(' ', trim($clientFullName));
$initials = (count($nameParts) >= 2) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($clientFullName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Messages & Support</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/CUSTOMER DASHBOARD/customer-portal.css">
  <link rel="stylesheet" href="../../CSS/CUSTOMER DASHBOARD/customer-messages.css">

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
          <h1>Direct Messages & Support</h1>
          <p class="welcome">Communicate directly with our project managers and support team regarding your orders.</p>
        </div>
        <div class="topbar-actions">
          <a href="request-project.php" class="text-link" style="padding: 8px 14px; background: var(--green, #123c2d); color: #fff; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">+ Request New Project</a>

          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search messages..." aria-label="Search">
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

      <?php if (!empty($successMessage)): ?>
        <div class="alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
      <?php endif; ?>

      <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
      <?php endif; ?>

      <!-- MESSAGING PANEL -->
      <article class="panel chat-panel">
        
        <!-- Chat Header -->
        <div class="chat-header">
          <div>
            <h2 class="chat-title">SERT Enterprise Support Team</h2>
            <small class="chat-subtitle">Typically replies within 1 business hour</small>
          </div>
          <span class="status-pill on-track">Online</span>
        </div>

        <!-- Message History Feed -->
        <div class="chat-feed">
          <?php if (empty($messages)): ?>
            <div class="chat-empty">
              No messages yet. Send a message below to start a conversation with our team!
            </div>
          <?php else: ?>
            <?php foreach ($messages as $msg): 
              $isClient = (strtolower($msg['sender_type'] ?? '') === 'client');
            ?>
              <div class="chat-message-row <?php echo $isClient ? 'row-client' : 'row-admin'; ?>">
                <div class="chat-bubble <?php echo $isClient ? 'bubble-client' : 'bubble-admin'; ?>">
                  <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                </div>
                <small class="chat-timestamp">
                  <?php echo $isClient ? 'You · ' : 'SERT Admin · '; ?>
                  <?php echo date('M j, g:i a', strtotime($msg['sent_at'] ?? 'now')); ?>
                </small>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Chat Input Form -->
        <form action="customer-messages.php" method="POST" class="chat-input-form">
          <input type="text" name="message_body" placeholder="Type your message or inquiry here..." required autocomplete="off" class="chat-input">
          <button type="submit" name="send_message" class="submit-btn chat-send-btn">Send</button>
        </form>

      </article>

      <footer class="footer">© 2026 SERT Enterprise. Client Portal.</footer>
    </main>
  </div>
  <script src="../../JS/customer-dashboard.js"></script>
</body>
</html>