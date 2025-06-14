<?php 
session_start();
include_once("../../db_config.php");

//  Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

//  Login and role check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login/login.php");
    exit();
}

// ⏳ Auto logout
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// 🔍 Fetch customers
$sql = "SELECT id, name, email, phone, status FROM users WHERE role = 'customer'";
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
            margin-top: 20px;
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
            margin: 2px;
            display: inline-block;
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
        .unblock-btn {
            background-color: green;
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

    <?php while ($row = mysqli_fetch_assoc($result)) { 
        $status = strtolower($row['status']); 
    ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($status === 'blocked') { ?>
                    <a href="block_user.php?id=<?= $row['id'] ?>&action=unblock" class="button unblock-btn" onclick="return confirm('Unblock this customer?')">Unblock</a>
                <?php } elseif ($status !== 'deleted') { ?>
                    <a href="block_user.php?id=<?= $row['id'] ?>&action=block" class="button block-btn" onclick="return confirm('Block this customer?')">Block</a>
                <?php } ?>

                <?php if ($status !== 'deleted') { ?>
                    <!-- <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a> -->

                <?php } else { ?>
                    <!-- <span style="color: grey;">Removed</span> -->
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
