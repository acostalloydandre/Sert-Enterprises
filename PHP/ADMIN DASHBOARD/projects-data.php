<?php
/**
 * SERT Enterprise - Projects Data Layer (Custom Orders Tracker)
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

// Phase Progress Mapping for Custom Workflows
$customPhaseMap = [
    'Planning'                => 20,
    'Designing'               => 40,
    'Development'             => 60,
    'Site Visit Request'      => 80,
    'Installation'            => 90,
    'Delivery Complete'       => 100
];

$teamMembers = ['Unassigned', 'Lloyd Acosta', 'Juan Dela Cruz', 'Maria Clara', 'Mark Reyes'];
$successMessage = '';

$selectedPhaseFilter = $_GET['phase'] ?? 'All';
$selectedProjectId = $_GET['project_id'] ?? null;

// Helper function to map phase names to clean CSS classes
function getPhaseClass($phase) {
    switch ($phase) {
        case 'Planning': return 'planning';
        case 'Designing': return 'designing';
        case 'Development': return 'development';
        case 'Site Visit Request': return 'site-visit';
        case 'Installation': return 'installation';
        case 'Delivery Complete': return 'delivery';
        default: return 'planning';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';

    // Handle Team Assignment
    if ($action === 'assign_employee') {
        $assignee = $_POST['assigned_employee'] ?? 'Unassigned';
        $projIdToUpdate = $_POST['project_id'] ?? null;

        if ($projIdToUpdate) {
            $stmt = $pdo->prepare("UPDATE projects SET assigned_employee = ? WHERE project_id = ?");
            $stmt->execute([$assignee, $projIdToUpdate]);
            $successMessage = "Team member assigned successfully!";
            $selectedProjectId = $projIdToUpdate;
        }
    }

    // Handle Phase Stage Advance & Progress Calculation
    if ($action === 'update_phase') {
        $projIdToUpdate = $_POST['project_id'] ?? null;
        $newPhase = $_POST['current_phase'] ?? '';

        $progress = $customPhaseMap[$newPhase] ?? 0;
        $overallStatus = ($newPhase === 'Delivery Complete') ? 'Completed' : 'Ongoing';

        if ($projIdToUpdate && !empty($newPhase)) {
            $stmt = $pdo->prepare("UPDATE projects SET current_phase = ?, progress = ?, status = ? WHERE project_id = ?");
            $stmt->execute([$newPhase, $progress, $overallStatus, $projIdToUpdate]);
            $successMessage = "Project stage updated to '{$newPhase}' ({$progress}% Complete)!";
            $selectedProjectId = $projIdToUpdate;
        }
    }
}

$projectsList = [];
$selectedProjectDetails = [];

if ($pdo) {
    try {
        $sql = "SELECT * FROM projects WHERE order_type = 'Custom'";
        $params = [];

        if ($selectedPhaseFilter !== 'All') {
            $sql .= " AND current_phase = ?";
            $params[] = $selectedPhaseFilter;
        }

        $sql .= " ORDER BY deadline ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $projectsList = $stmt->fetchAll();

        if ($selectedProjectId) {
            $detStmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = ? AND order_type = 'Custom'");
            $detStmt->execute([$selectedProjectId]);
            $selectedProjectDetails = $detStmt->fetch();
        }
    } catch (PDOException $e) {
        $projectsList = [];
    }
}
?>