<?php
include 'navbar1.php';
require_once 'db_connection.php';

// Check if player_id is set
if (isset($_GET['player_id'])) {
    $player_id = intval($_GET['player_id']);

    $query = "
        SELECT 
            p.player_id, 
            p.Fname, 
            p.Lname, 
            p.positions, 
            p.height, 
            p.bio,
            t.team_name,
            ps.points,
            ps.assists,
            ps.rebounds,
            ps.steals,
            ps.blocks
        FROM players p
        JOIN teams t ON p.team_id = t.team_id
        JOIN playerstats ps ON p.player_id = ps.player_id
        WHERE p.player_id = $player_id
    ";

    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $profile = mysqli_fetch_assoc($result);
        echo json_encode($profile);
    } else {
        echo json_encode(['error' => 'Player not found']);
    }
} else {
    echo json_encode(['error' => 'No player ID provided']);
}
?>


<antArtifact identifier="db-connection-php" type="application/vnd.ant.code" language="php" title="Database Connection File">
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abahubdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

include 'footer.php';
?>