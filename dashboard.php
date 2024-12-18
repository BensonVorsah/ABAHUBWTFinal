<?php
include 'navbar1.php';
require_once 'db_connection.php';

// Fetch dynamic content for each section
$recent_results = fetchRecentResults($conn);
$upcoming_games = fetchUpcomingGames($conn);
$teams = fetchTeams($conn);
$league_standings = fetchLeagueStandings($conn);
$stats_leaders = fetchStatsLeaders($conn);
$award_holders = fetchAwardHolders($conn);
/* $media_posts = fetchInstagramPosts(); */
function fetchWelcomeImages() {
    return [
        ['image_path' => 'images/curry.jpg', 'image_description' => 'Basketball Action'],
        ['image_path' => 'images/court 1.jpg', 'image_description' => 'Team Huddle'],
        ['image_path' => 'images/bg1.png', 'image_description' => 'Championship Moment']
    ];
}

$welcome_images = fetchWelcomeImages(); // Add this line


function fetchRecentResults($conn) {
    $query = "SELECT m.match_id, t1.team_name AS team1_name, t2.team_name AS team2_name,
                    t1.team_logo AS team1_logo, t2.team_logo AS team2_logo, m.team1_score, m.team2_score, m.match_date
              FROM matches m
              JOIN teams t1 ON m.team1_id = t1.team_id
              JOIN teams t2 ON m.team2_id = t2.team_id
              WHERE m.match_date < CURDATE()
              ORDER BY m.match_date DESC
              LIMIT 2";

    $result = mysqli_query($conn, $query);
    if (!$result){
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


function fetchUpcomingGames($conn) {
    $query = "SELECT m.match_id, t1.team_name AS team1_name, t2.team_name AS team2_name,
                     m.match_date, t1.team_logo AS team1_logo, t2.team_logo AS team2_logo
             FROM matches m
             JOIN teams t1 ON m.team1_id = t1.team_id
             JOIN teams t2 ON m.team2_id = t2.team_id
             WHERE m.match_date >= CURDATE()
             ORDER BY m.match_date ASC
             LIMIT 4";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetchTeams($conn) {
    $query = "SELECT team_id, team_name, team_logo FROM teams";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetchLeagueStandings($conn) {
    $query = "SELECT  ranking, team_logo, team_name, wins, losses, games_played, diff FROM teams ORDER BY ranking";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetchStatsLeaders($conn) {
    $query = "SELECT
        p.Fname,
        p.Lname, 
        MAX(ps.PPG) as max_ppg, 
        MAX(ps.APG) as max_apg, 
        MAX(ps.RPG) as max_rpg, 
        MAX(ps.SPG) as max_spg, 
        MAX(ps.BPG) as max_bpg
    FROM playerstats ps
    JOIN players p ON ps.player_id = p.player_id
    GROUP BY p.player_id, p.Fname, p.Lname
    ORDER BY max_ppg DESC   
    LIMIT 5";

    $result = mysqli_query($conn, $query);
    
    // Add error handling
    if (!$result) {
        error_log("Database error: " . mysqli_error($conn));
        return [];
    }
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetchAwardHolders($conn) {
    $query = "SELECT 
        a.award_name,
        p.Fname,
        p.Lname,
        p.player_image 
    FROM winners w
    JOIN awards a ON w.award_id = a.award_id
    JOIN players p ON w.player_id = p.player_id
    ORDER BY w.award_id
    LIMIT 8";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// YouTube Data API Configuration
// IMPORTANT: You must get a YouTube Data API v3 key from Google Cloud Console
$youtubeApiKey = 'AIzaSyBZlQiRoTbQsTc97ctKpPZgYwb4B0n5e1M';

// Playlist ID for 2024-2025 NBA SEASON from Hooper Highlights
$playlistId = 'PLXlvFN0gKJA5nXpVeX9Uq999DbKREL446';

// Function to fetch playlist items from YouTube
function fetchYouTubePlaylistVideos($apiKey, $playlistId, $maxResults = 3) {
    $apiUrl = "https://www.googleapis.com/youtube/v3/playlistItems?" . http_build_query([
        'part' => 'snippet',
        'playlistId' => $playlistId,
        'key' => $apiKey,
        'maxResults' => $maxResults
    ]);

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if(curl_errno($ch)){
        // Log or handle the error
        return [];
    }

    // Close cURL resource
    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Check if items exist
    $videos = [];
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $item) {
            $videos[] = [
                'title' => $item['snippet']['title'],
                'video_id' => $item['snippet']['resourceId']['videoId'],
                'thumbnail' => $item['snippet']['thumbnails']['medium']['url']
            ];
        }
    }

    return $videos;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ABA Hub Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="dashboard.js"></script>
</head>
<body>
<div class="dashboard-container">
        <!-- Welcome Section -->
        <section id="welcome" class="dashboard-section">
            <div class="welcome-content">
                <div class="welcome-left">
                    <h1>Welcome to ABAHUB</h1>
                    <p>ABAHUB is home to all things ABA. The number 1 Hub of the ABA.</p>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-login">Login</a>
                        <a href="signup.php" class="btn btn-signup">Sign Up</a>
                    </div>
                </div>
                
                <div class="welcome-right">
                    <h2>Explore ABA Basketball</h2>
                    <p>Get real-time updates, stats, and highlights from your favorite teams and players.</p>
                </div>
            </div>
        </section>

        <!-- Results Section -->
        <section id="results" class="dashboard-section">
            <h2>Recent Results</h2>
            <div class="results-container">
                <?php foreach($recent_results as $result): ?>
                    <div class="result-card" onclick="window.location.href='game.php?id=<?php echo $result['match_id']; ?>'">
                        <div class="team-home">
                            <img src= "<?php echo htmlspecialchars($result['team1_logo']); ?>" alt="<?php echo htmlspecialchars($results['team1_logo'])?>">
                            <?php echo htmlspecialchars($result['team1_name']); ?>
                        </div>
                        <div class="score">
                            <?php echo htmlspecialchars($result['team1_score']); ?> - 
                            <?php echo htmlspecialchars($result['team2_score']); ?>
                        </div>
                        <div class="team-away">
                            <?php echo htmlspecialchars($result['team2_name']); ?>
                            <img src= "<?php echo htmlspecialchars($result['team2_logo']); ?>" alt="<?php echo htmlspecialchars($results['team2_logo'])?>">
                        </div>
                        <div class="match-date">
                            <?php echo htmlspecialchars($result['match_date']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ABA Schedule Section -->
        <section id="schedule" class="dashboard-section">
            <h2>Upcoming Games</h2>
            <div class="schedule-container">
                <?php foreach($upcoming_games as $game): ?>
                    <div class="game-card" onclick="window.location.href='game.php?id=<?php echo $game['match_id']; ?>'">
                        <div class="game-date">
                            <?php echo htmlspecialchars($game['match_date']); ?>
                        </div>
                        <div class="teams">
                            <div class="team-home">
                                <img src="<?php echo htmlspecialchars($game['team1_logo']); ?>" alt="<?php echo htmlspecialchars($game['team1_name']); ?>">
                                <?php echo htmlspecialchars($game['team1_name']); ?>
                            </div>
                            <div class="vs">VS</div>
                            <div class="team-away">
                                <img src="<?php echo htmlspecialchars($game['team2_logo']); ?>" alt="<?php echo htmlspecialchars($game['team2_name']); ?>">
                                <?php echo htmlspecialchars($game['team2_name']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ABA Teams Section -->
        <section id="teams" class="dashboard-section">
            <h2>ABA Teams</h2>
            <div class="teams-container">
                <?php foreach($teams as $team): ?>
                    <div class="team-card" onclick="window.location.href='team.php?id=<?php echo $team['team_id']; ?>'">
                        <img src="<?php echo htmlspecialchars($team['team_logo']); ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?>">
                        <div class="team-name"><?php echo htmlspecialchars($team['team_name']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- League Standings Section -->
        <section id="standings" class="dashboard-section">
            <h2>League Standings</h2>
            <table class="standings-table" onclick="window.location.href='standings.php'">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Logo</th>
                        <th>Team</th>
                        <th>W</th>
                        <th>L</th>
                        <th>GP</th>
                        <th>Diff</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach($league_standings as $standing): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($standing['ranking']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($standing['team_logo']); ?>"></td>
                            <td><?php echo htmlspecialchars($standing['team_name']); ?></td>
                            <td><?php echo htmlspecialchars($standing['wins']); ?></td>
                            <td><?php echo htmlspecialchars($standing['losses']); ?></td>
                            <td><?php echo htmlspecialchars($standing['games_played']); ?></td>
                            <td><?php echo htmlspecialchars($standing['diff']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Player Stats Leaders Section -->
        <section id="stats-leaders" class="dashboard-section">
            <h2>Stats Leaders</h2>
            <div class="stats-container">
                <table class="stats-leaders-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>PPG</th>
                            <th>APG</th>
                            <th>RPG</th>
                            <th>SPG</th>
                            <th>BPG</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($stats_leaders as $leader): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($leader['Fname'] . ' ' . $leader['Lname']); ?></td>
                            <td><?php echo number_format($leader['max_ppg'], 1); ?></td>
                            <td><?php echo number_format($leader['max_apg'], 1); ?></td>
                            <td><?php echo number_format($leader['max_rpg'], 1); ?></td>
                            <td><?php echo number_format($leader['max_spg'], 1); ?></td>
                            <td><?php echo number_format($leader['max_bpg'], 1); ?></td>
                            </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="player-profile-hover">
                    <!-- Dynamic player profile will be populated via JavaScript -->
                </div>
            </div>
        </section>

        <!-- Awards Section -->
        <section id="awards" class="dashboard-section">
            <h2>Current Award Holders</h2>
            <div class="awards-container" onclick="window.location.href='awards.php'">
                <?php foreach($award_holders as $award): ?>
                    <div class="award-card">
                        <div class="award-name">
                            <?php echo htmlspecialchars($award['award_name']); ?>
                        </div>
                        <div class="player-image">
                            <img scr="<?php echo htmlspecialchars($award['player_image']); ?>" alt="<?php echo htmlspecialchars($award['Fname']. ' ' .$award['Lname']); ?>">
                            <div class="player-name">
                                <?php echo htmlspecialchars($award['Fname']); ?>
                                <?php echo htmlspecialchars($award['Lname']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        
        <section id="media" class="dashboard-section">
            <h2>Latest Highlights</h2>
            <div class="media-container">
                <?php 
                $videos = fetchYouTubePlaylistVideos($youtubeApiKey, $playlistId);
                
                foreach($videos as $video): 
                ?>
                    <div class="media-card">
                        <iframe
                            class="video-embed"
                            src="https://www.youtube.com/embed/<?= htmlspecialchars($video['video_id']) ?>"
                            title="<?= htmlspecialchars($video['title']) ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all-media">
                <a href="media.php" class="btn btn-view-all">View All Highlights</a>
            </div>
        </section>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>