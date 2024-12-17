<?php
include 'db_connection.php';
include 'navbar1.php';

if ($user) {
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    
    // Redirect to a secure page
    header("Location: games.php");
    exit();
}


// Fetch upcoming matches
$query = "SELECT m.match_id, 
                 t1.team_id AS home_team_id, 
                 t2.team_id AS away_team_id,
                 t1.team_name AS home_team, 
                 t2.team_name AS away_team, 
                 t1.team_logo AS home_logo, 
                 t2.team_logo AS away_logo, 
                 m.match_date
          FROM Matches m
          JOIN Teams t1 ON m.team1_id = t1.team_id
          JOIN Teams t2 ON m.team2_id = t2.team_id
          WHERE m.match_date > NOW()
          ORDER BY m.match_date";

$result = $conn->query($query);
$upcomingMatches = $result->fetch_all(MYSQLI_ASSOC);

// Check if prediction is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $match_id = $_POST['match_id'];
    $predicted_team_id = $_POST['predicted_team_id'];
    $user_id = $_SESSION['user_id'];

    // Validate user_id exists in users table
    $userCheckQuery = "SELECT 1 FROM users WHERE user_id = ?";
    $userCheckStmt = $conn->prepare($userCheckQuery);
    $userCheckStmt->bind_param("i", $user_id);
    $userCheckStmt->execute();
    $userCheckResult = $userCheckStmt->get_result();

    if ($userCheckResult->num_rows === 0) {
        // Log the error
        error_log("Prediction attempt with invalid user ID: $user_id");
        
        // Destroy session and redirect to login
        session_destroy();
        header("Location: login.php?error=invalid_user");
        exit();
    }

    // Validate match exists
    $matchCheckQuery = "SELECT 1 FROM Matches WHERE match_id = ?";
    $matchCheckStmt = $conn->prepare($matchCheckQuery);
    $matchCheckStmt->bind_param("i", $match_id);
    $matchCheckStmt->execute();
    $matchCheckResult = $matchCheckStmt->get_result();

    if ($matchCheckResult->num_rows === 0) {
        $message = "Invalid match selected.";
        // You might want to log this as well
        error_log("Prediction attempt with invalid match ID: $match_id by user $user_id");
    } else {
        // Determine if it's a home team prediction
        $matchQuery = "SELECT team1_id, team2_id FROM Matches WHERE match_id = ?";
        $stmt = $conn->prepare($matchQuery);
        $stmt->bind_param("i", $match_id);
        $stmt->execute();
        $matchResult = $stmt->get_result()->fetch_assoc();
        $is_home_team = ($predicted_team_id == $matchResult['team1_id']);

        // Check if user has already predicted this match
        $checkPredictionQuery = "SELECT * FROM Predictions 
                                 WHERE user_id = ? AND match_id = ?";
        $checkStmt = $conn->prepare($checkPredictionQuery);
        $checkStmt->bind_param("ii", $user_id, $match_id);
        $checkStmt->execute();
        $existingPrediction = $checkStmt->get_result()->fetch_assoc();

        try {
            if ($existingPrediction) {
                // Update existing prediction
                $updateQuery = "UPDATE Predictions 
                                SET predicted_team_id = ?, is_home_team = ?
                                WHERE user_id = ? AND match_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("iiii", $predicted_team_id, $is_home_team, $user_id, $match_id);
                $updateStmt->execute();
                $message = "Prediction updated successfully!";
            } else {
                // Insert new prediction
                $insertQuery = "INSERT INTO Predictions 
                                (user_id, match_id, predicted_team_id, is_home_team) 
                                VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("iiib", $user_id, $match_id, $predicted_team_id, $is_home_team);
                $insertStmt->execute();
                $message = "Prediction submitted successfully!";
            }
        } catch (mysqli_sql_exception $e) {
            // Log the specific error
            error_log("Prediction insertion error: " . $e->getMessage());
            $message = "An error occurred while submitting your prediction. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Predictions</title>
    <link rel="stylesheet" href="stylesgames.css">
    <style>
        .prediction-form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }
        .prediction-form form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 600px;
        }
        .match-prediction {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .team-prediction {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .team-prediction img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .prediction-submit {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #dff0d8;
            color: #3c763d;
        }
    </style>
</head>
<body>
    <div class="prediction-container">
        <h1>Upcoming Matches Predictions</h1>
        
        <?php if(isset($message)): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if(empty($upcomingMatches)): ?>
            <p>No upcoming matches available for prediction.</p>
        <?php else: ?>
            <?php foreach($upcomingMatches as $match): ?>
                <div class="prediction-form">
                    <form method="POST" action="">
                        <div class="match-prediction">
                            <div class="team-prediction home-team">
                                <img src="<?php echo htmlspecialchars($match['home_logo']); ?>" 
                                     alt="<?php echo htmlspecialchars($match['home_team']); ?>">
                                <label>
                                    <input type="radio" 
                                           name="predicted_team_id" 
                                           value="<?php echo $match['home_team_id']; ?>" 
                                           required>
                                    <?php echo htmlspecialchars($match['home_team']); ?>
                                </label>
                            </div>
                            <div class="match-details">
                                <p>Date: <?php echo date('F j, Y', strtotime($match['match_date'])); ?></p>
                                <p>Time: <?php echo date('H:i A', strtotime($match['match_date'])); ?></p>
                            </div>
                            <div class="team-prediction away-team">
                                <img src="<?php echo htmlspecialchars($match['away_logo']); ?>" 
                                     alt="<?php echo htmlspecialchars($match['away_team']); ?>">
                                <label>
                                    <input type="radio" 
                                           name="predicted_team_id" 
                                           value="<?php echo $match['away_team_id']; ?>" 
                                           required>
                                    <?php echo htmlspecialchars($match['away_team']); ?>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="match_id" value="<?php echo $match['match_id']; ?>">
                        <button type="submit" class="prediction-submit">Submit Prediction</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
include 'footer.php';
// Close the database connection
mysqli_close($conn);
?>