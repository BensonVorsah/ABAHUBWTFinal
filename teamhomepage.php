<?php
include 'navbar1.php';
// File: teamhomepage.php
require_once 'db_connection.php';

// Function to fetch team data with detailed stats
function getTeamData($teamId) {
    global $conn;  // Changed from $connection to $conn
    
    $query = "
        SELECT t.*, 
               CONCAT(c.Fname, ' ', c.Lname) AS coach_name, 
               CONCAT(capt.Fname, ' ', capt.Lname) AS captain_name, 
               CONCAT(ac.Fname, ' ', ac.Lname) AS assistant_coach_name
        FROM teams t
        LEFT JOIN players c ON t.coach_id = c.player_id
        LEFT JOIN players capt ON t.captain_id = capt.player_id
        LEFT JOIN players ac ON t.assistant_coach_id = ac.player_id
        WHERE t.team_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    
    $stmt->bind_param("i", $teamId);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        die("Get result failed: " . htmlspecialchars($stmt->error));
    }
    
    return $result->fetch_assoc();
}

// Similarly, update the getTeamRoster function
function getTeamRoster($teamId) {
    global $conn;  // Changed from $connection to $conn
    
    $query = "SELECT 
            player_id,
            CONCAT(Fname, ' ', Lname) AS player_name,
            jersey_number, 
            position, 
            height, 
            weight, 
            bio,
            FROM players 
            WHERE team_id = ? ORDER BY Lname, Fname";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    
    $stmt->bind_param("i", $teamId);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        die("Get result failed: " . htmlspecialchars($stmt->error));
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get team ID from URL or set a default
$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 1;

// Get team details and roster
$team = getTeamData($team_id);
$roster = getTeamRoster($team_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($team['team_name']) ?> Team Page</title>
    <link rel="stylesheet" href="team_styles.css">
</head>
<body>
    <div class="team-header">
        <img src="<?= htmlspecialchars($team['team_logo']) ?>" alt="<?= htmlspecialchars($team['team_name']) ?> Logo" class="team-logo">
        <div class="team-header-info">
            <h1><?= htmlspecialchars($team['team_name']) ?></h1>
            <div class="team-record">
                <span>Record: <?= htmlspecialchars($team['wins']) ?>-<?= htmlspecialchars($team['losses']) ?></span>
                <span>Ranking: #<?= htmlspecialchars($team['ranking'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <div class="team-stats">
        <h2>Team Statistics</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-label">Games Played</span>
                <span class="stat-value"><?= htmlspecialchars($team['games_played']) ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Points per Game</span>
                <span class="stat-value"><?= number_format($team['points_total'] / max($team['games_played'], 1), 1) ?></span>
                <span class="stat-rank">(Rank: <?= htmlspecialchars($team['ppg_rank'] ?? 'N/A') ?>)</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Assists per Game</span>
                <span class="stat-value"><?= number_format($team['assists_total'] / max($team['games_played'], 1), 1) ?></span>
                <span class="stat-rank">(Rank: <?= htmlspecialchars($team['apg_rank'] ?? 'N/A') ?>)</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Rebounds per Game</span>
                <span class="stat-value"><?= number_format($team['rebounds_total'] / max($team['games_played'], 1), 1) ?></span>
                <span class="stat-rank">(Rank: <?= htmlspecialchars($team['rpg_rank'] ?? 'N/A') ?>)</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Steals per Game</span>
                <span class="stat-value"><?= number_format($team['steals_total'] / max($team['games_played'], 1), 1) ?></span>
                <span class="stat-rank">(Rank: <?= htmlspecialchars($team['spg_rank'] ?? 'N/A') ?>)</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Blocks per Game</span>
                <span class="stat-value"><?= number_format($team['blocks_total'] / max($team['games_played'], 1), 1) ?></span>
                <span class="stat-rank">(Rank: <?= htmlspecialchars($team['bpg_rank'] ?? 'N/A') ?>)</span>
            </div>
        </div>
    </div>

    <div class="team-leadership">
        <h2>Team Leadership</h2>
        <div class="leadership-grid">
            <div class="leadership-item">
                <span class="leadership-label">Coach</span>
                <span class="leadership-name"><?= htmlspecialchars($team['coach_name'] ?? 'N/A') ?></span>
            </div>
            <div class="leadership-item">
                <span class="leadership-label">Captain</span>
                <span class="leadership-name"><?= htmlspecialchars($team['captain_name'] ?? 'N/A') ?></span>
            </div>
            <div class="leadership-item">
                <span class="leadership-label">Assistant Coach</span>
                <span class="leadership-name"><?= htmlspecialchars($team['assistant_coach_name'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <div class="team-roster">
        <h2>Team Roster</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Position</th>
                    <th>Height</th>
                    <th>Weight</th>
                    <th>Age</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roster as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player['player_name']) ?></td>
                    <td><?= htmlspecialchars($player['jersey_number']) ?></td>
                    <td><?= htmlspecialchars($player['position']) ?></td>
                    <td><?= htmlspecialchars($player['height']) ?></td>
                    <td><?= htmlspecialchars($player['weight']) ?></td>
                    <td><?= htmlspecialchars($player['age']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>