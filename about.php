<?php
include 'navbar1.php';
require_once 'db_connection.php';
require_once 'Parsedown.php'; // Make sure to include Parsedown library for markdown parsing
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About ABA Hub | Ashesi Basketball Association</title>
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="about-container">
        <header class="about-header">
            <div class="header-overlay">
                <h1>About ABA Hub</h1>
                <p>Empowering Athletes, Inspiring Futures</p>
            </div>
        </header>

        <main class="about-content">
            <?php
            // Read the markdown content
            $markdownContent = file_get_contents('about-page.md');

            // Initialize Parsedown
            $Parsedown = new Parsedown();
            $Parsedown->setSafeMode(true);

            // Convert markdown to HTML
            echo $Parsedown->text($markdownContent);
            ?>
        </main>

        <section class="gallery-section">
            <h2>Our Basketball Journey</h2>
            <div class="image-gallery">
                <div class="gallery-item">
                    <img src="images/curry.jpg" alt="Team Practice">
                    <p>Intense Training Sessions</p>
                </div>
                <div class="gallery-item">
                    <img src="images/court 1.jpg" alt="Championship Moment">
                    <p>Celebrating Victory</p>
                </div>
                <div class="gallery-item">
                    <img src="images/wpp (1).jpg" alt="Team Huddle">
                    <p>Teamwork in Action</p>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content">
                <h2>Ready to Be Part of Something Special?</h2>
                <p>Join our community, support our athletes, and be part of the ABA legacy.</p>
                <div class="cta-buttons">
                    <a href="signup.php" class="btn btn-primary">Join ABA Hub</a>
                    <a href="contact.php" class="btn btn-secondary">Contact Us</a>
                </div>
            </div>
        </section>
    </div>
    <script src="about.js"></script>


</body>
</html>
<?php
include 'footer.php'
?>