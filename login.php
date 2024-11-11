<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start the session

// Include database configuration
require 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // Check if "Remember Me" was checked

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedIn'] = true; // Set session variable
        $_SESSION['username'] = $username; // Store username in session
        $_SESSION['role'] = $user['role']; // Store user role in session

        // Set session storage or cookies based on remember me
        if ($remember) {
            // Set a cookie to remember the user for 30 days
            setcookie('rememberMe', 'true', time() + (30 * 24 * 60 * 60), "/"); // 30 days
            setcookie('username', $username, time() + (30 * 24 * 60 * 60), "/"); // 30 days
        }

        // Redirect to index.html
        echo "<script>
    sessionStorage.setItem('loggedIn', 'true');
    sessionStorage.setItem('role', '" . addslashes($user['role']) . "');
    window.location.href = 'index.html';
</script>";
        exit();
    } else {
        // Redirect back to login with an error message
        header('Location: login.html?error=Invalid username or password');
        exit();
    }
}
?>
