<?php
// Navbar.php - ABAHUB Navbar Component
session_start(); // Start session to manage user login state

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Redirect logic (you'll need to implement actual authentication)
if (!$isLoggedIn && !in_array(basename($_SERVER['PHP_SELF']), ['index.php', 'login.php', 'signup.php'])) {
    header('Location: login.php');
    exit();
}

// Function to get user profile picture or default
function getUserProfilePic() {
    return isset($_SESSION['profile_pic']) && !empty($_SESSION['profile_pic']) 
        ? $_SESSION['profile_pic'] 
        : 'images/ABAHUBLOGO_black.png';
}

// Function to get current page
function getCurrentPage() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return str_replace('.php', '', $currentPage);
}
?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABAHUB - Ashesi Basketball Association Hub</title>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS Variables for Color Scheme -->
    <style>
        :root {
            --color-text-primary: #1c2a38;
            --color-text-secondary: #8A8F98;
            --color-text-alert: #d72641;
            --color-text-icon: #dbdade;
            --color-bg-primary: #fff;
            --color-bg-secondary: #f3f5f9;
            --color-bg-alert: #fdeaec;
            --color-theme-primary: #1d428a;
        }

        /* Navbar Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: var(--color-theme-primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

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

        .navbar-logo span {
            font-weight: bold;
            color: var(--color-theme-primary);
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .navbar-menu {
            display: flex;
            gap: 80px;
        }

        .navbar-menu a {
            text-decoration: none;
            color: var(--color-text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-menu a.active {
            color: white;
            font-weight: bold;
            position: relative;
        }

        .navbar-menu a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--color-theme-primary);
            transition: width 0.3s ease;
        }

        .navbar-menu a:hover {
            color: var(--color-bg-primary);
        }

        .navbar-menu a:hover::after {
            width: 100%;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-actions .icon {
            cursor: pointer;
            color: var(--color-text-secondary);
            transition: color 0.3s ease;
            font-size: 1.2rem;
        }

        .navbar-actions .icon:hover {
            color: var(--color-theme-primary);
        }

        .profile-icon img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--color-bg-secondary);
            transition: transform 0.3s ease;
        }

        .profile-icon img:hover {
            transform: scale(1.1);
        }

        .profile-popup {
            display: none;
            position: absolute;
            right: 30px;
            top: 70px;
            background-color: var(--color-bg-primary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 10px;
            width: 200px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-popup.show {
            display: block;
        }

        .profile-popup a {
            display: flex;
            align-items: center;
            padding: 10px;
            text-decoration: none;
            color: var(--color-text-primary);
            transition: background-color 0.3s ease;
        }

        .profile-popup a i {
            margin-right: 10px;
            color: var(--color-theme-primary);
        }

        .profile-popup a:hover {
            background-color: var(--color-bg-secondary);
        }

        /* Contacts Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--color-bg-primary);
            padding: 30px;
            border-radius: 10px;
            width: 500px;
            max-width: 90%;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <!-- Logo Section -->
        <div class="navbar-logo" onclick="navigateHome()">
            <img src="images/ABAHUBLOGO_white.png " alt="ABAHUB Logo">
            <span>ABAHUB</span>
        </div>

        <!-- Navigation Menu -->
        <div class="navbar-menu">
            <a href="games.php" class="<?php echo (getCurrentPage() === 'games')? 'active' : ''; ?>">Games</a>
            <a href="standings.php"  class="<?php echo (getCurrentPage() === 'standings')? 'active' : ''; ?>">Standings</a>
            <a href="stats.php" class="<?php echo (getCurrentPage() === 'stats')? 'active' : ''; ?>">Stats</a>
            <a href="teams.php"  class="<?php echo (getCurrentPage() === 'teams')? 'active' : ''; ?>">Teams</a>
            <a href="players.php"  class="<?php echo (getCurrentPage() === 'players')? 'active' : ''; ?>">Players</a>
            <a href="awards.php"  class="<?php echo (getCurrentPage() === 'awards')? 'active' : ''; ?>">Awards</a>
            <a href="media.php"  class="<?php echo (getCurrentPage() === 'media')? 'active' : ''; ?>">Media</a>
            <a href="about.php" class="<? echo(getCurrentPage() === 'about')? 'active' : ''; ?>">About</a>
        </div>

        <!-- Actions Section -->
        <div class="navbar-actions">

            <!-- Contacts Icon -->
            <div class="icon contacts-icon" onclick="openContactsPage()">
                <i class="fas fa-address-book"></i>
            </div>

            <!-- Profile Picture -->
            <div class="icon profile-icon" onclick="toggleProfilePopup()">
                <img src="<?php echo getUserProfilePic(); ?>" alt="Profile">
            </div>
        </div>

        <!-- Profile Popup -->
        <div class="profile-popup" id="profilePopup">
            <?php if ($isLoggedIn): ?>
                <a href="login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>



    <script>
        // Toggle Profile Popup
        function toggleProfilePopup() {
            const popup = document.getElementById('profilePopup');
            popup.classList.toggle('show');
        }

        // Function to navigate home
        function navigateHome() {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'dashboard.php';
            <?php else: ?>
                window.location.href = 'index.php';
            <?php endif; ?>
        }

        // Open Contacts Modal
        function openContactsPage() {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'contact.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }

        // Close Contacts Modal
        document.getElementById('contactsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });

        // Close Profile Popup when clicking outside
        window.addEventListener('click', function(e) {
            const popup = document.getElementById('profilePopup');
            if (!e.target.closest('.profile-icon') && popup.classList.contains('show')) {
                popup.classList.remove('show');
            }
        });
    </script>
</body>
</html>