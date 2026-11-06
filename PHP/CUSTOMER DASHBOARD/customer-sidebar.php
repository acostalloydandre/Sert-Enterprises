<?php
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
<div class="brand">
    <img src="../../Assets/Logo/SERT-Logo.webp" class="brand-logo brand-logo-full" alt="SERT Enterprise">
    <img src="../../Assets/Logo/logofigure.png" class="brand-logo brand-logo-small" alt="SERT Enterprise">
  </div>

  <div class="sidebar-label">WORKSPACE</div>
  <nav class="main-nav">
    <a href="customer-dashboard.php" class="nav-link <?php echo ($currentFile === 'customer-dashboard.php') ? 'active' : ''; ?>">
      <span class="nav-icon">⊞</span>
      <span class="nav-text">Dashboard</span>
    </a>
    <a href="customer-orders.php" class="nav-link <?php echo ($currentFile === 'customer-orders.php') ? 'active' : ''; ?>">
      <span class="nav-icon">▤</span>
      <span class="nav-text">My Orders</span>
    </a>
    <a href="request-project.php" class="nav-link <?php echo ($currentFile === 'request-project.php') ? 'active' : ''; ?>">
      <span class="nav-icon">＋</span>
      <span class="nav-text">Request Project</span>
    </a>
    <a href="customer-projects.php" class="nav-link <?php echo ($currentFile === 'customer-projects.php') ? 'active' : ''; ?>">
      <span class="nav-icon">▣</span>
      <span class="nav-text">Project Tracker</span>
    </a>
    <a href="customer-messages.php" class="nav-link <?php echo ($currentFile === 'customer-messages.php') ? 'active' : ''; ?>">
      <span class="nav-icon">✉</span>
      <span class="nav-text">Messages</span>
    </a>
    <a href="customer-calendar.php" class="nav-link <?php echo ($currentFile === 'customer-calendar.php') ? 'active' : ''; ?>">
      <span class="nav-icon">▦</span>
      <span class="nav-text">Calendar</span>
    </a>
  </nav>

  <div class="sidebar-label secondary-label">ACCOUNT</div>
  <nav class="main-nav">
    <a href="customer-settings.php" class="nav-link <?php echo ($currentFile === 'customer-settings.php') ? 'active' : ''; ?>">
      <span class="nav-icon">⚙</span>
      <span class="nav-text">Profile Settings</span>
    </a>
    <a href="logout.php" class="nav-link">
      <span class="nav-icon">⮑</span>
      <span class="nav-text">Log out</span>
    </a>
  </nav>

  <button class="sidebar-toggle" aria-label="Toggle Sidebar">‹</button>
</aside>