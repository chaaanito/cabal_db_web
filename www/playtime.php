<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include('config.php');
include('ShopConfigg.php');

$userID = $_SESSION['user_id'];
$usernum = $_SESSION['usernum'];
$cashValue = $_SESSION['cash_value'];
$confirm = "";
$outputs = "";
// Establish the database connections
$cabalConn = sqlsrv_connect($serverName, $connectionOptions);
$shopConn = sqlsrv_connect($shopServerName, $shopConnectionOptions);



if ($cabalConn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($shopConn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (isset($_POST['search'])) {
    $sql = "SELECT PlayTime FROM [dbo].[cabal_auth_table] WHERE ID = ?";
    $params = array($userID);
    $stmt = sqlsrv_query($cabalConn, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $playTime = $row['PlayTime'];
        $output = "Your Playtime, $userID: $playTime mins";
    } else {
        $output = "No record found for username $userID";
    }

    sqlsrv_free_stmt($stmt);
    $outputs = "<div>$output</div>";
}

if (isset($_POST['convert'])) {
    // Retrieve the PlayTime value
    $playTimeSql = "SELECT PlayTime FROM [dbo].[cabal_auth_table] WHERE ID = ?";
    $playTimeParams = array($userID);
    $playTimeStmt = sqlsrv_query($cabalConn, $playTimeSql, $playTimeParams);

    if ($playTimeStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($playTimeStmt)) {
        $row = sqlsrv_fetch_array($playTimeStmt, SQLSRV_FETCH_ASSOC);
        $playTime = $row['PlayTime'];
    } else {
        $playTime = 0;
    }

    sqlsrv_free_stmt($playTimeStmt);

    // Convert PlayTime to Cash and deduct from PlayTime
    $cash = $playTime;
    $playTime = 0;

    // Begin a transaction to ensure data consistency across databases
    sqlsrv_begin_transaction($cabalConn);
    sqlsrv_begin_transaction($shopConn);

    try {
        // Update the CashAccount table in the shop database
        $updateSql = "UPDATE [dbo].[CashAccount] SET Cash = Cash + ? WHERE ID = ?";
        $updateParams = array($cash, $userID);
        $updateStmt = sqlsrv_query($shopConn, $updateSql, $updateParams);
        
        if ($updateStmt === false) {
            sqlsrv_rollback($shopConn);
            throw new Exception("Error updating CashAccount table");
        }

        // Update the cabal_auth_table with the deducted PlayTime in the cabal database
        $deductSql = "UPDATE [dbo].[cabal_auth_table] SET PlayTime = ? WHERE ID = ?";
        $deductParams = array($playTime, $userID);
        $deductStmt = sqlsrv_query($cabalConn, $deductSql, $deductParams);

        if ($deductStmt === false) {
            sqlsrv_rollback($cabalConn);
            throw new Exception("Error updating cabal_auth_table");
        }
       
        // Commit the changes if everything is successful
        sqlsrv_commit($cabalConn);
        sqlsrv_commit($shopConn);

        $confirm = "<div>Conversion complete.</div><br> <div>PlayTime converted to Cash: $cash</div>";
    } catch (Exception $e) {
        // Rollback the changes if an error occurred
        sqlsrv_rollback($cabalConn);
        sqlsrv_rollback($shopConn);
        die($e->getMessage());
    }

    sqlsrv_free_stmt($updateStmt);
    sqlsrv_free_stmt($deductStmt);
}

sqlsrv_close($cabalConn);
sqlsrv_close($shopConn);
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


<form method="POST" action="">
            <div class="flex justify-center">
                <div><?php echo "Welcome, $userID !"; ?></div>
                <div><?php echo "Current Cash: $cashValue"; ?></div>
              
                <div class="login-container">
            <div class="flex justify-center">
            <h1>Playtime Converter</h1>
            <h6>(1 min = 1 Cash)</h6>
                <div><?php echo "$outputs"; ?></div>
            <div><?php echo "$confirm"; ?></div>
            </div>
            <br>
            <div class="container h-100 d-flex justify-content-center align-items-center">
        
                <button type="submit" class="navbar-brand btn btn-primary" name="search">Check Playtime</button>
                <button type="submit" class="navbar-brand btn btn-primary" name="convert">Convert</button>
            </div>
            </div>
            </div>
        </form>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
