<?php 
include 'db_connection.php';
include 'navbar1.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABAHUB</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>   
    <!-- Welcome Section -->
    <section class="welcome">
        <div class="slideshow-container">
            <!-- Slide Images -->
             <div class="slide fade">
                <img src="images/court 1.jpg" alt="Court1">
             </div>
             <div class="slide fade">
                <img src="images/wpp (7).jpg" alt="Court1">
             </div>
             <div class="slide fade">
                <img src="images/wpp (10).jpg" alt="Court1">
             </div>
             <div class="slide fade">
                <img src="images/wpp (12).jpg" alt="Court1">
             </div>
             <div class="slide fade">
                <img src="images/bg1.png" alt="Court1">
             </div>

             <!-- Dot Indicators -->
             <div class="dots-container">
                <span class="dot" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
             </div>
    </section>
    
    

    <!-- Features & Results Section -->
    <section class="features-results">
        <h2>Features & Results</h2>
        <div class="results-container">
            <!-- Placeholder for dynamic data -->
            <div class="game-result">
                <img src="images/team1-logo.png" alt="Team 1 Logo">
                <span>Team Name</span>
                <span>Score</span>
                <img src="images/team2-logo.png" alt="Team 2 Logo">
                <span>Team Name</span>
            </div>
            <!-- Repeat for other game results -->
        </div>
    </section>

    <!-- ABA Schedule Section -->
    <section class="schedule">
        <h2>ABA Schedule</h2>
        <div class="schedule-container">
            <!-- Placeholder for dynamic data -->
            <div class="match-card">
                <div class="match-header">
                    <div class="match-status">Upcoming</div>
                    <div class="match-tournament"><img src="images/ABA Logo.png" alt="ABA">
                        </div>
                    <div class="match-action"></div>
                </div>
                <div class="match-content">
                    <div class="column">
                        <div class="team team--home">
                            <div class="team-logo">
                                <img src="images/Ash Knights Logo.png" alt="AshKnights">
                            </div>
                            <h2 class="team-name">AshKnights</h2>
                        </div>
                    </div>

                    <div class="column">
                        <div class="match-details">
                            <div class="match-date">
                            Regular Season: <strong> Round 2</strong>
                            </div>
                            <div class="match-score">
                                <span class="match-score-number">VS</span>
                            </div>

                            <div class="match-time-lapsed">Tip-Off - 18:30pm</div>
                            <div class="match-referee">
                                Referees: <strong>Gabby & Sniper</strong>
                            </div>
                            <div class="match-predict-options">
                                <button class="match-predict-option">1</button>
                                <button class="match-predict-option">X</button>
                                <button class="match-predict-option">2</button>
                            </div>
                            <button class="match-predict-place">Prediction</button>
                        </div>
                    </div>

                    <div class="column">
                        <div class="team team--away">
                            <div class="team-logo">
                                <img src="images/Berekuso Warriors logo_edited.png" alt="westham">
                            </div>
                            <h2 class="team-name">Warriors</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Repeat for other matches -->
        </div>
    </section>

    <!-- ABA Teams Section -->
    <section class="teams">
        <h2><strong>ABA Teams</strong></h2>
        <div class="teams-container">
            <!-- Placeholder for dynamic data -->
            <div class="ashk">
                <img src="images/Ash Knights Logo.png" alt="AshKnights">
                <span><strong>AshKnights</strong></span>
            </div>
            <div class="warr">
                <img src="images/Berekuso Warriors logo_edited.png" alt="Warriors">
                <span><strong>Berekuso Warriors</strong></span>
            </div>
            <div class="hill">
                <img src="images/Hillblazers logo_edited.png" alt="HillBlazers">
                <span><strong>Hillblazers</strong></span>
            </div>
            <div class="astr">
                <img src="images/Los Astros Logo_edited.png" alt="Los Astros">
                <span><strong>Los Astros</strong></span>
            </div>
            <div class="long">
                <img src="images/Longshots Logo.png" alt="Longshots">
                <span><strong>Longshots</strong></span>
            </div>
        </div>
    </section>

    <script src="slideshow.js"></script>
</body>
</html>
<?php
include 'footer.php';
// Close the database connection
mysqli_close($conn);
?>