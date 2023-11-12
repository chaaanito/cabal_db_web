<?php
session_start();
include('config.php');
include('secure.php');
check_inject();

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $tsql = "SELECT ID, UserNum, Password FROM Account.dbo.cabal_auth_table WHERE ID = ? AND PWDCOMPARE(?, Password) = 1";

    $params = array($username, $password);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $stmt = sqlsrv_query($conn, $tsql, $params, $options);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row_count = sqlsrv_num_rows($stmt);

    // Check for empty fake login
    if (empty($username) || empty($password)) {
        $error = 'Login Failed! Please try again';
    }
    // Check if login is correct
    elseif ($row_count == 0) {
        $error = 'Login Failed! Please try again';
    }
    // If all is OK
    else {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $_SESSION['user_id'] = $row['ID'];
        $_SESSION['usernum'] = $row['UserNum'];
        header("Location: dashboard.php");
        exit;
    }

    sqlsrv_free_stmt($stmt);
}
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
        <a class="navbar-brand" href="index.php">Home</a>
        <a class="navbar-brand" href="downloads.php">Downloads</a>
        <a class="navbar-brand" href="register_stored.php">Register</a>
    </nav>
    <div class="container h-100 d-flex justify-content-center align-items-center">
        <div class="login-container">
            <h3 class="text-center">Cabal Origin</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login" name="login">
                </div>
            </form>
            <p class="text-center">Don't have an account? <a href="register_stored.php">Register</a></p>
            <?php
            if (!empty($error)) {
                echo "<p class='text-center error'>$error</p>";
            }
            ?> 
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>