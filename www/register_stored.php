<?php
include('config.php');
include('secure.php');
check_inject();

$ip = $_SERVER['REMOTE_ADDR'];
$message ="";
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $reppassword = $_POST['reppassword'];
    $email = $_POST['email'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $error = '';
    // Check for already existing username
    $check_username_query = "SELECT * FROM cabal_auth_table WHERE ID = ?";
    $params = array($username);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($conn, $check_username_query, $params, $options);
    $check_username = sqlsrv_num_rows($stmt);

    if ($check_username != 0) {
        $message = "<font color='red'><b>This username is used by another player</b></font> <br>";
        $error = 'Yes';
    }

    // Check repeat password
    if ($password != $reppassword) {
        $message =   "<font color='red'><b>Your Passwords must be the same</b></font> <br>";
        $error = 'Yes';
    }

    // Check for empty fields
    if (empty($username) || empty($password) || empty($reppassword) || empty($email) || empty($question) || empty($answer)) {
        $message =  "<font color='red'><b>You cannot leave empty fields</b></font> <br>";
        $error = 'Yes';
    }

    // Register if all is OK
    if ($error != 'Yes') {
        $register_query = "EXECUTE Account.dbo.cabal_tool_registerAccount_web ?, ?, ?, ?, ?, ?";
        $params = array($username, $password, $email, $question, $answer, $ip);
        $stmt = sqlsrv_query($conn, $register_query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $message = "<font color='green'><b>Account $username is registered successfully.</b></font><br>";
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
            <h3 class="text-center">Registration</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="reppassword" placeholder="Repeat Password" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="email" required>
                </div>
                <div class="form-group">
                <select class="form-control" name="question" required>
                    <option value="" disabled selected>Select a Secret Question</option>
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What is the name of your first pet?">What is the name of your first pet?</option>
                    <option value="What is your favorite book?">What is your favorite book?</option>
                    <option value="What city were you born in?">What city were you born in?</option>
                </select>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="answer" placeholder="Secret Answer" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Register" name="register">
                </div>
                
                
            </form>
            <?php echo $message;?>
            
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
