<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include('ShopConfig.php');
include('secure.php');
check_inject();

$userID = $_SESSION['user_id'];
$usernum = $_SESSION['usernum'];
$cashValue = $_SESSION['cash_value'];
echo "Hello " . $userID . "!";
echo "Cash Balance: " . $cashValue;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Success Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #32CD32;
        }

        p {
            color: #333;
        }

        .button {
            padding: 10px 20px;
            background-color: #32CD32;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Item Successfully Sent!</h1>
        <p>Thank you for your transaction.</p>
        <a href="WebShop.php" class="button">Go Back to WebShop</a>
    </div>
</body>
</html>