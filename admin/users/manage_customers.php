<?php
include_once("db_config.php");
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.html");
    exit();
}

// Fetch customers
$sql = "SELECT * FROM customer"; 
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Customers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        a.button {
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        a.button:hover {
            background-color: #2980b9;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .block-btn {
            background-color: #f39c12;
        }
    </style>
</head>
<body>

<h2>Manage Customers</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['customer_id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
            <?php if ($row['status'] === 'Blocked') { ?>
    <a href="block_user.php?type=customer&id=<?= $row['customer_id'] ?>&action=unblock" class="button" style="background-color: green;" onclick="return confirm('Unblock this customer?')">Unblock</a>
<?php } else { ?>
    <a href="block_user.php?type=customer&id=<?= $row['customer_id'] ?>&action=block" class="button block-btn" onclick="return confirm('Block this customer?')">Block</a>
<?php } ?>

<a href="delete_user.php?type=customer&id=<?= $row['customer_id'] ?>" class="button delete-btn" onclick="return confirm('Delete this customer?')">Remove</a>

            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
