<?php
require_once __DIR__ . '/messages-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERT Enterprise | Messages Hub</title>

  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-dashboard.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/body.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/sidebar.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-orders.css">
  <link rel="stylesheet" href="../../CSS/DASHBOARD DESIGN/admin-messages.css">
</head>
<body>
  <div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
      
      <!-- TOP HEADER -->
      <header class="topbar">
        <div class="page-heading">
          <p class="eyebrow">SERT ENTERPRISE / WORKSPACE</p>
          <h1>Client Communications Hub</h1>
          <p class="welcome">Manage direct inquiries and project messaging threads with registered clients.</p>
        </div>
        <div class="topbar-actions">
          <label class="search-box">
            <span>⌕</span>
            <input type="search" placeholder="Search messages..." aria-label="Search">
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

      <?php if (!empty($successMessage)): ?>
        <div class="alert-banner-success" style="margin-top: 16px;"><?php echo htmlspecialchars($successMessage); ?></div>
      <?php endif; ?>

      <!-- TWO-PANEL MESSAGING LAYOUT -->
      <div class="messages-master-layout" style="margin-top: 16px;">
        
        <!-- LEFT PANEL: Conversations Directory & Tabs -->
        <article class="panel messages-directory-panel">
          <div class="directory-header">
            <p class="directory-eyebrow">INBOX DIRECTORY</p>
            <h3 class="directory-title">Client Conversations</h3>
          </div>
          
          <!-- TAB NAVIGATION PILLS (Inbox / Unread / Archived) -->
          <div style="display: flex; gap: 6px; margin: 12px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <a href="messages.php?tab=inbox" class="status-pill-btn <?php echo ($currentTab === 'inbox') ? 'active' : ''; ?>" style="padding: 6px 12px; font-size: 11px;">Inbox</a>
            <a href="messages.php?tab=unread" class="status-pill-btn <?php echo ($currentTab === 'unread') ? 'active' : ''; ?>" style="padding: 6px 12px; font-size: 11px;">Unread</a>
            <a href="messages.php?tab=archived" class="status-pill-btn <?php echo ($currentTab === 'archived') ? 'active' : ''; ?>" style="padding: 6px 12px; font-size: 11px;">Archived</a>
          </div>

          <div class="messages-list-wrap">
            <?php if (empty($conversationsList)): ?>
              <p class="empty-directory-text" style="padding: 16px; text-align: center; color: #64748b; font-size: 12px;">No conversations found in this tab.</p>
            <?php else: ?>
              <?php foreach ($conversationsList as $conv): 
                $isActiveConv = ((string)$selectedConversationId === (string)$conv['conversation_id']);
                $isUnread = !empty($conv['is_unread']) && $conv['is_unread'] == 1;
              ?>
                <a href="messages.php?tab=<?php echo htmlspecialchars($currentTab); ?>&conversation_id=<?php echo urlencode($conv['conversation_id']); ?>" class="message-item-link <?php echo $isActiveConv ? 'active' : ''; ?>">
                  <div style="flex-grow: 1; overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                      <strong style="font-size: 13px; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                        <?php if ($isUnread): ?>
                          <span style="width: 8px; height: 8px; background: #059669; border-radius: 50%; display: inline-block;"></span>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($conv['customer_name'] ?? 'Unknown Client'); ?>
                      </strong>
                      <small style="font-size: 10px; color: #64748b;"><?php echo date('M d', strtotime($conv['updated_at'] ?? 'now')); ?></small>
                    </div>
                    <small style="display: block; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 11px;">
                      <?php echo htmlspecialchars($conv['subject'] ?? 'Project Discussion'); ?>
                    </small>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </article>

        <!-- RIGHT PANEL: Active Chat Thread Workspace -->
        <article class="panel chat-workspace-panel">
          <?php if (!$selectedConversationId || empty($activeConversationInfo)): ?>
            <div class="empty-orders-state" style="margin: auto; padding: 40px;">
              <h3>No Conversation Selected</h3>
              <p>Choose an inbox thread from the left directory to view the message history and reply.</p>
            </div>
          <?php else: ?>
            
            <!-- CHAT HEADER INFO WITH ARCHIVE TOGGLE BUTTON -->
            <div class="chat-header-bar">
              <div>
                <p class="eyebrow">ACTIVE THREAD</p>
                <h3 style="font-size: 15px; color: #1e293b; margin-top: 2px;"><?php echo htmlspecialchars($activeConversationInfo['customer_name']); ?></h3>
                <small style="font-size: 11px; color: #64748b;"><?php echo htmlspecialchars($activeConversationInfo['email'] ?? ''); ?> · <?php echo htmlspecialchars($activeConversationInfo['subject'] ?? ''); ?></small>
              </div>
              
              <div style="display: flex; gap: 10px; align-items: center;">
                <span class="type-badge badge-custom">Online Portal</span>
                
                <!-- Archive / Unarchive Quick Action Form -->
                <form action="messages.php?tab=<?php echo htmlspecialchars($currentTab); ?>&conversation_id=<?php echo urlencode($selectedConversationId); ?>" method="POST">
                  <input type="hidden" name="action" value="toggle_archive">
                  <input type="hidden" name="conversation_id" value="<?php echo htmlspecialchars($selectedConversationId); ?>">
                  <?php $isCurrentlyArchived = !empty($activeConversationInfo['is_archived']) && $activeConversationInfo['is_archived'] == 1; ?>
                  <input type="hidden" name="target_state" value="<?php echo $isCurrentlyArchived ? '0' : '1'; ?>">
                  <button type="submit" class="back-to-projects-btn" style="cursor: pointer; border: none;">
                    <?php echo $isCurrentlyArchived ? 'Restore to Inbox' : 'Archive Thread'; ?>
                  </button>
                </form>
              </div>
            </div>

            <!-- CHAT MESSAGES THREAD BUBBLES -->
            <div class="chat-thread-container">
              <?php if (empty($activeMessages)): ?>
                <p style="text-align: center; color: #64748b; font-size: 12px; margin: auto;">No messages exchanged in this thread yet.</p>
              <?php else: ?>
                <?php foreach ($activeMessages as $msg): 
                  $isAdmin = ($msg['sender_type'] === 'Admin');
                ?>
                  <div class="chat-bubble <?php echo $isAdmin ? 'admin' : 'client'; ?>">
                    <p><?php echo nl2br(htmlspecialchars($msg['message_content'])); ?></p>
                    <small style="display: block; font-size: 9px; margin-top: 4px; opacity: 0.8; text-align: right;">
                      <?php echo date('M d, g:i a', strtotime($msg['sent_at'])); ?>
                    </small>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <!-- REPLY INPUT FOOTER -->
            <div class="chat-reply-footer">
              <form action="messages.php?tab=<?php echo htmlspecialchars($currentTab); ?>&conversation_id=<?php echo urlencode($selectedConversationId); ?>" method="POST" class="chat-reply-form">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="conversation_id" value="<?php echo htmlspecialchars($selectedConversationId); ?>">
                
                <input type="text" name="message_content" placeholder="Type your reply to client..." required autocomplete="off" class="chat-reply-input">
                <button type="submit" class="chat-send-btn">Send Reply</button>
              </form>
            </div>

          <?php endif; ?>
        </article>

      </div>
    </main>
  </div>
  <script src="../../JS/admin-dashboard.js"></script>
</body>
</html>