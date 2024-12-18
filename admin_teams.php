<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Handle team deletion
if (isset($_GET['delete']) && isset($_GET['team_id'])) {
    $team_id = intval($_GET['team_id']);
    
    // First, remove related records to prevent foreign key constraint issues
    $delete_queries = [
        "DELETE FROM teamroster WHERE team_id = ?",
        "DELETE FROM teamstats WHERE team_id = ?",
        "DELETE FROM team_winners WHERE team_id = ?",
        "DELETE FROM playoff_brackets WHERE first_seed_id = ? OR second_seed_id = ? OR third_seed_id = ? OR fourth_seed_id = ?",
        "DELETE FROM teams WHERE team_id = ?"
    ];
    
    $stmt = $conn->prepare($delete_queries[0]);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    
    $stmt = $conn->prepare($delete_queries[1]);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    
    $stmt = $conn->prepare($delete_queries[2]);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    
    $stmt = $conn->prepare($delete_queries[3]);
    $stmt->bind_param("iiii", $team_id, $team_id, $team_id, $team_id);
    $stmt->execute();
    
    $stmt = $conn->prepare($delete_queries[4]);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    
    header("Location: admin_teams.php?success=Team deleted successfully");
    exit();
}

// Fetch teams
$teams_query = "SELECT * FROM teams";
$teams_result = $conn->query($teams_query);

// Fetch coaches (assuming coaches are players with coach role)
$coaches_query = "SELECT player_id, Fname, Lname FROM players WHERE position = 'Coach'";
$coaches_result = $conn->query($coaches_query);

// Handle team addition/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : null;
    $team_name = $_POST['team_name'];
    $coach_id = $_POST['coach_id'];
    $team_color = $_POST['team_color'];
    $text_color = $_POST['text_color'];
    
    if ($team_id) {
        // Update existing team
        $update_query = "UPDATE teams SET team_name = ?, coach_id = ?, team_color = ?, text_color = ? WHERE team_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sissi", $team_name, $coach_id, $team_color, $text_color, $team_id);
        $stmt->execute();
        
        header("Location: admin_teams.php?success=Team updated successfully");
    } else {
        // Add new team
        $insert_query = "INSERT INTO teams (team_name, coach_id, team_color, text_color) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("siss", $team_name, $coach_id, $team_color, $text_color);
        $stmt->execute();
        
        header("Location: admin_teams.php?success=Team added successfully");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Team Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function confirmDelete(teamId) {
        if (confirm('Are you sure you want to delete this team? This will remove all related records.')) {
            window.location.href = '?delete=1&team_id=' + teamId;
        }
    }
    
    function prepareEditModal(teamId, teamName, coachId, teamColor, textColor) {
        document.getElementById('team_id').value = teamId;
        document.getElementById('team_name').value = teamName;
        document.getElementById('coach_id').value = coachId;
        document.getElementById('team_color').value = teamColor;
        document.getElementById('text_color').value = textColor;
        document.getElementById('form-title').innerText = 'Edit Team';
        document.getElementById('submit-btn').innerText = 'Update Team';
        document.getElementById('team-modal').classList.remove('hidden');
    }
    
    function openAddModal() {
        document.getElementById('team_id').value = '';
        document.getElementById('team_name').value = '';
        document.getElementById('coach_id').value = '';
        document.getElementById('team_color').value = '#f0f0f0';
        document.getElementById('text_color').value = '#000000';
        document.getElementById('form-title').innerText = 'Add New Team';
        document.getElementById('submit-btn').innerText = 'Add Team';
        document.getElementById('team-modal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('team-modal').classList.add('hidden');
    }
    </script>
</head>
<body class="bg-gray-100">
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mx-auto mt-8 px-4">
        <h1 class="text-3xl font-bold mb-6">Team Management</h1>
        
        <?php
        // Display success or error messages
        if (isset($_GET['success'])) {
            echo "<div class='bg-green-200 text-green-800 p-4 rounded mb-4'>" . htmlspecialchars($_GET['success']) . "</div>";
        }
        ?>
        
        <div class="bg-white shadow-md rounded">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold">Teams List</h2>
                <button 
                    onclick="openAddModal()" 
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    Add New Team
                </button>
            </div>
            
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3 text-left">Team Name</th>
                        <th class="p-3 text-left">Coach</th>
                        <th class="p-3 text-left">Wins</th>
                        <th class="p-3 text-left">Losses</th>
                        <th class="p-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($team = $teams_result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="p-3"><?php echo htmlspecialchars($team['team_name']); ?></td>
                        <td class="p-3">
                            <?php 
                            $coach_name = $team['coach_id'] ? 
                                $conn->query("SELECT Fname, Lname FROM players WHERE player_id = {$team['coach_id']}")->fetch_assoc() 
                                : null; 
                            echo $coach_name ? htmlspecialchars($coach_name['Fname'] . ' ' . $coach_name['Lname']) : 'No Coach'; 
                            ?>
                        </td>
                        <td class="p-3"><?php echo htmlspecialchars($team['wins']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($team['losses']); ?></td>
                        <td class="p-3">
                            <button 
                                onclick="prepareEditModal(
                                    <?php echo $team['team_id']; ?>, 
                                    '<?php echo htmlspecialchars($team['team_name'], ENT_QUOTES); ?>', 
                                    <?php echo $team['coach_id'] ?? 'null'; ?>, 
                                    '<?php echo htmlspecialchars($team['team_color'], ENT_QUOTES); ?>', 
                                    '<?php echo htmlspecialchars($team['text_color'], ENT_QUOTES); ?>'
                                )" 
                                class="bg-blue-500 text-white px-3 py-1 rounded mr-2"
                            >
                                Edit
                            </button>
                            <button 
                                onclick="confirmDelete(<?php echo $team['team_id']; ?>)" 
                                class="bg-red-500 text-white px-3 py-1 rounded"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Team Modal -->
    <div id="team-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 id="form-title" class="text-2xl mb-4">Add New Team</h2>
            <form method="POST" action="">
                <input type="hidden" name="team_id" id="team_id">
                
                <div class="mb-4">
                    <label class="block mb-2">Team Name</label>
                    <input 
                        type="text" 
                        name="team_name" 
                        id="team_name" 
                        required 
                        class="w-full p-2 border rounded"
                    >
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2">Coach</label>
                    <select 
                        name="coach_id" 
                        id="coach_id" 
                        class="w-full p-2 border rounded"
                    >
                        <option value="">Select Coach</option>
                        <?php 
                        mysqli_data_seek($coaches_result, 0);
                        while($coach = $coaches_result->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $coach['player_id']; ?>">
                                <?php echo htmlspecialchars($coach['Fname'] . ' ' . $coach['Lname']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2">Team Color</label>
                    <input 
                        type="color" 
                        name="team_color" 
                        id="team_color" 
                        class="w-full p-2 border rounded"
                    >
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2">Text Color</label>
                    <input 
                        type="color" 
                        name="text_color" 
                        id="text_color" 
                        class="w-full p-2 border rounded"
                    >
                </div>
                
                <div class="flex justify-between">
                    <button 
                        type="submit" 
                        id="submit-btn" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                        Add Team
                    </button>
                    <button 
                        type="button" 
                        onclick="closeModal()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
include 'footer.php';
?>