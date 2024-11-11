<?php
session_start();
session_destroy(); // Destroy the session
setcookie('rememberMe', '', time() - 3600, "/"); // Expire the cookie
setcookie('username', '', time() - 3600, "/"); // Expire the cookie
echo "<script>
    sessionStorage.removeItem('loggedIn'); // Clear client-side session
    window.location.href = 'login.html'; // Redirect to login
</script>";
exit();
?>
