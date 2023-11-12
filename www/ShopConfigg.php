
<?php
$shopServerName = "100.118.188.173"; // IP of DB machine
$shopConnectionOptions = array(
    "Database" => "CabalCash", // Database name
    "Uid" => "sa", // MSSQL username
    "PWD" => "Jbgj_Jbgj" // MSSQL password
);

$conn = sqlsrv_connect($shopServerName, $shopConnectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>