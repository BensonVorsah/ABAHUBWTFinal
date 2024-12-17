<?php
// Include necessary files
include 'navbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

// Start session to access user data 
session_start();

// Get the current user's ID from session 
$userId = $_SESSION['user_id']; 

// Initialize success message
$successMessage = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $interests = $_POST['interests'];
    $skills = $_POST['skills'];
    $profileImage = $_FILES['profile_image']['name']; // New image file name

    // Handle profile image upload (if a new image is uploaded)
    if (!empty($profileImage)) {
        // Specify the target directory for the image upload
        $targetDir = 'uploads/';
        $targetFile = $targetDir . basename($profileImage);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);
    }

    // SQL query to update the user's profile information
    $query = "UPDATE users SET interests = ?, skills = ?";
    
    // Add the image update part if a new profile image is uploaded
    if (!empty($profileImage)) {
        $query .= ", profile_image = ?";
    }
    
    $query .= " WHERE userID = ?";

    // Prepare and execute the SQL query
    if ($stmt = $conn->prepare($query)) {
        if (!empty($profileImage)) {
            $stmt->bind_param("sssi", $interests, $skills, $profileImage, $userId); // Bind the parameters
        } else {
            $stmt->bind_param("ssi", $interests, $skills, $userId); // Bind the parameters without the image
        }
        $stmt->execute(); // Execute the query
        $stmt->close(); // Close the statement

        // Set success message
        $successMessage = "Your profile has been updated successfully!";
    }
}

// Query to get the user's profile information from the users table
$query = "SELECT u.first_name, u.last_name, u.gender, u.email, u.role, u.profile_image, u.interests, u.skills, m.majorName 
          FROM users u
          LEFT JOIN Major m ON u.majorId = m.majorId
          WHERE u.userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId); // Bind the user ID parameter
$stmt->execute();
$result = $stmt->get_result(); // Get the result of the query

// Fetch the user data from the result
$userData = $result->fetch_assoc();

// Check if user data exists
if (!$userData) {
    // Handle case where user data doesn't exist (e.g., user not found)
    echo "User not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | ABAHUB </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Edit User Profile</h1>

            <!-- Success Message -->
            <?php if ($successMessage): ?>
                <div class="success-message">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <form class="profile-form" action="profilepage.php" method="post" enctype="multipart/form-data">
                <!-- Profile Image -->
                <div class="form-group">
                    <label for="profile-image">Profile Image</label>
                    <div class="profile-image-container">
                        <img src="uploads/<?php echo htmlspecialchars($userData['profile_image']); ?>" alt="Profile Image" class="profile-img">
                        <input type="file" id="profile-image" name="profile_image" accept="image/*">
                    </div>
                </div>

                <!-- Final Fields -->
                <div class="form-group">
                    <label for="student-id">Student ID</label>
                    <input type="text" id="student-id" name="student_id" value="<?php echo htmlspecialchars($userData['userID']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($userData['gender']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="major">Major</label>
                    <input type="text" id="major" name="major" value="<?php echo htmlspecialchars($userData['majorName']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($userData['role']); ?>" readonly>
                </div>

                <!-- Editable Fields -->
                <div class="form-group">
                    <label for="interests">Interests</label>
                    <textarea id="interests" name="interests" rows="3" placeholder="Enter your interests"><?php echo htmlspecialchars($userData['interests']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="skills">Skills</label>
                    <textarea id="skills" name="skills" rows="3" placeholder="Enter your skills"><?php echo htmlspecialchars($userData['skills']); ?></textarea>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>

<?php
include 'footer.php';
?>
