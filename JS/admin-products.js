function openProductModal() {
    const modal = document.getElementById('productModal');
    if (modal) {
      modal.classList.add('active');
    }
  }
  
  function closeProductModal() {
    const modal = document.getElementById('productModal');
    if (modal) {
      modal.classList.remove('active');
    }
  }
  
  document.addEventListener('DOMContentLoaded', () => {
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const modal = document.getElementById('productModal');
  
    if (closeBtn) {
      closeBtn.addEventListener('click', closeProductModal);
    }
    if (cancelBtn) {
      cancelBtn.addEventListener('click', closeProductModal);
    }
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          closeProductModal();
        }
      });
    }
  });