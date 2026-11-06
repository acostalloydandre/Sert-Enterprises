<?php
/**
 * SERT Enterprise - Messages Data Layer (With Unread & Archived Support)
 */
date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$dbname = 'sert_enterprise';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    $pdo = null;
}

$selectedConversationId = $_GET['conversation_id'] ?? null;
$currentTab = $_GET['tab'] ?? 'inbox'; // 'inbox', 'unread', or 'archived'
$conversationsList = [];
$activeMessages = [];
$activeConversationInfo = null;
$successMessage = '';

if ($pdo) {
    // Handle Actions (Send Reply, Archive/Unarchive)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $convId = $_POST['conversation_id'] ?? null;

        if ($action === 'send_message' && $convId) {
            $replyText = trim($_POST['message_content'] ?? '');
            if (!empty($replyText)) {
                $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_type, message_content, sent_at) VALUES (?, 'Admin', ?, NOW())");
                $stmt->execute([$convId, $replyText]);

                // Mark conversation as read for admin & update timestamp
                $updateStmt = $pdo->prepare("UPDATE conversations SET updated_at = NOW(), is_unread = 0 WHERE conversation_id = ?");
                $updateStmt->execute([$convId]);

                $successMessage = "Reply sent successfully!";
                $selectedConversationId = $convId;
            }
        }

        if ($action === 'toggle_archive' && $convId) {
            $newArchiveState = (int)($_POST['target_state'] ?? 0);
            $archStmt = $pdo->prepare("UPDATE conversations SET is_archived = ? WHERE conversation_id = ?");
            $archStmt->execute([$newArchiveState, $convId]);
            $successMessage = $newArchiveState ? "Conversation archived." : "Conversation restored to inbox.";
            $selectedConversationId = $convId;
        }
    }

    try {
        // Fetch conversations list based on active tab filter
        $convQuery = "SELECT c.*, cust.customer_name, cust.email FROM conversations c LEFT JOIN customers cust ON c.customer_id = cust.customer_id";
        
        if ($currentTab === 'unread') {
            $convQuery .= " WHERE c.is_unread = 1 AND (c.is_archived = 0 OR c.is_archived IS NULL)";
        } elseif ($currentTab === 'archived') {
            $convQuery .= " WHERE c.is_archived = 1";
        } else {
            // Default 'inbox' tab shows active unarchived chats
            $convQuery .= " WHERE (c.is_archived = 0 OR c.is_archived IS NULL)";
        }

        $convQuery .= " ORDER BY c.updated_at DESC";
        $stmt = $pdo->prepare($convQuery);
        $stmt->execute();
        $conversationsList = $stmt->fetchAll();

        // Auto-select first conversation if none specified
        if (!$selectedConversationId && !empty($conversationsList)) {
            $selectedConversationId = $conversationsList[0]['conversation_id'];
        }

        // If a conversation is selected, mark it as read and load messages
        if ($selectedConversationId) {
            $readStmt = $pdo->prepare("UPDATE conversations SET is_unread = 0 WHERE conversation_id = ?");
            $readStmt->execute([$selectedConversationId]);

            $infoStmt = $pdo->prepare("SELECT c.*, cust.customer_name, cust.email, cust.phone FROM conversations c LEFT JOIN customers cust ON c.customer_id = cust.customer_id WHERE c.conversation_id = ?");
            $infoStmt->execute([$selectedConversationId]);
            $activeConversationInfo = $infoStmt->fetch();

            $msgStmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = ? ORDER BY sent_at ASC");
            $msgStmt->execute([$selectedConversationId]);
            $activeMessages = $msgStmt->fetchAll();
        }
    } catch (PDOException $e) {
        $conversationsList = [];
        $activeMessages = [];
    }
}
?>