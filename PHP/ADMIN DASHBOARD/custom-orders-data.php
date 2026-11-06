<?php
/**
 * SERT Enterprise - Custom Orders Data Layer
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

$categories = [
    'All',
    'Large Format Printing',
    'Panaflex Signage',
    'Acrylic Works',
    'Metal Works',
    'Wood Works',
    'Window Binds',
    'Carpets',
    'Office Furniture & Partitions'
];

$statusFilters = [
    'All',
    'Pending',
    'Site Visit Scheduled',
    'For Quotation',
    'Quotation Sent',
];

$selectedStatus = $_GET['status'] ?? 'All';
$selectedCategory = $_GET['category'] ?? 'All';
$selectedClientName = $_GET['client'] ?? null;
$successMessage = '';
$errorMessage = '';

// --- HANDLE ADMIN WORKFLOW ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';
    $orderIdToUpdate = $_POST['custom_order_id'] ?? null;

    if ($orderIdToUpdate) {
        try {
            if ($action === 'approve_and_schedule') {
                $siteVisitDate = $_POST['site_visit_date'] ?? null;
                $adminNotes = $_POST['admin_notes'] ?? '';

                $stmt = $pdo->prepare("UPDATE custom_orders SET status = 'Site Visit Scheduled', site_visit_date = ?, admin_notes = ? WHERE custom_order_id = ?");
                $stmt->execute([$siteVisitDate, $adminNotes, $orderIdToUpdate]);
                $successMessage = "Custom order approved and site visit successfully scheduled!";
            } 
            else if ($action === 'mark_visit_successful') {
                $stmt = $pdo->prepare("UPDATE custom_orders SET status = 'For Quotation' WHERE custom_order_id = ?");
                $stmt->execute([$orderIdToUpdate]);
                $successMessage = "Site visit marked successful! Order moved to Pending Quotation.";
            } 
            else if ($action === 'issue_quotation') {
                $quoteAmount = $_POST['estimated_quote'] ?? '0.00';
                $quoteNotes = $_POST['quotation_notes'] ?? '';

                $stmt = $pdo->prepare("UPDATE custom_orders SET estimated_quote = ?, admin_notes = ?, status = 'Quotation Sent' WHERE custom_order_id = ?");
                $stmt->execute([$quoteAmount, $quoteNotes, $orderIdToUpdate]);
                $successMessage = "Quotation issued and sent to client successfully!";
            }
            else if ($action === 'confirm_downpayment_and_graduate') {
                // 1. Mark custom order as Approved / Completed
                $stmt = $pdo->prepare("UPDATE custom_orders SET status = 'Approved' WHERE custom_order_id = ?");
                $stmt->execute([$orderIdToUpdate]);

                // 2. Fetch order details to graduate into the projects table
                $fetchStmt = $pdo->prepare("SELECT * FROM custom_orders WHERE custom_order_id = ?");
                $fetchStmt->execute([$orderIdToUpdate]);
                $orderData = $fetchStmt->fetch();

                if ($orderData) {
                    $projId = 'PRJ-' . date('Y') . '-' . rand(1000, 9999);
                    $projName = $orderData['category'] . ' Fabrication';
                    $deadline = date('Y-m-d', strtotime('+30 days'));

                    $insertProj = $pdo->prepare("INSERT INTO projects (project_id, project_name, customer_name, order_type, current_phase, progress, status, deadline, assigned_employee) VALUES (?, ?, ?, 'Custom', 'Planning', 20, 'Ongoing', ?, 'Unassigned')");
                    $insertProj->execute([$projId, $projName, $orderData['customer_name'], $deadline]);
                }

                $successMessage = "Downpayment confirmed! Order has successfully graduated to the Projects Tracker.";
            }
            else if ($action === 'reject_order') {
                $rejectReason = $_POST['admin_notes'] ?? 'Order rejected by admin.';
                $stmt = $pdo->prepare("UPDATE custom_orders SET status = 'Rejected', admin_notes = ? WHERE custom_order_id = ?");
                $stmt->execute([$rejectReason, $orderIdToUpdate]);
                $successMessage = "Custom order has been rejected and moved to archives.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error processing action: " . $e->getMessage();
        }
    }
}

$clients = [];
$selectedClientCustomOrders = [];

if ($pdo) {
    try {
        $sql = "SELECT DISTINCT customer_name, COUNT(custom_order_id) as total_requests FROM custom_orders WHERE 1=1";
        $params = [];

        if ($selectedStatus !== 'All') {
            $sql .= " AND status = ?";
            $params[] = $selectedStatus;
        }

        if ($selectedCategory !== 'All') {
            $sql .= " AND category = ?";
            $params[] = $selectedCategory;
        }

        $sql .= " GROUP BY customer_name ORDER BY customer_name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $clients = $stmt->fetchAll();
    } catch (PDOException $e) {
        $clients = [];
    }

    if (!$selectedClientName && !empty($clients)) {
        $selectedClientName = $clients[0]['customer_name'];
    }

    if ($selectedClientName) {
        try {
            $sql = "SELECT * FROM custom_orders WHERE customer_name = ?";
            $params = [$selectedClientName];

            if ($selectedStatus !== 'All') {
                $sql .= " AND status = ?";
                $params[] = $selectedStatus;
            }

            if ($selectedCategory !== 'All') {
                $sql .= " AND category = ?";
                $params[] = $selectedCategory;
            }

            $sql .= " ORDER BY request_date DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $selectedClientCustomOrders = $stmt->fetchAll();
        } catch (PDOException $e) {
            $selectedClientCustomOrders = [];
        }
    }
}
?>