<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Check in Admin table
        $query = "SELECT * FROM admin WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) { // Admin found
            $admin = $result->fetch_assoc();
            if ($password === $admin['password']) {  // Direct comparison (Not Secure)
                $_SESSION['admin_id'] = $admin['id'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid Password!";
            }
        } else {
            // Check in Users table
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) { // User found
                $user = $result->fetch_assoc();
                if ($password === $user['password']) {  // Direct comparison (Not Secure)
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    header("Location: user_dashboard.php");
                    exit();
                } else {
                    $error = "Incorrect Password!";
                }
            } else {
                $error = "User not found!";
            }
        }
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>
