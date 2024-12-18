<?php

session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Fetch some key statistics
$stats_queries = [
    'total_players' => "SELECT COUNT(*) as count FROM players",
    'total_teams' => "SELECT COUNT(*) as count FROM teams",
    'total_users' => "SELECT COUNT(*) as count FROM users",
    'total_matches' => "SELECT COUNT(*) as count FROM matches",
    'upcoming_matches' => "SELECT m.match_id, t1.team_name as team1, t2.team_name as team2, m.match_date 
                            FROM matches m 
                            JOIN teams t1 ON m.team1_id = t1.team_id 
                            JOIN teams t2 ON m.team2_id = t2.team_id 
                            WHERE m.team1_score IS NULL AND m.team2_score IS NULL 
                            ORDER BY m.match_date LIMIT 5"
];

$stats = [];
foreach ($stats_queries as $key => $query) {
    $result = mysqli_query($conn, $query);
    $stats[$key] = ($key === 'upcoming_matches') 
        ? mysqli_fetch_all($result, MYSQLI_ASSOC) 
        : mysqli_fetch_assoc($result)['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - ABA Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <div>
                <span class="mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="admin_logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Logout</a>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Total Players</h3>
                <p class="text-3xl font-bold"><?php echo $stats['total_players']; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Total Teams</h3>
                <p class="text-3xl font-bold"><?php echo $stats['total_teams']; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Total Users</h3>
                <p class="text-3xl font-bold"><?php echo $stats['total_users']; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Total Matches</h3>
                <p class="text-3xl font-bold"><?php echo $stats['total_matches']; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Upcoming Matches</h3>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2">Match</th>
                            <th class="p-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['upcoming_matches'] as $match): ?>
                        <tr>
                            <td class="p-2 text-center"><?php echo htmlspecialchars($match['team1'] . ' vs ' . $match['team2']); ?></td>
                            <td class="p-2 text-center"><?php echo date('M d, Y H:i', strtotime($match['match_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="admin_players.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        Manage Players
                    </a>
                    <a href="admin_users.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                        Manage Users
                    </a>
                    <a href="admin_matches.php" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">
                        Manage Matches
                    </a>
                    <a href="admin_teams.php" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                        Manage Teams
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
include 'footer.php';
?>