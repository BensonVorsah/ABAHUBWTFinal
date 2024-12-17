<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user role from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Check if the user is an ABA Star
if ($role !== 'ABA Star') {
    header("Location: access_denied.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Admin Panel</h1>

    <h2>Correct Player Stats</h2>
    <form action="admin.php" method="post">
        <label for="player_id">Player ID:</label>
        <input type="text" id="player_id" name="player_id" required>
        <label for="game_id">Game ID:</label>
        <input type="text" id="game_id" name="game_id" required>
        <label for="points">Points:</label>
        <input type="text" id="points" name="points" required>
        <label for="rebounds">Rebounds:</label>
        <input type="text" id="rebounds" name="rebounds" required>
        <label for="assists">Assists:</label>
        <input type="text" id="assists" name="assists" required>
        <button type="submit" name="correct_stats">Correct Stats</button>
    </form>

    <?php
    if (isset($_POST['correct_stats'])) {
        $player_id = $_POST['player_id'];
        $game_id = $_POST['game_id'];
        $points = $_POST['points'];
        $rebounds = $_POST['rebounds'];
        $assists = $_POST['assists'];

        $sql = "UPDATE playerstats SET points = ?, rebounds = ?, assists = ? WHERE player_id = ? AND match_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiii", $points, $rebounds, $assists, $player_id, $game_id);
        $stmt->execute();
        $stmt->close();

        echo "<p>Stats corrected successfully!</p>";
    }
    ?>

    <h2>Record Game Results</h2>
    <form action="admin.php" method="post">
        <label for="match_id">Match ID:</label>
        <input type="text" id="match_id" name="match_id" required>
        <label for="team1_score">Team 1 Score:</label>
        <input type="text" id="team1_score" name="team1_score" required>
        <label for="team2_score">Team 2 Score:</label>
        <input type="text" id="team2_score" name="team2_score" required>
        <button type="submit" name="record_game">Record Game</button>
    </form>

    <?php
    if (isset($_POST['record_game'])) {
        $match_id = $_POST['match_id'];
        $team1_score = $_POST['team1_score'];
        $team2_score = $_POST['team2_score'];

        $sql = "UPDATE matches SET team1_score = ?, team2_score = ? WHERE match_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $team1_score, $team2_score, $match_id);
        $stmt->execute();
        $stmt->close();

        echo "<p>Game results recorded successfully!</p>";
    }
    ?>
</body>
</html>
