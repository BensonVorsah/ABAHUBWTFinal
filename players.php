<?php
include 'navbar1.php';
require_once 'db_connection.php';

// Fetch player data from the database
$players = fetchAllPlayers($conn);

function fetchAllPlayers($conn) {
    $query = "SELECT 
        players.player_id,
        players.Fname,
        players.Lname, 
        players.position, 
        players.player_image,
        teams.team_name, 
        teams.team_logo
    FROM players 
    JOIN teams ON players.team_id = teams.team_id
    ORDER BY teams.team_name, players.Fname, players.Lname";
    
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <title>ABA Hub - Players</title>
    <link rel="stylesheet" href="players.css">
</head>
<body>
    <div class="players-container">
        <?php foreach ($players as $player): ?>
        <a href="player_details.php?id=<?php echo htmlspecialchars($player['player_id']); ?>" class="player-card-link">
            <div class="player-card">
                <div class="player-image">
                    <?php if (!empty($player['player_image'])): ?>
                        <img src="<?php echo htmlspecialchars($player['player_image']); ?>" alt="<?php echo htmlspecialchars($player['Fname'] . ' ' . $player['Lname']); ?>">
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($player['team_logo']); ?>" alt="<?php echo htmlspecialchars($player['team_name']); ?> Logo">
                    <?php endif; ?>
                </div>
                <div class="player-info">
                    <div class="player-name"><?php echo htmlspecialchars($player['Fname'] . ' ' . $player['Lname']); ?></div>
                    <div class="player-position"><?php echo htmlspecialchars($player['position']); ?></div>
                    <div class="team-name"><?php echo htmlspecialchars($player['team_name']); ?></div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>