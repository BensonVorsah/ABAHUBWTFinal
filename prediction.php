<?php 
include 'navbar1'; 
include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABAHUB</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>

    <div class="container">
        <div class="match">
            <div class="match-header">
                <div class="match-status">Live</div>
                <div class="match-tournament"><img src="images/ABA Logo.png" alt="ABA">
                    Ashesi Basketball Association</div>
                <div class="match-action"></div>
            </div>
            <div class="match-content">
                <div class="column">
                    <div class="team team--home">
                        <div class="team-logo">
                            <img src="images/team_logos/Ash_Knights_Logo.png" alt="AshKnights">
                        </div>
                        <h2 class="team-name">AshKnights</h2>
                    </div>
                </div>

                <div class="column">
                    <div class="match-details">
                        <div class="match-date">
                            1 May at <strong>18:30</strong>
                        </div>
                        <div class="match-score">
                            <span class="match-score-number match-score-number--leading">64</span>
                            <span class="match-score-divider">:</span>
                            <span class="match-score-number">51</span>
                        </div>

                        <div class="match-time-lapsed">4Q 06:32'</div>
                        <div class="match-referee">
                            Referee: <strong>Sniper</strong>
                        </div>
                        <div class="match-predict-options">
                            <button class="match-predict-option">1</button>
                            <button class="match-predict-option">X</button>
                            <button class="match-predict-option">2</button>
                        </div>
                        <button class="match-predict-place">Make a prediction</button>
                    </div>
                </div>

                <div class="column">
                    <div class="team team--away">
                        <div class="team-logo">
                            <img src="images/team_logos/Berekuso_Warriors_logo_edited.png" alt="westham">
                        </div>
                        <h2 class="team-name">Warriors</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>