<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Handle user deletion
if (isset($_GET['delete']) && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $delete_query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "Error deleting user.";
    }
    $stmt->close();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $fav_team = $_POST['fav_team'];

    $update_query = "UPDATE users SET 
        username = ?, 
        email = ?, 
        gender = ?, 
        role = ?, 
        fav_team = ? 
    WHERE user_id = ?";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", 
        $username, $email, $gender, $role, $fav_team, $user_id
    );

    if ($stmt->execute()) {
        $success_message = "User updated successfully.";
    } else {
        $error_message = "Error updating user.";
    }
    $stmt->close();
}

// Fetch users
$users_query = "SELECT * FROM users ORDER BY username";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Users Management</title>
</head>
<body>
    <h1>Users Management</h1>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Role</th>
            <th>Favorite Team</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = $users_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['gender']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td><?php echo htmlspecialchars($user['fav_team']); ?></td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            <td>
                <a href="#" onclick="editUser(
                    <?php echo $user['user_id']; ?>, 
                    '<?php echo htmlspecialchars($user['username']); ?>', 
                    '<?php echo htmlspecialchars($user['email']); ?>', 
                    '<?php echo htmlspecialchars($user['gender']); ?>', 
                    '<?php echo htmlspecialchars($user['role']); ?>', 
                    '<?php echo htmlspecialchars($user['fav_team']); ?>'
                )">Edit</a>
                <a href="?delete=1&user_id=<?php echo $user['user_id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add/Edit User</h2>
    <form id="userForm" method="POST">
        <input type="hidden" name="user_id" id="user_id">
        <label>Username: <input type="text" name="username" id="username" required></label><br>
        <label>Email: <input type="email" name="email" id="email" required></label><br>
        <label>Gender:
            <select name="gender" id="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </label><br>
        <label>Role:
            <select name="role" id="role">
                <option value="ABA Star">ABA Star</option>
                <option value="ABA Fan">ABA Fan</option>
            </select>
        </label><br>
        <label>Favorite Team:
            <select name="fav_team" id="fav_team">
                <option value="AshKnights">AshKnights</option>
                <option value="Berekuso Warriors">Berekuso Warriors</option>
                <option value="HillBlazers">HillBlazers</option>
                <option value="Longshots">Longshots</option>
                <option value="Los Astros">Los Astros</option>
            </select>
        </label><br>
        <input type="submit" value="Save User">
    </form>

    <script>
    function editUser(id, username, email, gender, role, fav_team) {
        document.getElementById('user_id').value = id;
        document.getElementById('username').value = username;
        document.getElementById('email').value = email;
        document.getElementById('gender').value = gender;
        document.getElementById('role').value = role;
        document.getElementById('fav_team').value = fav_team;
    }
    </script>
</body>
</html>