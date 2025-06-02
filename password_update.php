<?php
include 'db_config.php'; // adjust the path if needed

// Fetch users who do not have hashed passwords yet
$result = $conn->query("SELECT id, passhash FROM users");

while ($user = $result->fetch_assoc()) {
    $id = $user['id'];
    $currentPass = $user['passhash'];

    // Check if password is already hashed (starts with $2y$)
    if (strpos($currentPass, '$2y$') !== 0) {
        // Hash the plain password
        $hashedPassword = password_hash($currentPass, PASSWORD_DEFAULT);

        // Update the row with hashed password
        $stmt = $conn->prepare("UPDATE users SET passhash = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        $stmt->execute();
    }
}

echo "✅ Passwords hashed successfully.";
$conn->close();
?>
