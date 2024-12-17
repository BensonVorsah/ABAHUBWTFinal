<?php
include 'navbar1.php';
// Database connection
require_once 'db_connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function to calculate team rankings for various stats
function calculateTeamRankings($conn, $statColumn) {
    $rankings = [];
    
    // Get total stats for all teams
    $query = "SELECT team_id, $statColumn FROM teams ORDER BY $statColumn DESC";
    $result = mysqli_query($conn, $query);
    
    $rank = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $rankings[$row['team_id']] = $rank;
        $rank++;
    }
    
    return $rankings;
}

// Recalculate ranks dynamically
$ppgRankings = calculateTeamRankings($conn, 'points_total');
$apgRankings = calculateTeamRankings($conn, 'assists_total');
$rpgRankings = calculateTeamRankings($conn, 'rebounds_total');
$spgRankings = calculateTeamRankings($conn, 'steals_total');
$bpgRankings = calculateTeamRankings($conn, 'blocks_total');

// Fetch coaches and captain names
function getPlayerName($conn, $playerId) {
    if (!$playerId) return 'N/A';
    $query = "SELECT CONCAT(Fname, ' ', Lname) AS full_name FROM players WHERE player_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $playerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $player = mysqli_fetch_assoc($result);
    return $player ? $player['full_name'] : 'N/A';
}

function getDefendingChampion($conn) {
    $query = "SELECT team_id 
              FROM championships 
              WHERE championship_year = (
                  SELECT MAX(championship_year) 
                  FROM championships
              )";
    $result = mysqli_query($conn, $query);
    // Add error checking
    if (!$result) {
        error_log("Defending champion query failed: " . mysqli_error($conn));
        return null;
    }

    $defendingChampion = mysqli_fetch_assoc($result);
    
    return $defendingChampion ? $defendingChampion['team_id'] : null;
}


// Fetch all teams in alphabetical order
$teamsQuery = "SELECT * FROM teams ORDER BY team_name ASC";
$teamsResult = mysqli_query($conn, $teamsQuery);

// add error checking
if (!$teamsResult){
    echo "Query Error: " .mysqli_error($conn);
    exit;
}
$teamCount = mysqli_num_rows($teamsResult);
mysqli_data_seek($teamsResult, 0);

$defendingChampionId = getDefendingChampion($conn);

?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA | Teams</title>
    <link rel="stylesheet" href="teamsstyles.css">
</head>
<body>     
    <h1 class="page-title">ABA Teams</h1>

        <div class="teams-container">
            <?php 
            
            mysqli_data_seek($teamsResult, 0);

            $teamProcessCount = 0;

            while ($team = mysqli_fetch_assoc($teamsResult)): 
                $teamProcessCount++;
                // Fetch player names
                $headCoach = getPlayerName($conn, $team['coach_id'] ?? null);
                $assistantCoach = getPlayerName($conn, $team['assistant_coach_id'] ?? null);
                $teamCaptain = getPlayerName($conn, $team['captain_id'] ?? null);

                // Get team color
                $teamColor = !empty($team['team_color']) ? $team['team_color'] : '#f0f0f0';
                $textColor = !empty($team['text_color']) ? $team['text_color'] : '#000000';
            ?>
                <div class="team-section" onclick="window.location.href='<?php echo $team['team_name']; ?>.php'">
                    <div class="team-container" style="background-color: <?php echo htmlspecialchars($teamColor); ?>; color: <?php echo htmlspecialchars($textColor); ?>;">
                    <div class="team-header">
                        <?php 
                        if (isset($team['team_logo']) && !empty(trim($team['team_logo']))): 
                        ?>
                            <img src="<?php echo htmlspecialchars($team['team_logo']); ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?> Logo" class="team-logo">
                        <?php 
                        else:
                            // Add a debug message if no logo
                            error_log("No logo from team: {$team['team_name']}");
                        endif;
                        ?>
                            <h2>
                                <?php echo htmlspecialchars($team['team_name']); ?>
                                <?php if ($team['team_id'] == $defendingChampionId): ?>
                                    <span class="cup-icon">🏆</span>
                                <?php endif; ?>
                            </h2>
                        </div>
        
                    </div>
                    <div class="team-details">
                        <div class="team-header-stats">
                            <p>Record: <?php echo htmlspecialchars($team['wins'] . '-' . $team['losses']); ?></p>
                            <p>Games Played: <?php echo htmlspecialchars($team['games_played']); ?></p>
                            <p>Championships: <?php echo intval($team['championships']); ?></p>
                            <p>Current Ranking: <?php echo htmlspecialchars($team['ranking'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="team-bottom-section">
                        <div class="team-rankings">
                        <h3>Team Rankings</h3>
                        <ul>
                            <li>Points Rank: <?php echo $ppgRankings[$team['team_id']]; ?></li>
                            <li>Assists Rank: <?php echo $apgRankings[$team['team_id']]; ?></li>
                            <li>Rebounds Rank: <?php echo $rpgRankings[$team['team_id']]; ?></li>
                            <li>Steals Rank: <?php echo $spgRankings[$team['team_id']]; ?></li>
                            <li>Blocks Rank: <?php echo $bpgRankings[$team['team_id']]; ?></li>
                        </ul>
                    </div>
                    
                    <div class="team-leadership">
                        <h3>Leadership</h3>
                        <p>Head Coach: <?php echo htmlspecialchars($headCoach); ?></p>
                        <p>Assistant Coach: <?php echo htmlspecialchars($assistantCoach); ?></p>
                        <p>Team Captain: <?php echo htmlspecialchars($teamCaptain); ?></p>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
error_log("Total teams processed: {$teamProcessCount}");

include 'footer.php';
?>