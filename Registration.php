<?php
session_start();

include "auth.php";
$matching_error = " ";
$success_msg = "";
// Getting data from the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
   //checking if the passwords match or not
    if ($password !== $confirm_password ) {
        $matching_error = 'Passwords Dosen`t Match';
    }else{
  // Check if email already exists
  $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
  $result = mysqli_query($conn, $sql);    

  if (mysqli_num_rows($result) === 1) {
      $matching_error = 'Email already exists';
  } else {// if the password match it will hash it and save it
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";

      if (mysqli_query($conn, $sql)) {
          $success_msg = "Account created successfully!";
          echo "Stored hashed password: " . $hashed_password;

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/United/style.css">
</head>
<body>
<div class="container">
        <!-- Image Section -->
        <div class="image-container">
            <figure>
                <img src="http://localhost/United/pics/registration.png" alt="Signin Image">
            </figure>
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
            <form  method = 'POST'>
              <label for="username">Username</label>
              <input type="text" name="username"  class="form-control mb-2 " required>

              <label for="email">E-mail</label>
              <input type="email" name="email" class="form-control mb-2 " required>

              <label for="password">Password</label>
              <input type="password" name="password" class="form-control mb-2 " required>

              <label for=" confirm_password">Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control mb-2 " required>

              
              <button type="submit" class="btn btn-primary mb-2">Create</button><br>
             
             <a href="login.php" >Login</a>
            </form>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>