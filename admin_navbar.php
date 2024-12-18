<!-- admin_navbar.php -->
<?php
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

function getCurrentPage() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return str_replace('.php', '', $currentPage);
}

?>
<style>
        .navbar-logo {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .navbar-logo img {
            height: 50px;
            width: auto;
            margin-right: 10px;
            border-radius: 5px;
        }
</style>

<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <div class="navbar-logo" onclick="navigateHome()">
            <img src="images/ABAHUBLOGO_white.png " alt="ABAHUB Logo">
        </div>
        <ul class="flex space-x-4">
            <li><a href="admin_dashboard.php" class="text-white hover:text-gray-300">Dashboard</a></li>
            <li><a href="admin_teams.php" class="text-white hover:text-gray-300">Teams</a></li>
            <li><a href="admin_players.php" class="text-white hover:text-gray-300">Players</a></li>
            <li><a href="admin_matches.php" class="text-white hover:text-gray-300">Matches</a></li>
            <li><a href="admin_users.php" class="text-white hover:text-gray-300">Users</a></li>
        </ul>
        <div>
            <span class="text-white mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="admin_login.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Logout</a>
        </div>
    </div>
</nav>

<script>        
    function navigateHome() {
                window.location.href = 'admin_dashboard.php';
        }
</script>