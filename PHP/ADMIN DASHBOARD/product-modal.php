<!-- MODAL POPUP WINDOW FOR LISTING NEW PRODUCTS -->
<div class="modal-overlay" id="productModal">
  <div class="modal-card">
    <div class="modal-header">
      <h3>List New Product / Service</h3>
      <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
    </div>
    <div class="modal-body">
      <form action="products.php?category=<?php echo urlencode($selectedCategory ?? 'All Categories'); ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_product">
        
        <div class="seller-form-grid">
          <!-- Product Name -->
          <div class="seller-form-group full-width">
            <label for="modal_product_name">Product / Service Title</label>
            <input type="text" id="modal_product_name" name="product_name" required placeholder="e.g. Custom Panaflex Outdoor Signage">
          </div>

          <!-- Category -->
          <div class="seller-form-group">
            <label for="modal_category">Service Category</label>
            <select id="modal_category" name="category" required>
              <?php foreach ($categories as $cat): ?>
                <?php if ($cat !== 'All Categories'): ?>
                  <option value="<?php echo htmlspecialchars($cat); ?>">
                    <?php echo htmlspecialchars($cat); ?>
                  </option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Base Price -->
          <div class="seller-form-group">
            <label for="modal_price">Base Price (₱)</label>
            <input type="number" step="0.01" id="modal_price" name="price" required placeholder="0.00">
          </div>

          <!-- Dimensions -->
          <div class="seller-form-group">
            <label for="modal_dimensions">Dimensions (Width x Length)</label>
            <input type="text" id="modal_dimensions" name="dimensions" placeholder="e.g. 4ft x 8ft">
          </div>

          <!-- Height / Thickness -->
          <div class="seller-form-group">
            <label for="modal_height">Height / Material Thickness</label>
            <input type="text" id="modal_height" name="height" placeholder="e.g. 3mm Acrylic">
          </div>

          <!-- Listing Status -->
          <div class="seller-form-group">
            <label for="modal_status">Catalog Status</label>
            <select id="modal_status" name="status">
              <option value="Active">Active Listing</option>
              <option value="Draft">Draft / Hidden</option>
            </select>
          </div>

          <!-- Materials to be used -->
          <div class="seller-form-group full-width">
            <label for="modal_materials">Materials to be used</label>
            <input type="text" id="modal_materials" name="materials" placeholder="e.g. Panaflex banner, Heavy-duty aluminum frame, LED modules">
          </div>

          <!-- Description -->
          <div class="seller-form-group full-width">
            <label for="modal_description">Specifications & Description</label>
            <textarea id="modal_description" name="description" rows="3" placeholder="Enter materials, installation notes, or custom feature descriptions..."></textarea>
          </div>

          <!-- Product Image Upload -->
          <div class="seller-form-group full-width">
            <label>Product Display Image</label>
            <div class="image-upload-box">
              <p style="font-size: 13px; color: var(--text-main); font-weight: 600;">📁 Drag & drop product photo here, or click to browse</p>
              <p style="font-size: 11px; color: var(--muted); margin-top: 4px;">Supports PNG, JPG, WEBP</p>
              <input type="file" name="product_image" accept="image/*">
            </div>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
          <button type="button" class="btn-primary-action" id="cancelModalBtn" style="background: #e2e8f0; color: #1e293b;">Cancel</button>
          <button type="submit" class="btn-primary-action">Publish Product Listing</button>
        </div>
      </form>
    </div>
  </div>
</div>