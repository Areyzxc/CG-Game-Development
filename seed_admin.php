<?php
// seed_admin.php
// One‐off script to bulk‐insert admin accounts.
// Run this once, then remove or secure it.

include 'db_connection.php';  // $conn = new mysqli(...)

// Define your six proponents here:
$admins = [
    ['username' => 'James Aries', 'email' => 'aries@codegame.dev',   'password' => 'Areys27'],
    ['username' => 'Bob',   'email' => 'bob@codegame.dev',     'password' => 'BobPass123'],
    ['username' => 'Carol', 'email' => 'carol@codegame.dev',   'password' => 'CarolPass123'],
    ['username' => 'Dave',  'email' => 'dave@codegame.dev',    'password' => 'DavePass123'],
    ['username' => 'Eve',   'email' => 'eve@codegame.dev',     'password' => 'EvePass123'],
    ['username' => 'Frank', 'email' => 'frank@codegame.dev',   'password' => 'FrankPass123'],
];

$stmt = $conn->prepare("
    INSERT INTO admin_users (username, email, password_hash, role, created_at)
    VALUES (?, ?, ?, 'super_admin', NOW())
");
if ( ! $stmt ) {
    die("Prepare failed: " . $conn->error);
}

foreach ($admins as $admin) {
    $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
    $stmt->bind_param("sss",
        $admin['username'],
        $admin['email'],
        $hash
    );
    if ($stmt->execute()) {
        echo "Created admin: {$admin['username']} ({$admin['email']})<br>";
    } else {
        echo "Error creating {$admin['username']}: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
