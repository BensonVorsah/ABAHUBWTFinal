<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Handle player deletion
if (isset($_GET['delete']) && isset($_GET['player_id'])) {
    $player_id = intval($_GET['player_id']);
    $delete_query = "DELETE FROM players WHERE player_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $player_id);
    
    if ($stmt->execute()) {
        $success_message = "Player deleted successfully.";
    } else {
        $error_message = "Error deleting player.";
    }
    $stmt->close();
}

// Handle player update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $player_id = intval($_POST['player_id']);
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $team_id = intval($_POST['team_id']);
    $jersey_number = $_POST['jersey_number'];
    $position = $_POST['position'];
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    $status = $_POST['status'];
    $bio = $_POST['bio'];
    $player_image = $_POST['player_image'];

    $update_query = "UPDATE players SET 
        Fname = ?, 
        Lname = ?, 
        team_id = ?, 
        jersey_number = ?, 
        position = ?, 
        height = ?, 
        weight = ?, 
        status = ?, 
        bio = ?, 
        player_image = ? 
    WHERE player_id = ?";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssissddssssi", 
        $fname, $lname, $team_id, $jersey_number, $position, 
        $height, $weight, $status, $bio, $player_image, $player_id
    );

    if ($stmt->execute()) {
        $success_message = "Player updated successfully.";
    } else {
        $error_message = "Error updating player.";
    }
    $stmt->close();
}

// Fetch players with team name
$players_query = "SELECT p.*, t.team_name 
                  FROM players p 
                  LEFT JOIN teams t ON p.team_id = t.team_id 
                  ORDER BY p.Lname";
$players_result = $conn->query($players_query);

// Fetch teams for dropdown
$teams_query = "SELECT team_id, team_name FROM teams ORDER BY team_name";
$teams_result = $conn->query($teams_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Players Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    <h1 class="text-3xl font-bold">Players Management</h1>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <table border="1">
        <tr>
            <th>Player ID</th>
            <th>Name</th>
            <th>Team</th>
            <th>Position</th>
            <th>Jersey Number</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($player = $players_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $player['player_id']; ?></td>
            <td><?php echo htmlspecialchars($player['Fname'] . ' ' . $player['Lname']); ?></td>
            <td><?php echo htmlspecialchars($player['team_name'] ?? 'Unassigned'); ?></td>
            <td><?php echo htmlspecialchars($player['position']); ?></td>
            <td><?php echo htmlspecialchars($player['jersey_number']); ?></td>
            <td><?php echo htmlspecialchars($player['status']); ?></td>
            <td>
                <a href="#" onclick="editPlayer(
                    <?php echo $player['player_id']; ?>, 
                    '<?php echo htmlspecialchars($player['Fname']); ?>', 
                    '<?php echo htmlspecialchars($player['Lname']); ?>', 
                    <?php echo $player['team_id'] ?? 0; ?>, 
                    '<?php echo htmlspecialchars($player['jersey_number']); ?>', 
                    '<?php echo htmlspecialchars($player['position']); ?>', 
                    <?php echo $player['height']; ?>, 
                    <?php echo $player['weight']; ?>, 
                    '<?php echo htmlspecialchars($player['status']); ?>', 
                    '<?php echo htmlspecialchars($player['bio']); ?>', 
                    '<?php echo htmlspecialchars($player['player_image']); ?>'
                )">Edit</a>
                <a href="?delete=1&player_id=<?php echo $player['player_id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this player?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add/Edit Player</h2>
    <form id="playerForm" method="POST">
        <input type="hidden" name="player_id" id="player_id">
        <label>First Name: <input type="text" name="fname" id="fname" required></label><br>
        <label>Last Name: <input type="text" name="lname" id="lname" required></label><br>
        <label>Team:
            <select name="team_id" id="team_id">
                <option value="">Select Team</option>
                <?php 
                mysqli_data_seek($teams_result, 0);
                while ($team = $teams_result->fetch_assoc()): ?>
                    <option value="<?php echo $team['team_id']; ?>">
                        <?php echo htmlspecialchars($team['team_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br>
        <label>Jersey Number: <input type="text" name="jersey_number" id="jersey_number"></label><br>
        <label>Position: 
            <select name="position" id="position">
                <option value="SG">Shooting Guard</option>
                <option value="PG">Point Guard</option>
                <option value="SF">Small Forward</option>
                <option value="PF">Power Forward</option>
                <option value="C">Center</option>
            </select>
        </label><br>
        <label>Height: <input type="number" step="0.1" name="height" id="height"></label><br>
        <label>Weight: <input type="number" step="0.1" name="weight" id="weight"></label><br>
        <label>Status:
            <select name="status" id="status">
                <option value="Active">Active</option>
                <option value="Retired">Retired</option>
            </select>
        </label><br>
        <label>Bio: <textarea name="bio" id="bio"></textarea></label><br>
        <label>Player Image: <input type="text" name="player_image" id="player_image"></label><br>
        <input type="submit" value="Save Player">
    </form>

    <script>
    function editPlayer(id, fname, lname, team_id, jersey_number, position, height, weight, status, bio, player_image) {
        document.getElementById('player_id').value = id;
        document.getElementById('fname').value = fname;
        document.getElementById('lname').value = lname;
        document.getElementById('team_id').value = team_id;
        document.getElementById('jersey_number').value = jersey_number;
        document.getElementById('position').value = position;
        document.getElementById('height').value = height;
        document.getElementById('weight').value = weight;
        document.getElementById('status').value = status;
        document.getElementById('bio').value = bio;
        document.getElementById('player_image').value = player_image;
    }
    </script>
</body>
</html>
<?php
include 'footer.php';
?>