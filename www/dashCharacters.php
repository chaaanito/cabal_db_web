<?php


if (!isset($_SESSION['user_id'])) {
   header("Location: index.php");
   exit;
}

include('config.php');
include('Server01Config.php');

$usernum = $_SESSION['usernum'];
$newUserID = intval($usernum) * 8;

function getCharacters($startUserID, $count)
{
    global $conn;

    $query = "SELECT Name, LEV, Alz, STR, DEX, INT, PNT, PlayTime FROM dbo.cabal_character_table WHERE CharacterIdx >= ? AND CharacterIdx <= ?";
    $params = array($startUserID, $startUserID + $count - 1);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $characterData = array();

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $characterData[] = $row;
    }

    sqlsrv_free_stmt($stmt);

    return $characterData;
}

$characterData = getCharacters($newUserID, 8);

if (is_array($characterData)) {
    echo "<form action='update_points.php' method='post'>";
    echo "<table class='table table-striped'>";
    echo "<tr><th>Name</th><th>Level</th><th>Alz</th><th>STR</th><th>DEX</th><th>INT</th><th>PNT</th><th>PlayTime</th></tr>";
    foreach ($characterData as $character) {
        echo "<tr>";
        echo "<td>" . $character['Name'] . "</td>";
        echo "<td>" . $character['LEV'] . "</td>";
        echo "<td>" . $character['Alz'] . "</td>";
        echo "<td>" . $character['STR'] . "</td>";
        echo "<td>" . $character['DEX'] . "</td>";
        echo "<td>" . $character['INT'] . "</td>";
        echo "<td>" . $character['PNT'] . "</td>";
        echo "<td>" . $character['PlayTime'] . "</td>";
        echo "</tr>";
    }
   
} else {
    echo "Error: Unable to retrieve character data.";
}
?>