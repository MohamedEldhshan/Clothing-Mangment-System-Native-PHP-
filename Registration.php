<?php
session_start();
include "auth.php";
$matching_error = "";
$success_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password ) {
        $matching_error = 'Passwords Don\'t Match';
    }else{
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);    
        if (mysqli_num_rows($result) === 1) {
            $matching_error = 'Email already exists';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Account created successfully!";
            } else {
                $matching_error = "Insert failed: " . mysqli_error($conn);
            }
        } 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href ="http://localhost/United/style.css">
</head>
<body>
<div class="container">
    <!-- Image Section -->
    <div class="image-container">
        <img src="http://localhost/United/pics/registration.png" alt="Registration Image">
    </div>
    <!-- Form Section -->
    <div class="form-container">
        <h2>Registration</h2>
        <?php if (!empty($matching_error)): ?>
            <p style="color: red;"><?= $matching_error ?></p>
        <?php endif; ?>
        <?php if (!empty($success_msg)): ?>
            <p style="color: green;"><?= $success_msg ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" required>
            <label for="email">E-mail</label>
            <input type="email" name="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" required>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" required>
            <button type="submit" class="btn">Create</button>
            <a href="login.php">Login</a>
        </form>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>