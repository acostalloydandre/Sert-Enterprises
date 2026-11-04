<?php
session_start();

require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (empty($email) || empty($password)) {
    exit("Please enter your email and password.");
}

$sql = "SELECT customer_id, full_name, email, password
        FROM customers
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Something went wrong. Please try again.");
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $customer = $result->fetch_assoc();

    if (password_verify($password, $customer["password"])) {

        session_regenerate_id(true);

        $_SESSION["customer_id"] = $customer["customer_id"];
        $_SESSION["customer_name"] = $customer["full_name"];
        $_SESSION["customer_email"] = $customer["email"];

        header("Location: ../HTML/dashboard.php");
        exit();

    }
}

$stmt->close();
$conn->close();

exit("Incorrect email or password.");
?>
