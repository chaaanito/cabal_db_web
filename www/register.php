<?php
include('config.php');
include('secure.php');
check_inject();

$error = ''; // Initialize the $error variable

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $reppassword = $_POST['reppassword'];

    // Check for already existing username
    $check_username_query = "SELECT * FROM cabal_auth_table WHERE ID = ?";
    $params = array($username);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($conn, $check_username_query, $params, $options);
    $check_username = sqlsrv_num_rows($stmt);

    if ($check_username != 0) {
        echo "<font color='red'><b>This username is used by another player</b></font> <br>";
        $error = 'Yes';
    }

    // Check repeat password
    if ($password != $reppassword) {
        echo "<font color='red'><b>Your Passwords must be the same</b></font> <br>";
        $error = 'Yes';
    }

    // Check for empty fields
    if (empty($username) || empty($password) || empty($reppassword)) {
        echo "<font color='red'><b>You cannot leave empty fields</b></font> <br>";
        $error = 'Yes';
    }

    // Register if all is OK
    if ($error != 'Yes') {
        $insert_query = "INSERT INTO cabal_auth_table (ID, Password, Login, AuthType, IdentityNo) VALUES (?, pwdencrypt(?), '0', 1, '7700000000000')";
        $params = array($username, $password);
        $stmt = sqlsrv_query($conn, $insert_query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        echo "<font color='green'><b>Account $username is registered successfully.</b></font><br>";
    }

    sqlsrv_free_stmt($stmt);
}
?>
<br>
<form action='' method='POST'>
Username : <input type='text' name='username'><br>
Password : <input type='text' name='password'><br>
Repeat Password : <input type='text' name='reppassword'><br>
Email : <input type='text' name='email'><br>
Question : <input type='text' name='question'><br>
Answer : <input type='text' name='answer'><br>
<input type='submit' value='Register' name='register'>
</form>