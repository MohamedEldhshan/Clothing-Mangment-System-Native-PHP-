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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-50 to-pink-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="md:flex">
                <!-- Image Section -->
                <div class="md:w-1/2 bg-gradient-to-br from-purple-500 to-pink-500 p-8 flex items-center justify-center">
                    <div class="text-center text-white">
                        <i class="fas fa-user-plus text-8xl mb-4"></i>
                        <h3 class="text-2xl font-bold">Join Us Today!</h3>
                        <p class="mt-2">Create your account and get started</p>
                    </div>
                </div>
                
                <!-- Form Section -->
                <div class="md:w-1/2 p-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Registration</h2>
                    
                    <?php if (!empty($matching_error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i><?= $matching_error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_msg)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                            <i class="fas fa-check-circle mr-2"></i><?= $success_msg ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="username" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-purple-600"></i>Username
                            </label>
                            <input type="text" 
                                   name="username" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                   placeholder="Enter username"
                                   required>
                        </div>
                        
                        <div>
                            <label for="email" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2 text-purple-600"></i>E-mail
                            </label>
                            <input type="email" 
                                   name="email" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                   placeholder="Enter email"
                                   required>
                        </div>
                        
                        <div>
                            <label for="password" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-purple-600"></i>Password
                            </label>
                            <input type="password" 
                                   name="password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                   placeholder="Enter password"
                                   required>
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-purple-600"></i>Confirm Password
                            </label>
                            <input type="password" 
                                   name="confirm_password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                   placeholder="Confirm password"
                                   required>
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-user-plus mr-2"></i>Create Account
                        </button>
                        
                        <div class="text-center pt-4">
                            <a href="login.php" class="text-purple-600 hover:text-purple-800 font-semibold">
                                <i class="fas fa-sign-in-alt mr-2"></i>Already have an account? Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>