<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Default all badge counts to 0
$orderCount = 0;
$customOrderCount = 0;
$projectCount = 0;
$productCount = 0;
$messageCount = 0;

// Connect once and fetch counts for all tables safely
$dbHost = 'localhost';
$dbName = 'sert_enterprise';
$dbUser = 'root';
$dbPass = '';

try {
    $sidebarPdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. Premade Orders Count
    $orderCount = $sidebarPdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    // 2. Custom Orders Count (uncomment/adjust table name if yours differs, e.g. 'custom_orders')
    // $customOrderCount = $sidebarPdo->query("SELECT COUNT(*) FROM custom_orders")->fetchColumn();

    // 3. Messages Count (e.g. unread messages)
    // $messageCount = $sidebarPdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();

} catch (PDOException $e) {
    // Fail silently so the sidebar never crashes the page if a table is missing
}
?>
<aside class="sidebar">
  <div class="brand">
    <img src="../../Assets/Logo/SERT-Logo.webp" class="brand-logo brand-logo-full" alt="SERT Enterprise">
    <img src="../../Assets/Logo/logofigure.png" class="brand-logo brand-logo-small" alt="SERT Enterprise">
  </div>

  <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Collapse sidebar" aria-expanded="true">
      <span class="toggle-arrow">‹</span>
  </button>

  <div class="sidebar-label">WORKSPACE</div>
  <nav class="main-nav" aria-label="Main navigation">
    <a class="nav-link <?php echo ($currentPage == 'admin-dashboard.php') ? 'active' : ''; ?>" href="admin-dashboard.php">
      <span class="nav-icon">▦</span><span class="nav-text">Dashboard</span>
    </a>
    
    <!-- Premade Orders Badge -->
    <a class="nav-link <?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>" href="orders.php">
      <span class="nav-icon">▤</span><span class="nav-text">Orders</span> 
      <?php if ($orderCount > 0): ?>
        <span class="nav-count"><?php echo $orderCount; ?></span>
      <?php endif; ?>
    </a>

    <!-- Custom Orders Badge -->
    <a class="nav-link <?php echo ($currentPage == 'custom-orders.php') ? 'active' : ''; ?>" href="custom-orders.php">
      <span class="nav-icon">▧</span><span class="nav-text">Custom Orders</span> 
      <?php if ($customOrderCount > 0): ?>
        <span class="nav-count"><?php echo $customOrderCount; ?></span>
      <?php endif; ?>
    </a>

    <a class="nav-link <?php echo ($currentPage == 'projects.php') ? 'active' : ''; ?>" href="projects.php">
      <span class="nav-icon">▣</span><span class="nav-text">Projects</span>
    </a>
    <a class="nav-link <?php echo ($currentPage == 'products.php') ? 'active' : ''; ?>" href="products.php">
      <span class="nav-icon">◇</span><span class="nav-text">Products</span>
    </a>
    <a class="nav-link <?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>" href="customers.php">
      <span class="nav-icon">♙</span><span class="nav-text">Customers</span>
    </a>

    <!-- Messages Badge -->
    <a class="nav-link <?php echo ($currentPage == 'messages.php') ? 'active' : ''; ?>" href="messages.php">
      <span class="nav-icon">✉</span><span class="nav-text">Messages</span> 
      <?php if ($messageCount > 0): ?>
        <span class="nav-count unread"><?php echo $messageCount; ?></span>
      <?php endif; ?>
    </a>

    <a class="nav-link <?php echo ($currentPage == 'calendar.php') ? 'active' : ''; ?>" href="calendar.php">
      <span class="nav-icon">▦</span><span class="nav-text">Calendar</span>
    </a>
  </nav>

  <div class="sidebar-label secondary-label">MANAGEMENT</div>
  <nav class="main-nav" aria-label="Management navigation">
    <a class="nav-link <?php echo ($currentPage == 'reports.php') ? 'active' : ''; ?>" href="reports.php">
      <span class="nav-icon">▥</span><span class="nav-text">Reports</span>
    </a>
    <a class="nav-link <?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>" href="settings.php">
      <span class="nav-icon">⚙</span><span class="nav-text">Settings</span>
    </a>
  </nav>

  <div class="sidebar-bottom">
    <div class="sidebar-user">
      <div class="avatar avatar-admin">LA</div>
      <div><strong>Lloyd Acosta</strong><small>Administrator</small></div>
      <span class="more">•••</span>
    </div>
  </div>
</aside>