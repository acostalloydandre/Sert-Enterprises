<?php
/**
 * SERT Enterprise - Projects Data Layer
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

// Updated phases matching your exact workflow categories
$statuses = [
    'All',
    'Planning',
    'Designing',
    'Development',
    'Site visit request',
    'Installation',
    'Delivery'
];

$selectedStatus = $_GET['status'] ?? 'All';
$selectedProjectId = $_GET['project_id'] ?? null;

$projectsDirectory = [];
$selectedProjectDetails = [];
$totalProjectsCount = 0;

if ($pdo) {
    try {
        // Total count for sidebar badge
        $totalProjectsCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    } catch (PDOException $e) {
        $totalProjectsCount = 0;
    }

    try {
        // Fetch projects list for directory based on phase filter 
        // Note: Assumes your projects table uses a 'status' or 'phase' column. 
        // If your database column is named 'phase' instead of 'status', change status = ? to phase = ? below:
        if ($selectedStatus === 'All') {
            $stmt = $pdo->query("SELECT project_id, project_name, customer_name, progress, status FROM projects ORDER BY deadline DESC");
            $projectsDirectory = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare("SELECT project_id, project_name, customer_name, progress, status FROM projects WHERE status = ? ORDER BY deadline DESC");
            $stmt->execute([$selectedStatus]);
            $projectsDirectory = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        $projectsDirectory = [];
    }

    // Default to the first/latest project if none is selected
    if (!$selectedProjectId && !empty($projectsDirectory)) {
        $selectedProjectId = $projectsDirectory[0]['project_id'];
    }

    // Fetch full details for the selected project
    if ($selectedProjectId) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = ?");
            $stmt->execute([$selectedProjectId]);
            $selectedProjectDetails = $stmt->fetch();
        } catch (PDOException $e) {
            $selectedProjectDetails = [];
        }
    }
}
?>