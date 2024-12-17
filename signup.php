<?php
session_start();
include 'db_connection.php';

// Initialize error and success messages
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $fav_team = $_POST['fav_team'];

    // Validation
    if (empty($username)) {
        $error = "Username is required.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $error = "Password is required.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }

    // Check if username or email already exists
    if (empty($error)) {
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email already exists.";
        }
    }

    // If no errors, proceed with registration
    if (empty($error)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute insert statement
        $insert_query = "INSERT INTO users (username, email, password_hash, gender, role, fav_team) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ssssss", $username, $email, $hashed_password, $gender, $role, $fav_team);

        if ($insert_stmt->execute()) {
            $success = "Registration successful! You can now log in.";
            // Clear form data
            $username = $email = $gender = $role = $fav_team = '';
        } else {
            $error = "Error occurred while registering. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA League | Sign Up</title>
    <link rel="stylesheet" href="stylesgames.css">
    <style>
        .signup-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: var(--card-background);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .signup-form {
            display: flex;
            flex-direction: column;
        }
        .signup-form input, 
        .signup-form select {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }
        .signup-form .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .signup-form .submit-btn:hover {
            background-color: var(--primary-color-dark);
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
        .submit-btn {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        
        <?php 
        if (!empty($error)) {
            echo "<div class='error-message'>" . htmlspecialchars($error) . "</div>";
        }
        if (!empty($success)) {
            echo "<div class='success-message'>" . htmlspecialchars($success) . "</div>";
        }
        ?>
        
        <form class="signup-form" method="POST" action="">
            <input type="text" name="username" placeholder="Username" 
                   value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            
            <input type="email" name="email" placeholder="Email" 
                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            
            <input type="password" name="password" placeholder="Password" required>
            
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="ABA Star">ABA Star</option>
                <option value="ABA Fan">ABA Fan</option>
            </select>
            
            <select name="fav_team" required>
                <option value="">Select Favorite Team</option>
                <option value="AshKnights">AshKnights</option>
                <option value="Berekuso Warriors">Berekuso Warriors</option>
                <option value="HillBlazers">HillBlazers</option>
                <option value="Longshots">Longshots</option>
                <option value="Los Astros">Los Astros</option>
            </select>
            
            <button type="submit" class="submit-btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>
<?php
include 'footer.php';
// Close the database connection
mysqli_close($conn);
?>