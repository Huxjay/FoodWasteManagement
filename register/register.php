<?php
include '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $address = $_POST['address'] ?? 'Unknown';

    if (empty($latitude) || empty($longitude) || empty($address)) {
        die("Location is required. Please enable GPS and allow location access.");
    }

    // Insert location
    $location_query = "INSERT INTO locations (address, latitude, longitude) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($location_query);
    $stmt->bind_param("sdd", $address, $latitude, $longitude);
    $stmt->execute();
    $location_id = $stmt->insert_id;

    // Hash the password
    $passhash = password_hash($password, PASSWORD_BCRYPT);

    // Insert user
    $query = "INSERT INTO users (name, email, phone, passhash, role, status, location_id)
              VALUES (?, ?, ?, ?, ?, 'active', ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $name, $email, $phone, $passhash, $role, $location_id);

    if ($stmt->execute()) {
        echo "Registration successful. <a href='../Login/login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
