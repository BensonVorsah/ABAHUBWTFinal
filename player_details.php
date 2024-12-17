<?php
include 'navbar1.php';
require_once 'db_connection.php';

// Check if player ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: players.php');
    exit();
}

$player_id = intval($_GET['id']);

function fetchPlayerDetails($conn, $player_id) {
    $query = "SELECT 
        p.*,
        t.team_name, 
        t.team_logo,
        (SELECT SUM(ps.games_played) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_games_played,
        (SELECT SUM(ps.points) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_points,
        (SELECT AVG(ps.PPG) FROM playerstats ps WHERE ps.player_id = p.player_id) as avg_PPG,
        (SELECT SUM(ps.rebounds) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_rebounds,
        (SELECT AVG(ps.RPG) FROM playerstats ps WHERE ps.player_id = p.player_id) as avg_RPG,
        (SELECT SUM(ps.assists) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_assists,
        (SELECT AVG(ps.APG) FROM playerstats ps WHERE ps.player_id = p.player_id) as avg_APG,
        (SELECT SUM(ps.steals) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_steals,
        (SELECT AVG(ps.SPG) FROM playerstats ps WHERE ps.player_id = p.player_id) as avg_SPG,
        (SELECT SUM(ps.blocks) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_blocks,
        (SELECT AVG(ps.BPG) FROM playerstats ps WHERE ps.player_id = p.player_id) as avg_BPG,
        (SELECT SUM(ps.three_pointers) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_three_pointers,
        (SELECT SUM(ps.free_throws) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_free_throws,
        (SELECT SUM(ps.fantasy_points) FROM playerstats ps WHERE ps.player_id = p.player_id) as total_fantasy_points
    FROM players p
    LEFT JOIN teams t ON p.team_id = t.team_id
    WHERE p.player_id = ?";
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $player_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Fetch player details
$player = fetchPlayerDetails($conn, $player_id);

if (!$player) {
    // Redirect if player not found
    header('Location: players.php');
    exit();
}

// Convert height from float to feet and inches
function convertHeight($height) {
    if ($height === null) return 'N/A';
    $feet = floor($height);
    $inches = round(($height - $feet) * 12);
    return "{$feet}'$inches\"";
}
?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <title>ABA Hub - <?php echo htmlspecialchars($player['Fname'] . ' ' . $player['Lname']); ?></title>
    <link rel="stylesheet" href="playerdetails.css">
</head>
<body>
    <div class="player-details-container">
        <div class="player-header">
            <div class="player-main-image">
                <?php if (!empty($player['player_image'])): ?>
                    <img src="<?php echo htmlspecialchars($player['player_image']); ?>" alt="<?php echo htmlspecialchars($player['Fname'] . ' ' . $player['Lname']); ?>">
                <?php else: ?>
                    <img src="<?php echo htmlspecialchars($player['team_logo']); ?>" alt="<?php echo htmlspecialchars($player['team_name']); ?> Logo">
                <?php endif; ?>
            </div>
            <div class="player-header-info">
                <h1><?php echo htmlspecialchars($player['Fname'] . ' ' . $player['Lname']); ?></h1>
                <div class="player-basic-info">
                    <p><strong>Jersey Number:</strong> <?php echo htmlspecialchars($player['jersey_number'] ?? 'N/A'); ?></p>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($player['position'] ?? 'N/A'); ?></p>
                    <p><strong>Team:</strong> <?php echo htmlspecialchars($player['team_name'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="player-details">
            <div class="player-physical-info">
                <h2>Physical Information</h2>
                <p><strong>Height:</strong> <?php echo convertHeight($player['height']); ?></p>
                <p><strong>Weight:</strong> <?php echo $player['weight'] ? htmlspecialchars($player['weight'] . ' lbs') : 'N/A'; ?></p>
            </div>

            <div class="player-stats">
                <h2>Career Statistics</h2>
                <?php 
                // More robust stats checking
                $stats_to_display = [
                    'total_games_played' => 'Total Games Played',
                    'avg_PPG' => 'Average Points per Game',
                    'total_points' => 'Total Points',
                    'avg_RPG' => 'Average Rebounds per Game',
                    'total_rebounds' => 'Total Rebounds',
                    'avg_APG' => 'Average Assists per Game',
                    'total_assists' => 'Total Assists',
                    'avg_SPG' => 'Average Steals per Game',
                    'total_steals' => 'Total Steals',
                    'avg_BPG' => 'Average Blocks per Game',
                    'total_blocks' => 'Total Blocks',
                    'total_three_pointers' => 'Total Three Pointers',
                    'total_free_throws' => 'Total Free Throws',
                    'total_fantasy_points' => 'Total Fantasy Points'
                ];

                $has_stats = false;
                foreach ($stats_to_display as $field => $label) {
                    if (isset($player[$field]) && $player[$field] !== null) {
                        $has_stats = true;
                        break;
                    }
                }

                if ($has_stats): ?>
                    <div class="stats-grid">
                        <?php foreach ($stats_to_display as $field => $label):
                            if (isset($player[$field]) && $player[$field] !== null):
                        ?>
                            <div class="stat-item">
                                <span class="stat-label"><?php echo htmlspecialchars($label); ?></span>
                                <span class="stat-value">
                                    <?php 
                                    // For averages, format to 1 decimal place
                                    // For totals, round to whole number
                                    if (strpos($field, 'avg_') === 0) {
                                        echo htmlspecialchars(number_format($player[$field], 1));
                                    } else {
                                        echo htmlspecialchars(number_format($player[$field], 0));
                                    }
                                    ?>
                                </span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php else: ?>
                    <p>No statistics available for this player.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($player['bio'])): ?>
        <div class="player-bio">
            <h2>Player Bio</h2>
            <p><?php echo htmlspecialchars($player['bio']); ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>