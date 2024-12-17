<?php
include 'navbar1.php';
include 'db_connection.php';

if (!$conn){
    error_log("Database connection failed: " . mysqli_connect_error());
    die("An error occurred. Please try again later.");
}

// Function to fetch award winner details
function getAwardWinner($conn, $award_name) {
    $sql = "SELECT 
                w.player_id, 
                p.Fname, 
                p.Lname,
                p.player_image, 
                w.times_won,
                a.award_image
            FROM 
                winners w
            JOIN 
                players p ON w.player_id = p.player_id
            JOIN 
                awards a ON w.award_id = a.award_id
            WHERE 
                a.award_name = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        return null;
    }

    $stmt->bind_param("s", $award_name);
    if (!$stmt->execute()) {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        return null;
    }

    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to fetch team award winner
function getTeamAwardWinner($conn, $award_name) {
    $sql = "SELECT 
                t.team_name, 
                t.team_logo,
                w.times_won,
                a.award_image
            FROM 
                team_winners w
            JOIN 
                teams t ON w.team_id = t.team_id
            JOIN 
                awards a ON w.award_id = a.award_id
            WHERE 
                a.award_name = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        return null;
    }

    $stmt->bind_param("s", $award_name);

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Individual Awards List
$individual_awards = [
    'MVP', 'Finals MVP', 'WC MVP', 'EC MVP', 'MIP', 'DPOY', 'ROTY', 
    'SMOTY', 'Coach of the Year', 'Clutch Player of the Year', 
    'ABA Executive of the Year', 'ABA Sportsmanship Award', 
    'Best Teammate of the Year', 'All Star MVP', '3pt Contest Winner', 
    'King of the Court', 'Le Champ', 'Clutch Challenge Award', 
    'Inter Class MVP', 'ABA Citizenship Award', 'ABA Hustle Player Award',
    'Scoring Champ', 'Assists Champ', 'Rebounds Champ', 
    'Steals Champ', 'Blocks Champ', '3 Point Champ'
];

// Team Awards List
$team_awards = [
    'ABA Champions', 'WC Champions', 'EC Champions', 
    '1 Seed Champions', 'Inter Class Champs',
    'All Star Selection', 'School Team Selection'
];

// Fetch individual award winners
$individual_award_data = [];
foreach ($individual_awards as $award) {
    $winner = getAwardWinner($conn, $award);
    if ($winner) {
        $individual_award_data[$award] = $winner;
    }
}

// Fetch team award winners
$team_award_data = [];
foreach ($team_awards as $award) {
    $winner = getTeamAwardWinner($conn, $award);
    if ($winner) {
        $team_award_data[$award] = $winner;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA | Awards</title>
    <link rel="stylesheet" href="awardsstyles.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">ABA Awards</h1>
        
        <!-- Individual Awards Section -->
        <section class="awards-section">
            <h2 class="section-title">Individual Awards</h2>
            <div class="awards-grid">
                <?php 
                foreach ($individual_awards as $award):
                    if(isset($individual_award_data[$award])):
                        $winner = $individual_award_data[$award];
                ?>
                    <a href="awards.php?award=<?= urlencode($award) ?>" class="award-container" style="background-image: url('<?= htmlspecialchars($winner['award_image']) ?>');">
                        <div class="award-overlay">
                            <h3 class="award-title"><?= htmlspecialchars($award) ?></h3>
                            <img src="<?= htmlspecialchars($winner['player_image']) ?>" alt="<?= htmlspecialchars($winner['Fname']. ' ' . $winner['Lname']) ?>" class="award-image">
                            <div class="text-center">
                                <p class="award-winner-name"><?= htmlspecialchars($winner['Fname']. ' ' . $winner['Lname']) ?></p>
                                <p class="award-times-won">Won <?= $winner['times_won'] ?> time(s)</p>
                            </div>
                        </div>
                    </a>
                <?php endif; endforeach; ?>
            </div>
        </section>
        
        <!-- Team Awards Section -->
        <section class="awards-section">
            <h2 class="section-title">Team Awards</h2>
            <div class="awards-grid">
                <?php foreach ($team_awards as $award): 
                    if (isset($team_award_data[$award])):
                        $winner = $team_award_data[$award];
                ?>                    
                    <a href="awards.php?award=<?= urlencode($award) ?>" class="award-container" style="background-image: url('<?= htmlspecialchars($winner['award_image']) ?>');">
                        <div class="award-overlay">
                            <h3 class="award-title"><?= htmlspecialchars($award) ?></h3>
                            <img src="<?= htmlspecialchars($winner['team_logo']) ?>" alt="<?= htmlspecialchars($winner['team_name']) ?>" class="award-image">
                            <div class="text-center">
                                <p class="award-winner-name"><?= htmlspecialchars($winner['team_name']) ?></p>
                                <p class="award-times-won">Won <?= $winner['times_won'] ?> time(s)</p>
                            </div>
                        </div>
                    </a>
                <?php endif; endforeach; ?>
            </div>
        </section>
    </div>
    <script>
        // Dark Mode Toggle Functionality
        const modeToggle = document.getElementById('mode-toggle');
        const htmlElement = document.documentElement;

        modeToggle.addEventListener('click', () => {
            if (htmlElement.classList.contains('light-mode')) {
                htmlElement.classList.remove('light-mode');
                htmlElement.classList.add('dark-mode');
                localStorage.setItem('mode', 'dark');
            } else {
                htmlElement.classList.remove('dark-mode');
                htmlElement.classList.add('light-mode');
                localStorage.setItem('mode', 'light');
            }
        });

        // Check for saved mode preference or system preference
        const savedMode = localStorage.getItem('mode');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)');

        if (savedMode === 'dark' || (!savedMode && systemPrefersDark.matches)) {
            htmlElement.classList.remove('light-mode');
            htmlElement.classList.add('dark-mode');
        }
    </script>
</body>
</html>
<?php
include 'footer.php';
    // Close the database connection
    $conn->close();
?>