
<?php
session_start();

// Prevent access if the customer is not logged in
if (!isset($_SESSION["customer_id"])) {
    header("Location: login.html");
    exit();
}

$customerName = htmlspecialchars($_SESSION["customer_name"]);
$customerEmail = htmlspecialchars($_SESSION["customer_email"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SERT Enterprise - Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f4f7f5;
            color: #26352e;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 7%;
            background: #173f35;
            color: white;
        }

        header h2 {
            margin: 0;
        }

        .logout-button {
            padding: 10px 20px;
            border-radius: 7px;
            background: white;
            color: #173f35;
            font-weight: bold;
            text-decoration: none;
        }

        main {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 24px;
        }

        .welcome-card {
            padding: 35px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,.06);
        }

        .welcome-card h1 {
            color: #173f35;
        }

        .account-card {
            margin-top: 25px;
            padding: 25px;
            border-radius: 15px;
            background: white;
        }

        .account-card h3 {
            color: #173f35;
        }

        @media (max-width: 600px) {
            header {
                padding: 18px 5%;
            }

            main {
                margin-top: 25px;
            }

            .welcome-card {
                padding: 24px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>SERT Enterprise</h2>
    <a href="../PHP/logout.php" class="logout-button">
        Log out
    </a>
</header>

<main>

    <section class="welcome-card">
        <h1>Welcome, <?php echo $customerName; ?>!</h1>

        <p>
            We're glad to have you at SERT Enterprise.
            Manage your requests and monitor your projects here.
        </p>
    </section>

    <section class="account-card">
        <h3>Account Information</h3>

        <p>
            <strong>Name:</strong>
            <?php echo $customerName; ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo $customerEmail; ?>
        </p>
    </section>

</main>

</body>
</html>
