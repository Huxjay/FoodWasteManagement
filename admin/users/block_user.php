<?php 
session_start();
include_once("../../db_config.php");

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login/login.php");
    exit();
}

// Auto logout after 15 min
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Process block/unblock request
if (isset($_GET['id']) && isset($_GET['action'])) {
    $userId = intval($_GET['id']);
    $action = strtolower($_GET['action']); // "block" or "unblock"

    if ($action !== 'block' && $action !== 'unblock') {
        die("Invalid action.");
    }

    // Use consistent casing as in database checks
    $newStatus = ($action === 'block') ? 'Blocked' : 'Active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);

    if ($stmt->execute()) {
        // Redirect back to the correct manage page
        $roleCheck = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $roleCheck->bind_param("i", $userId);
        $roleCheck->execute();
        $roleResult = $roleCheck->get_result();
        $user = $roleResult->fetch_assoc();
        $roleCheck->close();

        if ($user) {
            header("Location: manage_" . $user['role'] . "s.php");
            exit();
        } else {
            echo "User not found.";
        }
    } else {
        echo "Failed to update status.";
    }

    $stmt->close();
} else {
    echo "Invalid parameters.";
}

$conn->close();
?>
