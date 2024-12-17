<?php 
include 'navbar1.php';
include 'db_connection.php';

// Improved error handling function
function handleDatabaseError($conn, $stmt = null) {
    $errorMessage = $conn->error;
    error_log("Database Error: " . $errorMessage);
    
    if ($stmt) {
        $stmt->close();
    }
    
    return [
        'success' => false, 
        'message' => 'An error occurred while processing your request.'
    ];
}

// Function to fetch future games
function getFutureGames($conn){
    $query = "SELECT m.match_id, m.match_date,
                    t1.team_name AS home_team, t1.team_logo AS home_logo, 
                    t2.team_name AS away_team, t2.team_logo AS away_logo
              FROM Matches m
              JOIN Teams t1 ON m.team1_id = t1.team_id
              JOIN Teams t2 ON m.team2_id = t2.team_id
              WHERE m.match_date > NOW()
              ORDER BY m.match_date
              LIMIT 5";  // Fetch multiple future games

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return handleDatabaseError($conn);
    }

    if (!$stmt->execute()) {
        return handleDatabaseError($conn, $stmt);
    }

    $result = $stmt->get_result();
    $games = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $games;
}

// Function to fetch upcoming games
function getUpcomingGames($conn) {
    $query = "SELECT m.match_id, m.match_date, 
                     t1.team_name AS home_team, t1.team_logo AS home_logo, 
                     t2.team_name AS away_team, t2.team_logo AS away_logo
              FROM Matches m
              JOIN Teams t1 ON m.team1_id = t1.team_id
              JOIN Teams t2 ON m.team2_id = t2.team_id
              WHERE m.match_date > NOW()
              ORDER BY m.match_date
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return handleDatabaseError($conn);
    }

    if (!$stmt->execute()) {
        return handleDatabaseError($conn, $stmt);
    }

    $result = $stmt->get_result();
    $game = $result->fetch_assoc();
    $stmt->close();

    return $game;
}

// Function to fetch past games
function getPastGames($conn) {
    $query = "SELECT m.match_id, m.match_date, 
                     m.team1_score, m.team2_score,
                     t1.team_name AS home_team, t1.team_logo AS home_logo, 
                     t2.team_name AS away_team, t2.team_logo AS away_logo
              FROM Matches m
              JOIN Teams t1 ON m.team1_id = t1.team_id
              JOIN Teams t2 ON m.team2_id = t2.team_id
              WHERE m.match_date < NOW()
              ORDER BY m.match_date DESC
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return handleDatabaseError($conn);
    }

    if (!$stmt->execute()) {
        return handleDatabaseError($conn, $stmt);
    }

    $result = $stmt->get_result();
    $game = $result->fetch_assoc();
    $stmt->close();

    return $game;
}

function getPredictionVotes($conn, $match_id){
    $query = "SELECT 
                m.team1_id AS home_team_id, 
                m.team2_id AS away_team_id,
                t1.team_name AS home_team, 
                t2.team_name AS away_team,
                SUM(CASE WHEN p.predicted_team_id = m.team1_id AND p.is_home_team = 1 THEN 1 ELSE 0 END) AS home_predictions,
                SUM(CASE WHEN p.predicted_team_id = m.team2_id AND p.is_home_team = 0 THEN 1 ELSE 0 END) AS away_predictions
              FROM Matches m
              JOIN Teams t1 ON m.team1_id = t1.team_id
              JOIN Teams t2 ON m.team2_id = t2.team_id
              LEFT JOIN Predictions p ON m.match_id = p.match_id
              WHERE m.match_id = ?
              GROUP BY m.match_id";

    // Implement prepared statement to prevent SQL injection
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA League | Games</title>
    <link rel="stylesheet" href="stylesgames.css">
</head>
<body>
    <div class= "games-toggle">
        <button id="past-games-btn">Past Games</button>
        <button id="current-week-btn" class="active">Current Week</button>
        <button id="future-games-btn">Future Games</button>
    </div>

    <div class="games-container">
          <!-- Current Week's Games Section -->
        <section class="current-week-games games-view active">
            <h2>Current Week Games</h2>
            <div class="game-container">
                <?php 
                    $upcomingGame = getUpcomingGames($conn);
                    if ($upcomingGame): 
                        $upcomingPredictionData = getPredictionVotes($conn, $upcomingGame['match_id']);
                ?>
                <div class="game-card upcoming">
                    <div class="game-header">
                        <div class="game-status">Upcoming</div>
                        <div class="game-tournament-logo">
                            <img src="images/ABA Logo.png" alt="ABA Logo">
                        </div>
                        <div class="game-round">Regular Season: Round 2</div>
                    </div>
                    <div class="game-content">
                        <div class="team home-team">
                            <div class="team-logo-container">
                                <div class="team-logo">
                                    <img src="<?php echo $upcomingGame['home_logo']; ?>" alt="<?php echo $upcomingGame['home_team']; ?>">
                                </div>
                            </div>
                            <div class="team-name"><?php echo $upcomingGame['home_team']; ?></div>
                        </div>
                        <div class="game-details">
                            <div class="game-home-indicator">@</div>
                            <div class="game-date-time">
                                <span class="date"><?php echo date('F j, Y', strtotime($upcomingGame['match_date'])); ?></span>
                                <span class="time"><?php echo date('H:i A', strtotime($upcomingGame['match_date'])); ?></span>
                            </div>
                            <a href="predict.php" class="predict-button">Predict</a>
                        </div>
                        <div class="team away-team">
                            <div class="team-logo-container">
                                <div class="team-logo">
                                    <img src="<?php echo $upcomingGame['away_logo']; ?>" alt="<?php echo $upcomingGame['away_team']; ?>">
                                </div>
                            </div>
                            <div class="team-name"><?php echo $upcomingGame['away_team']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Prediction Bar -->
                <div class="prediction-bar">
                    <div class="prediction-details">
                        <div class="home-team" data-votes="<?php echo $upcomingPredictionData['home_predictions']; ?>">
                            <?php echo $upcomingPredictionData['home_team']; ?>: 
                            <?php echo $upcomingPredictionData['home_predictions']; ?> predictions
                        </div>
                        <div class="away-team" data-votes="<?php echo $upcomingPredictionData['away_predictions']; ?>">
                            <?php echo $upcomingPredictionData['away_team']; ?>: 
                            <?php echo $upcomingPredictionData['away_predictions']; ?> predictions
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </section>
        
        <!-- Future Games Section -->
        <section class="future-games games-view">
            <h2>Future Games</h2>
            <div class="game-container">
                <?php 
                    $futureGames = getFutureGames($conn);
                    foreach ($futureGames as $game): 
                ?>
                <div class="game-card upcoming">
                    <div class="game-header">
                        <div class="game-status">Coming Soon</div>
                        <div class="game-tournament-logo">
                            <img src="images/ABA Logo.png" alt="ABA Logo">
                        </div>
                        <div class="game-round">Regular Season</div>
                    </div>
                    <div class="game-content">
                        <div class="team home-team">
                            <div class="team-logo-container">
                                <div class="team-logo">
                                    <img src="<?php echo $game['home_logo']; ?>" alt="<?php echo $game['home_team']; ?>">
                                </div>
                            </div>
                            <div class="team-name"><?php echo $game['home_team']; ?></div>
                        </div>
                        <div class="game-details">
                            <div class="game-home-indicator">@</div>
                            <div class="game-date-time">
                                <span class="date"><?php echo date('F j, Y', strtotime($game['match_date'])); ?></span>
                                <span class="time"><?php echo date('H:i A', strtotime($game['match_date'])); ?></span>
                            </div>
                            <a href="predict.php" class="predict-button">Predict</a>
                        </div>
                        <div class="team away-team">
                            <div class="team-logo-container">
                                <div class="team-logo">
                                    <img src="<?php echo $game['away_logo']; ?>" alt="<?php echo $game['away_team']; ?>">
                                </div>
                            </div>
                            <div class="team-name"><?php echo $game['away_team']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Past Games Section -->
        <section class="past-games games-view">
            <h2>Past Games</h2>
            <div class="game-container">
                <?php 
                    $pastGame = getPastGames($conn);
                    if ($pastGame): 
                        $predictionData = getPredictionVotes($conn, $pastGame['match_id']);
                ?>
                <div class="game-card ended">
                    <div class="game-header">
                        <div class="game-status">Ended</div>
                        <div class="game-tournament-logo">
                            <img src="images/ABA Logo.png" alt="ABA Logo">
                        </div>
                        <div class="game-round">Regular Season: Round 1</div>
                    </div>
                    <div class="game-content">
                        <div class="team home-team">
                            <div class="team-logo-container">
                                <div class="team-logo">
                                    <img src="<?php echo $pastGame['home_logo']; ?>" alt="<?php echo $pastGame['home_team']; ?>">
                                </div>
                            </div>
                            <div class="team-name"><?php echo $pastGame['home_team']; ?></div>
                            <div class="team-score"><?php echo $pastGame['team1_score']; ?></div>
                        </div>
                        <div class="game-details">
                            <div class="game-date-time">
                                <span class="date"><?php echo date('F j, Y', strtotime($pastGame['match_date'])); ?></span>
                                <span class="time"><?php echo date('H:i A', strtotime($pastGame['match_date'])); ?></span>
                            </div>
                            <div class="game-action-buttons">
                                <a href="stats.php" class="box-score-button">Box Score</a>
                                <a href="media.php" class="watch-game-button">Watch</a>
                            </div>
                        </div>
                        <div class="team away-team">
                            <div class="team-logo-container">
                                <div class="team-logo">
                                    <img src="<?php echo $pastGame['away_logo']; ?>" alt="<?php echo $pastGame['away_team']; ?>">
                                </div>
                            </div>
                            <div class="team-name"><?php echo $pastGame['away_team']; ?></div>
                            <div class="team-score"><?php echo $pastGame['team2_score']; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Prediction Bar -->
                <div class="prediction-bar">
                    <div class="prediction-details">
                        <div class="home-team" data-votes="<?php echo $predictionData['home_predictions']; ?>">
                            <?php echo $predictionData['home_team']; ?>: 
                            <?php echo $predictionData['home_predictions']; ?> predictions
                        </div>
                        <div class="away-team" data-votes="<?php echo $predictionData['away_predictions']; ?>">
                            <?php echo $predictionData['away_team']; ?>: 
                            <?php echo $predictionData['away_predictions']; ?> predictions
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <script>
        // Games Toggle Functionality
        const pastGamesBtn = document.getElementById('past-games-btn');
        const currentWeekBtn = document.getElementById('current-week-btn');
        const futureGamesBtn = document.getElementById('future-games-btn');
        
        const pastGamesSection = document.querySelector('.past-games');
        const currentWeekGamesSection = document.querySelector('.current-week-games');
        const futureGamesSection = document.querySelector('.future-games');

        // function to handle tab switching
        function switchTab(activeBtn, activeSection){
            // remove active class from all buttons
            [pastGamesBtn, currentWeekBtn, futureGamesBtn].forEach(btn =>
                btn.classList.remove('active')
            );

            // remove active class from all sections
            [pastGamesSection, currentWeekGamesSection, futureGamesSection].forEach(section =>
                section.classList.remove('active')
            );

            // add active class to the clicked button and corresponding section
            activeBtn.classList.add('active');
            activeSection.classList.add('active');
        }

        // add event listeners to each button
        pastGamesBtn.addEventListener('click', () =>
            switchTab(pastGamesBtn, pastGamesSection)        
        );

        currentWeekBtn.addEventListener('click', () => 
            switchTab(currentWeekBtn, currentWeekGamesSection)
        );

        futureGamesBtn.addEventListener('click', () => 
            switchTab(futureGamesBtn, futureGamesSection)
        );

        // Prediction Bar Visualization
        function updatePredictionBar() {
            const predictionBars = document.querySelectorAll('.prediction-bar');
            predictionBars.forEach(bar => {
                const homeTeam = bar.querySelector('.home-team');
                const awayTeam = bar.querySelector('.away-team');

                if(!homeTeam || !awayTeam) return;
        
                const homeVotes = parseInt(homeTeam.dataset.votes || '0');
                const awayVotes = parseInt(awayTeam.dataset.votes || '0');
                const totalVotes = homeVotes + awayVotes;

                // Remove any existing vote bars
                const existingHomeBars = homeTeam.querySelectorAll('.vote-bar');
                const existingAwayBars = awayTeam.querySelectorAll('.vote-bar');
                existingHomeBars.forEach(bar => bar.remove());
                existingAwayBars.forEach(bar => bar.remove());

                // Create visual bars
                const homeVoteBar = document.createElement('div');
                homeVoteBar.classList.add('vote-bar', 'home-vote-bar');
                homeTeam.appendChild(homeVoteBar);

                const awayVoteBar = document.createElement('div');
                awayVoteBar.classList.add('vote-bar', 'away-vote-bar');
                awayTeam.appendChild(awayVoteBar);

                // Calculate and set bar widths
                if (totalVotes === 0) {
                    homeVoteBar.style.width = '50%';
                    awayVoteBar.style.width = '50%';
                } else {
                    homeVoteBar.style.width = `${(homeVotes / totalVotes) * 100}%`;
                    awayVoteBar.style.width = `${(awayVotes / totalVotes) * 100}%`;
                }

                // Update vote text to include percentage
                homeTeam.innerHTML += ` (${totalVotes > 0 ? ((homeVotes / totalVotes) * 100).toFixed(1) : 0}%)`;
                awayTeam.innerHTML += ` (${totalVotes > 0 ? ((awayVotes / totalVotes) * 100).toFixed(1) : 0}%)`;
            });
        }

        document.addEventListener('DOMContentLoaded', updatePredictionBar);

        function uploadTeamLogo(teamId, logoFile) {
            const targetDir = "images/team_logos/";
            const fileName = teamId + "_" + logoFile.name;
            const targetFilePath = targetDir + fileName;

            //This is a placeholder - actual file upload requires server-side handling
            console.log("Uploading team logo:", {
                teamId,
                fileName,
                targetFilePath
            });
        }
    </script>
</body>
</html>

<?php
include 'footer.php';
// Close the database connection
mysqli_close($conn);
?>
