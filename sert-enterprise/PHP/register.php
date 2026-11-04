
<?php
require_once "db_connect.php";

// Only accept form submissions
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

// Get the submitted information
$email = trim($_POST["email"] ?? "");
$name = trim($_POST["name"] ?? "");
$password = $_POST["password"] ?? "";
$phone = trim($_POST["phone"] ?? "");
$address = trim($_POST["address"] ?? "");

$marketingConsent = isset($_POST["marketing-consent"]) ? 1 : 0;

// Validate required fields
if (
    empty($email) ||
    empty($name) ||
    empty($password) ||
    empty($phone) ||
    empty($address)
) {
    exit("Please complete all required fields.");
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Please enter a valid email address.");
}

// Require at least 8 characters
if (strlen($password) < 8) {
    exit("Password must contain at least 8 characters.");
}

// Securely hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL statement
$sql = "INSERT INTO customers
        (email, full_name, password, phone, address, marketing_consent)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Unable to process registration. Please try again.");
}

$stmt->bind_param(
    "sssssi",
    $email,
    $name,
    $hashedPassword,
    $phone,
    $address,
    $marketingConsent
);

// Save customer information
if ($stmt->execute()) {
    echo "<h2>Registration Successful!</h2>";
    echo "<p>Your account has been created.</p>";
    echo '<a href="../HTML/signup.html">Back to Sign Up</a>';
} else {
    if ($stmt->errno === 1062) {
        echo "<h2>Email Already Registered</h2>";
        echo "<p>This email address is already in use.</p>";
    } else {
        echo "<h2>Registration Failed</h2>";
        echo "<p>Something went wrong. Please try again.</p>";
    }

    echo '<a href="../HTML/signup.html">Back to Sign Up</a>';
}

$stmt->close();
$conn->close();
?>
