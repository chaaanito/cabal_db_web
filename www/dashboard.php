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
$welcome = "Welcome " . $userID . "!";

// Function to query Cash value for a given user ID
function getCashValue($userID) {
    try {
        // Establish MSSQL connection (assumes it's defined in ShopConfig.php)
        global $conn; // Assuming the connection is stored in the $conn variable
        
        // Query the CashAccount table
        $query = "SELECT Cash FROM dbo.CashAccount WHERE ID = ?";
        $params = array($userID);
        $stmt = sqlsrv_query($conn, $query, $params);
        
        if ($stmt === false) {
            throw new Exception("Error while querying the CashAccount table: " . sqlsrv_errors());
        }
        
        // Fetch the Cash value
        if (sqlsrv_fetch($stmt) === false) {
            throw new Exception("No matching record found for the user ID '$userID'");
        }
        
        $cashValue = sqlsrv_get_field($stmt, 0);
        
        // Close the statement
        sqlsrv_free_stmt($stmt);
        
        // Return the Cash value
        return $cashValue;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


// Usage example

$cashValue = getCashValue($userID);
$_SESSION['cash_value'] = $cashValue;
$myCash = "Remaining Cash: " . $cashValue;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cabal Origin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="dashboard.php">Home</a>
    <a class="navbar-brand" href="WebShop.php">WebShop</a>
    <a class="navbar-brand" href="playtime.php">Playtime</a>
    
    <form class="ml-auto" action="logout.php" method="POST">
        <input type="submit" class="navbar-brand btn btn-primary" value="Logout">
    </form>
</nav>

<span><?php echo $welcome; ?></span>
<br>
<span><?php echo $myCash; ?></span>


    <div class="container h-100 d-flex justify-content-center align-items-center">
        <div class="login-container">
            <h3 class="text-center">Characters</h3>
            <form>
            <span><?php include('dashCharacters.php'); ?></span>
            </form>
           
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>




