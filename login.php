<?php
session_start();
include 'db_connection.php';

/*
// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
*/

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username_or_email) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        // Prepare SQL to prevent SQL injection
        $query = "SELECT user_id, username, email, password_hash, role, fav_team 
                  FROM users 
                  WHERE username = ? OR email = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fav_team'] = $user['fav_team'];

                // Redirect to home or dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username/email or password.";
            }
        } else {
            $error = "Invalid username/email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA League | Login</title>
    <link rel="stylesheet" href="stylesgames.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: var(--card-background);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .login-form {
            display: flex;
            flex-direction: column;
        }
        .login-form input {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }
        .submit-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-form {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
    
        .submit-btn:hover {
            background-color: var(--primary-color-dark);
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .signup-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Log In</h2>
        
        <?php 
        if (!empty($error)) {
            echo "<div class='error-message'>" . htmlspecialchars($error) . "</div>";
        }
        ?>
        
        <form class="login-form" method="POST" action="">
            <input type="text" name="username_or_email" 
                   placeholder="Username or Email" required>
            
            <input type="password" name="password" 
                   placeholder="Password" required>
            
            <button type="submit" class="submit-btn">Log In</button>
        </form>
        
        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</body>
</html>
<?php
include 'footer.php';
// Close the database connection
mysqli_close($conn);
?>