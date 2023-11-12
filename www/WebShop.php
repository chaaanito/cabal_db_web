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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the selected item from the form
    $itemSelection = $_POST['itemSelection'];

    // Load the itemData from JSON file
    $itemDataFile = './CashShop/itemData.json';
    $itemData = json_decode(file_get_contents($itemDataFile), true);

    // Find the selected item in the itemData array
    $selectedItem = null;
    foreach ($itemData as $item) {
        if ($item['value'] === $itemSelection) {
            $selectedItem = $item;
            break;
        }
    }

    // Check if the selected item exists
    if ($selectedItem) {
        // Retrieve the necessary parameters from the selected item
        $itemIdx = $selectedItem['itemidx'];
        $itemOpt = $selectedItem['itemopt'];
        $tranNo = 1;
        $serverIdx = 1;
        $durationIdx = 31;
        $itemPrice = $selectedItem['price'];

        // Check if the user has enough cash to purchase the item
        if ($cashValue >= $itemPrice) {
            // Deduct the item price from the cash value
            $cashValue -= $itemPrice;

            // Update the cash value in the session
            $_SESSION['cash_value'] = $cashValue;

            // Establish a connection to the database
            $conn = sqlsrv_connect($serverName, $connectionOptions);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // Execute the stored procedure
            $tsql = " DECLARE @return_value INT; EXEC @return_value = [dbo].[up_AddMyCashItemByItem] @UserNum = ?, @TranNo = ?, @ServerIdx = ?, @ItemIdx = ?, @ItemOpt = ?, @DurationIdx = ?; SELECT @return_value AS ReturnValue; ";
            $params = array($usernum, $tranNo, $serverIdx, $itemIdx, $itemOpt, $durationIdx);
            $stmt = sqlsrv_query($conn, $tsql, $params);

            if ($stmt) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                if (isset($row['ReturnValue'])) {
                    $returnValue = $row['ReturnValue'];
                    // echo "Stored procedure executed successfully. Return value: " . $returnValue;
                } else {
                    // echo "Stored procedure executed successfully.";
                }

                // Update the cash value in the database
                $updateCashQuery = "UPDATE dbo.CashAccount SET Cash = ? WHERE UserNum = ?";
                $updateCashParams = array($cashValue, $usernum);
                $updateCashStmt = sqlsrv_query($conn, $updateCashQuery, $updateCashParams);

                if ($updateCashStmt) {
                    // echo "Cash value updated successfully in the database.";
                    $_SESSION['cash_value'] = $cashValue; // Update the cash value in the session after updating in the database
                } else {
                    // echo "Failed to update cash value in the database.";
                }

                sqlsrv_free_stmt($stmt);

                // Redirect to the confirmation page
                header("Location: success.php");
                exit;
            } else {
                echo "Failed to execute stored procedure.";
            }

            sqlsrv_close($conn);
        } else {
            echo "Insufficient cash to purchase the item.";
        }
    } else {
        echo "Selected item does not exist.";
    }
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
    <a class="navbar-brand" href="dashboard.php">Home</a>
    <a class="navbar-brand" href="WebShop.php">WebShop</a>
    <a class="navbar-brand" href="playtime.php">Playtime</a>
    <form class="ml-auto" action="logout.php" method="POST">
        <input type="submit" class="navbar-brand btn btn-primary" value="Logout">
    </form>
</nav>

<?php if ($userID === "ADMIN" || $userID === "killyou" || $userID === "option3"): ?>
        <div><?php include 'itemCSV.php' ?></div>
    <?php endif; ?>
<div class="container h-100 d-flex justify-content-center align-items-center">
    <div class="login-container">
        <h3 class="text-center">Cash Shop</h3>
        <form action="WebShop.php" method="POST">
            <div class="output">
                

                <div class="select-container">
                    <select class="custom-select" name="itemSelection" id="itemSelection"></select>
                    <div class="select-grid"></div>
                </div>
            </div>
            <div class="buy-button">
            <span><?php echo "Cash Balance: " . $cashValue; ?></span>
            <div>
            
            </div>
            </div>
        </form>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>








    




<script type="module">
import itemData from './CashShop/itemData.js';

const select = document.getElementById('itemSelection');
const selectGrid = document.querySelector('.select-grid');

// Populate select options and grid elements
itemData.forEach(item => {
    // Create select option
    const selectOption = document.createElement('option');
    selectOption.value = item.value;
    selectOption.dataset.itemidx = item.itemidx;
    selectOption.dataset.itemopt = item.itemopt;
    selectOption.dataset.price = item.price; // Include the price dataset attribute
    selectOption.textContent = `${item.label} - Price: ${item.price}`;
    select.appendChild(selectOption);

    // Create grid option
    const gridOption = document.createElement('div');
    gridOption.className = 'select-option';
    gridOption.dataset.value = item.value;
    gridOption.dataset.itemidx = item.itemidx;
    gridOption.dataset.itemopt = item.itemopt;
    gridOption.dataset.price = item.price; // Include the price dataset attribute

    const img = document.createElement('img');
    img.src = item.img;
    img.alt = '';
    gridOption.appendChild(img);

    const span = document.createElement('span');
    span.textContent = `${item.label} - Price: ${item.price}`;
    span.classList.add('itemTag');
    gridOption.appendChild(span);

    // Create buy button
        const buyButton = document.createElement('button');
        buyButton.textContent = 'Buy';
        buyButton.classList.add('btn', 'btn-primary');
        gridOption.appendChild(buyButton);

    selectGrid.appendChild(gridOption);
});

// JavaScript to synchronize the selected option
const selectOptions = Array.from(selectGrid.children);

selectOptions.forEach(option => {
    option.addEventListener('click', () => {
        const value = option.getAttribute('data-value');
        select.value = value;

        selectOptions.forEach(opt => {
            opt.classList.remove('selected');
        });

        option.classList.add('selected');
    });
});

// JavaScript to maintain selection focus
select.addEventListener('change', () => {
    const selectedOption = selectOptions.find(option => option.getAttribute('data-value') === select.value);

    selectOptions.forEach(option => {
        option.classList.remove('selected');
    });

    selectedOption.classList.add('selected');
});
</script>