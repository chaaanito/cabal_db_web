<?php
$serverName = "172.168.9.41";
$connectionOptions = array(
    "Database" => "Account",
    "UID" => "sa",
    "PWD" => "Jbgj_Jbgj"
);

$mssql = sqlsrv_connect($serverName, $connectionOptions);

function mssql_escape_string($str)
{
    if (ctype_alnum($str))
        return $str;
    else
        return str_ireplace(array(';', '%', "'"), "", $str);
}

foreach ($_GET as $key => $getvar) {
    $_GET[$key] = mssql_escape_string($getvar);
}
foreach ($_POST as $key => $postvar) {
    $_POST[$key] = mssql_escape_string($postvar);
}

if (!isset($_POST['reg_username']))
    $_POST['reg_username'] = '';

sqlsrv_query($mssql, 'USE [ACCOUNT]');
$checkacc = sqlsrv_query($mssql, 'SELECT COUNT(*) as count FROM [cabal_auth_table] WHERE ID=\'' . mssql_escape_string($_POST['reg_username']) . '\'');
$errors = array();

if (empty($_POST['reg_username']) || empty($_POST['reg_password']))
    $errors[] = 'You must fill out all fields.';

if (!empty($_POST['reg_username']) && sqlsrv_fetch_array($checkacc)['count'] > 0)
    $errors[] = '' . $_POST['reg_username'] . ' is already in use.';

if (!empty($_POST['reg_username']) && (strlen($_POST['reg_username']) > 16 || strlen($_POST['reg_username']) < 6))
    $errors[] = 'Usernames must be between 6 - 16 characters.';

if (!empty($_POST['reg_password']) && (strlen($_POST['reg_password']) > 16 || strlen($_POST['reg_password']) < 6))
    $errors[] = 'Passwords must be between 6 - 16 characters.';


if (isset($_POST['reg_submit'])) {
    if (count($errors) > 0) {
        echo '<div class="fail">';
        foreach ($errors as $error) {
            echo $error . '<br/>';
        }
        echo '</div>';
    } else {
        sqlsrv_query($mssql, 'EXEC [dbo].[cabal_tool_registerAccount]"' . mssql_escape_string($_POST['reg_username']) . '","' . mssql_escape_string($_POST['reg_password']) . '"');
        echo '<div class="success">Your account has been successfully created.</div>';
    }
}
?>
