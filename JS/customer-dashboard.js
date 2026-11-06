/**
 * SERT Enterprise - Customer Portal Sidebar Toggle Logic
 */
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.querySelector('.sidebar-toggle');
  
    if (toggleBtn) {
      // Check saved state from previous navigation and apply to <body>
      const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
      if (isCollapsed) {
        document.body.classList.add('sidebar-collapsed');
        toggleBtn.textContent = '›';
      }
  
      // Toggle event on click targeting document.body
      toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-collapsed');
  
        const collapsedNow = document.body.classList.contains('sidebar-collapsed');
        localStorage.setItem('sidebar_collapsed', collapsedNow);
        
        // Switch arrow direction matching sidebar.css rules
        toggleBtn.textContent = collapsedNow ? '›' : '‹';
      });
    }
  });