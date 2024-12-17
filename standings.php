<?php
include 'navbar1.php';
include 'db_connection.php';

// Fetch teams standings from database
$sql = "SELECT 
    team_id, 
    team_name, 
    team_logo, 
    games_played, 
    wins, 
    losses, 
    diff,
    CASE WHEN games_played > 0 THEN ROUND(wins * 100.0 / games_played, 2) ELSE 0 END as win_percentage
FROM teams
ORDER BY win_percentage DESC";

$result = $conn->query($sql);

// Prepare standings data
$standings = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $standings[] = $row;
    }
}

// Create playoff matchups based on current standings
$playoff_matchups = [];
if (count($standings) >= 4) {
    $playoff_matchups = [
        'first_team' => $standings[0]['team_name'],
        'first_team_logo' => $standings[0]['team_logo'],
        'fourth_team' => $standings[3]['team_name'],
        'fourth_team_logo' => $standings[3]['team_logo'],
        'second_team' => $standings[1]['team_name'],
        'second_team_logo' => $standings[1]['team_logo'],
        'third_team' => $standings[2]['team_name'],
        'third_team_logo' => $standings[2]['team_logo']
    ];
}

$conn->close();
?>

<?php if (!empty($playoff_matchups)): ?>
    <div class="playoff-bracket">
        <!-- Your existing playoff bracket HTML -->
    </div>
<?php else: ?>
    <p>No playoff matchups available.</p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABAHUB | Standings</title>
    <link rel="stylesheet" href="standingsstyles.css">
</head>
<body>
    <h1 class="page-title">Playoffs Bracket</h1>

    <div class="championship-title">Conference Finals</div>

    <div class="playoff-bracket">
        <div class="bracket-column">
            <div class="match-up">
                <div class="team-info">
                    <img src="<?php echo htmlspecialchars($playoff_matchups['first_team_logo']); ?>"
                        alt="<?php echo htmlspecialchars($playoff_matchups['first_team']); ?> Logo"
                        class="team-logo">
                    <span class="team-name"><?php echo htmlspecialchars($playoff_matchups['first_team']); ?></span>
                </div>
                <div class="vs-divider">VS</div>
                <div class="team-info">
                    <img src="<?php echo htmlspecialchars($playoff_matchups['fourth_team_logo']); ?>"
                        alt="<?php echo htmlspecialchars($playoff_matchups['fourth_team']); ?> Logo"
                        class="team-logo">
                    <span class="team-name"><?php echo htmlspecialchars($playoff_matchups['fourth_team']); ?></span>
                </div>
            </div>
        </div>

        <div class="bracket-column">
            <div class="match-up">
                <div class="team-info">
                    <img src="<?php echo htmlspecialchars($playoff_matchups['second_team_logo']); ?>" 
                        alt="<?php echo htmlspecialchars($playoff_matchups['second_team']); ?> Logo" 
                        class="team-logo">
                    <span class="team-name"><?php echo htmlspecialchars($playoff_matchups['second_team']); ?></span>
                </div>
                <div class="vs-divider">VS</div>
                <div class="team-info">
                    <img src="<?php echo htmlspecialchars($playoff_matchups['third_team_logo']); ?>" 
                        alt="<?php echo htmlspecialchars($playoff_matchups['third_team']); ?> Logo" 
                        class="team-logo">
                    <span class="team-name"><?php echo htmlspecialchars($playoff_matchups['third_team']); ?></span>
                </div>
            </div>
        </div>
    </div>
    

    <div class="championship-title">ABA Championship Finals</div>

    <div class="finals-matchup">
        <div class="bracket-column">
            <div class="match-up">
                <div class="team-info">
                    <img src="<?php echo htmlspecialchars($playoff_matchups['first_team_logo']); ?>" 
                        alt="<?php echo htmlspecialchars($playoff_matchups['first_team']); ?> Logo" 
                        class="team-logo">
                    <span class="team-name"><?php echo htmlspecialchars($playoff_matchups['first_team']); ?></span>
                </div>
                <div class="vs-divider">VS</div>
                <div class="team-info">
                    <img src="<?php echo htmlspecialchars($playoff_matchups['second_team_logo']); ?>" 
                        alt="<?php echo htmlspecialchars($playoff_matchups['second_team']); ?> Logo" 
                        class="team-logo">
                    <span class="team-name"><?php echo htmlspecialchars($playoff_matchups['second_team']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <h1 class="page-title">League Standings</h1>

    <table class="standings-table">
        <thead>
            <tr>
                <th>Seed</th>
                <th>Team Logo</th>
                <th>Team</th>
                <th>GP</th>
                <th>W</th>
                <th>L</th>
                <th>DIFF</th>
                <th>Win %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($standings as $index => $team): 
                $seed_class = '';
                if ($index === 0) $seed_class = 'seed-1';
                elseif ($index === 1) $seed_class = 'seed-2';
                elseif ($index === 2) $seed_class = 'seed-3';
                elseif ($index === 4) $seed_class = 'out-of-playoffs';
            ?>
                <tr class="<?php echo $seed_class; ?>">
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($team['team_logo']); ?>" 
                             alt="<?php echo htmlspecialchars($team['team_name']); ?> Logo" 
                             class="team-logo">
                    </td>
                    <td><?php echo htmlspecialchars($team['team_name']); ?></td>
                    <td><?php echo htmlspecialchars($team['games_played']); ?></td>
                    <td><?php echo htmlspecialchars($team['wins']); ?></td>
                    <td><?php echo htmlspecialchars($team['losses']); ?></td>
                    <td><?php echo htmlspecialchars($team['diff']); ?></td>
                    <td><?php echo htmlspecialchars($team['win_percentage']) . '%'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

<?php 
include 'footer.php';
?>