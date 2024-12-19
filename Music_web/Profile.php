<?php
session_start();
require 'connection.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Sign_in.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare('SELECT username, email, profile_picture FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = $_POST['new_username'];
        $new_email = $_POST['new_email'];

        // Update username and email
        $stmt = $conn->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        $stmt->bind_param('ssi', $new_username, $new_email, $user_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            $user['username'] = $new_username;
            $user['email'] = $new_email;
        } else {
            $message = "Error updating profile.";
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        $stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();

        if (password_verify($current_password, $user_data['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->bind_param('si', $hashed_password, $user_id);
                if ($stmt->execute()) {
                    $message = "Password changed successfully!";
                } else {
                    $message = "Error changing password.";
                }
            } else {
                $message = "New passwords do not match.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    } elseif (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            // Ensure the uploads directory exists
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Validate file type and size (e.g., only allow images, and limit to 2MB)
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($file['type'], $allowed_types) && $file['size'] <= 2 * 1024 * 1024) {
                $file_name = uniqid() . "_" . basename($file['name']);
                $upload_file = $upload_dir . $file_name;

                // Move the file to the upload directory
                if (move_uploaded_file($file['tmp_name'], $upload_file)) {
                    // Update profile picture path in database
                    $stmt = $conn->prepare('UPDATE users SET profile_picture = ? WHERE id = ?');
                    $stmt->bind_param('si', $file_name, $user_id);
                    if ($stmt->execute()) {
                        $message = "Profile picture updated successfully!";
                        $user['profile_picture'] = $file_name;
                    } else {
                        $message = "Error updating profile picture.";
                    }
                } else {
                    $message = "Error uploading file.";
                }
            } else {
                $message = "Invalid file type or size. Please upload a valid image file (JPEG, PNG, GIF) under 2MB.";
            }
        } else {
            $message = "No file uploaded or upload error.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-color: #f0f2f5;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    color: #333;
    margin-bottom: 20px;
}

h1 {
    font-size: 2rem;
}

h2 {
    font-size: 1.5rem;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #dcdcdc;
    border-radius: 6px;
    font-size: 1rem;
}

input[type="submit"] {
    background-color: #007bff;
    color: #ffffff;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}

.message {
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

.profile-picture {
    text-align: center;
    margin-bottom: 30px;
}

.profile-picture img {
    border-radius: 50%;
    border: 3px solid #ddd;
    width: 120px;
    height: 120px;
    object-fit: cover;
}

.profile-picture input[type="file"] {
    margin-top: 10px;
}
		
.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #ff5f5f;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 20px 0;
}

.back-btn:hover {
    background-color: #ff9999;
    transform: translateX(-10px); /* Slide effect to the left on hover */
}

.back-btn:active {
    transform: scale(0.95); /* Slight zoom out effect on click */
}

.back-btn:focus {
    outline: none;
}

    </style>
</head>
<body>
	<button class="back-btn" onclick="goBack()">&#x2190; Back</button>
    <div class="container">
        <h1>User Profile</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2>Current Information</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        
        <div class="profile-picture">
            <?php if ($user['profile_picture']): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="uploads/default_profile.png" alt="Default Profile Picture">
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*" required>
                <input type="submit" value="Upload Profile Picture">
            </form>
        </div>

        <h2>Update Profile</h2>
        <form method="POST">
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            
            <label for="new_email">New Email:</label>
            <input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            
            <input type="submit" name="update_profile" value="Update Profile">
        </form>

        <h2>Change Password</h2>
        <form method="POST">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
            
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <input type="submit" name="change_password" value="Change Password">
        </form>
    </div>
</body>
	<script>
	    
		function goBack() {
          window.history.back();
}
	</script>
</html>