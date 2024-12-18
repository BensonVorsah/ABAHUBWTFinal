<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Add Match
if (isset($_POST['add_match'])) {
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];
    $location = $_POST['location'];
    $team1_score = $_POST['team1_score'] ?: null;
    $team2_score = $_POST['team2_score'] ?: null;
    $highlight_url = $_POST['highlight_url'] ?: null;

    $sql = "INSERT INTO matches (team1_id, team2_id, match_date, location, team1_score, team2_score, highlight_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iississ", $team1_id, $team2_id, $match_date, $location, $team1_score, $team2_score, $highlight_url);
    
    if ($stmt->execute()) {
        $success_message = "Match added successfully!";
    } else {
        $error_message = "Error adding match: " . $stmt->error;
    }
}

// Handle Edit Match
if (isset($_POST['edit_match'])) {
    $match_id = $_POST['match_id'];
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];
    $location = $_POST['location'];
    $team1_score = $_POST['team1_score'] ?: null;
    $team2_score = $_POST['team2_score'] ?: null;
    $highlight_url = $_POST['highlight_url'] ?: null;

    $sql = "UPDATE matches SET team1_id=?, team2_id=?, match_date=?, location=?, team1_score=?, team2_score=?, highlight_url=? 
            WHERE match_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissisis", $team1_id, $team2_id, $match_date, $location, $team1_score, $team2_score, $highlight_url, $match_id);
    
    if ($stmt->execute()) {
        $success_message = "Match updated successfully!";
    } else {
        $error_message = "Error updating match: " . $stmt->error;
    }
}

// Handle Delete Match
if (isset($_GET['delete_match'])) {
    $match_id = $_GET['delete_match'];
    
    $sql = "DELETE FROM matches WHERE match_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $match_id);
    
    if ($stmt->execute()) {
        $success_message = "Match deleted successfully!";
    } else {
        $error_message = "Error deleting match: " . $stmt->error;
    }
}

// Fetch Teams for Dropdowns
$teams_query = "SELECT team_id, team_name FROM teams";
$teams_result = $conn->query($teams_query);

// Fetch Matches
$matches_query = "SELECT m.*, t1.team_name AS team1_name, t2.team_name AS team2_name 
                  FROM matches m 
                  JOIN teams t1 ON m.team1_id = t1.team_id 
                  JOIN teams t2 ON m.team2_id = t2.team_id 
                  ORDER BY m.match_date DESC";
$matches_result = $conn->query($matches_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Matches Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Matches Management</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Add/Edit Match Form -->
        <div class="card mb-4">
            <div class="card-header">Add/Edit Match</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="match_id" id="match_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Team 1</label>
                            <select name="team1_id" class="form-control" required>
                                <option value="">Select Team 1</option>
                                <?php while($team = $teams_result->fetch_assoc()): ?>
                                    <option value="<?php echo $team['team_id']; ?>"><?php echo $team['team_name']; ?></option>
                                <?php endwhile; 
                                mysqli_data_seek($teams_result, 0);?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Team 2</label>
                            <select name="team2_id" class="form-control" required>
                                <option value="">Select Team 2</option>
                                <?php while($team = $teams_result->fetch_assoc()): ?>
                                    <option value="<?php echo $team['team_id']; ?>"><?php echo $team['team_name']; ?></option>
                                <?php endwhile; 
                                mysqli_data_seek($teams_result, 0);?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Match Date</label>
                            <input type="datetime-local" name="match_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Team 1 Score (Optional)</label>
                            <input type="number" name="team1_score" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Team 2 Score (Optional)</label>
                            <input type="number" name="team2_score" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Highlight URL (Optional)</label>
                        <input type="url" name="highlight_url" class="form-control">
                    </div>
                    <button type="submit" name="add_match" class="btn btn-primary">Add Match</button>
                    <button type="submit" name="edit_match" class="btn btn-success" style="display:none;" id="edit_match_btn">Update Match</button>
                </form>
            </div>
        </div>
        
        <!-- Matches List -->
        <div class="card">
            <div class="card-header">Matches List</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Match ID</th>
                            <th>Team 1</th>
                            <th>Team 2</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($match = $matches_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $match['match_id']; ?></td>
                            <td><?php echo $match['team1_name']; ?></td>
                            <td><?php echo $match['team2_name']; ?></td>
                            <td><?php echo $match['match_date']; ?></td>
                            <td><?php echo $match['location']; ?></td>
                            <td>
                                <?php echo ($match['team1_score'] !== null && $match['team2_score'] !== null) 
                                    ? $match['team1_score'] . ' - ' . $match['team2_score'] 
                                    : 'Not played'; ?>
                            </td>
                            <td>
                                <button onclick="editMatch(
                                    <?php echo $match['match_id']; ?>,
                                    <?php echo $match['team1_id']; ?>,
                                    <?php echo $match['team2_id']; ?>,
                                    '<?php echo $match['match_date']; ?>',
                                    '<?php echo $match['location']; ?>',
                                    <?php echo $match['team1_score'] ?? 'null'; ?>,
                                    <?php echo $match['team2_score'] ?? 'null'; ?>,
                                    '<?php echo $match['highlight_url'] ?? ''; ?>'
                                )" class="btn btn-sm btn-warning">Edit</button>
                                <a href="?delete_match=<?php echo $match['match_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this match?');" 
                                   class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function editMatch(matchId, team1Id, team2Id, matchDate, location, team1Score, team2Score, highlightUrl) {
        // Populate form fields
        document.querySelector('select[name="team1_id"]').value = team1Id;
        document.querySelector('select[name="team2_id"]').value = team2Id;
        document.querySelector('input[name="match_date"]').value = matchDate.replace(' ', 'T');
        document.querySelector('input[name="location"]').value = location;
        document.querySelector('input[name="team1_score"]').value = team1Score || '';
        document.querySelector('input[name="team2_score"]').value = team2Score || '';
        document.querySelector('input[name="highlight_url"]').value = highlightUrl || '';
        
        // Set match ID
        document.getElementById('match_id').value = matchId;
        
        // Show update button, hide add button
        document.getElementById('edit_match_btn').style.display = 'inline-block';
        document.querySelector('button[name="add_match"]').style.display = 'none';
    }
    </script>
</body>
</html>