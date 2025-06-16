<?php
session_start();
include "auth.php";

if (is_user_logged_in()) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare SQL to safely search for the user
    $stmt = mysqli_prepare($conn, "SELECT username, password FROM users WHERE username = ?  ");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    // Get result
    $result = mysqli_stmt_get_result($stmt);
    
    // Check if user found
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "❌ Invalid password.";
        }
    } else {
        $error_message = "❌ User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/United/style.css">
</head>
<body>
<div class = login-body>
    <div class="login-container">
        <!-- Image Section -->
        <div class="image-container">
            <figure>
                <img src="http://localhost/United/pics/signin-image.jpg" alt="Signin Image">
            </figure>
        </div>

        <!-- Form Section -->
        <div class="form-container">
            <h2>Log In</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form  method = 'POST' action="">
              
              <label for="username">Username</label>
              <input type="text" name="username" id="username" required>

              <label for="password">Password</label>
              <input type="password" name="password" id="password" required>

              <button type="submit" class="btn btn-primary">Login</button><br>
             
              <a href="Registration.php" >Create an Account</a>
              
            </form>
        </div>
    </div>
 </div>
</body>
</html>