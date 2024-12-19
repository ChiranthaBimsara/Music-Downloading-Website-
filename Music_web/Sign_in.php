<?php
require 'connection.php'; // Database connection

// Initialize error message
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if the email exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email exists, check the password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // If password is correct, start a session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to the dashboard or home page
            header('Location: Home.php');
            exit;
        } else {
            // If password is incorrect, set an error message
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        // If email does not exist, set an error message
        $error_message = "Email not found. Please register first.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Website</title>
	<style>
	/* General body styling */
	body {
	  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	  background-color: #f4f7f8;
	  margin: 0;
	  padding: 0;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	  height: 100vh;
	}

	/* Container styling */
	.container {
	  width: 400px;
	  padding: 40px;
	  background-color: #ffffff;
	  box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
	  border-radius: 8px;
	  animation: fadeIn 1s ease-in-out;
	}

	/* Form title */
	h2 {
	  margin-bottom: 20px;
	  text-align: center;
	  color: #333;
	  font-weight: bold;
	}

	/* Form group styling */
	.form-group {
	  margin-bottom: 20px;
	  position: relative;
	}

	/* Label styling */
	label {
	  display: block;
	  font-size: 14px;
	  margin-bottom: 5px;
	  color: #333;
	  transition: all 0.3s ease;
	}

	/* Input field styling */
	input[type="email"], input[type="password"] {
	  width: 100%;
	  padding: 10px;
	  font-size: 16px;
	  border: 1px solid #ddd;
	  border-radius: 4px;
	  outline: none;
	  transition: border-color 0.3s ease, box-shadow 0.3s ease;
	}

	input[type="email"]:focus, input[type="password"]:focus {
	  border-color: #007bff;
	  box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
	}

	/* Submit button styling */
	button[type="submit"] {
	  width: 100%;
	  padding: 12px;
	  font-size: 16px;
	  border: none;
	  border-radius: 4px;
	  background-color: #007bff;
	  color: #ffffff;
	  cursor: pointer;
	  transition: background-color 0.3s ease, box-shadow 0.3s ease;
	}

	button[type="submit"]:hover {
	  background-color: #0056b3;
	  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
	}

	/* Error message styling */
	.error-message {
	  color: #d9534f;
	  font-size: 13px;
	  margin-top: 5px;
	  display: block;
	  animation: fadeIn 0.3s ease-in-out;
	}

	/* Success message styling */
	.success-message {
	  color: #5cb85c;
	  font-size: 14px;
	  margin-top: 20px;
	  text-align: center;
	  display: none;
	}

	/* Animation keyframes */
	@keyframes fadeIn {
	  from {
		opacity: 0;
	  }
	  to {
		opacity: 1;
	  }
	}

	/* Additional styles */
	.form-group input[type="email"], .form-group input[type="password"] {
	  margin-bottom: 10px;
	}

	.form-group button[type="submit"] {
	  margin-top: 20px;
	}

	.container p {
	  font-size: 14px;
	  margin-top: 20px;
	  text-align: center;
	}

	.container p a {
	  text-decoration: none;
	  color: #007bff;
	}

	.container p a:hover {
	  color: #0056b3;
	}
	</style>
</head>
<body>

<div class="container"> 
    <h2>Sign-in Form</h2>
    <form method="POST">
        <div class="form-group">
            <input type="email" name="email" required placeholder="Email">
        </div>
        <div class="form-group">
            <input type="password" name="password" required placeholder="Password">
        </div>
        <button type="submit">Login</button>
        <?php
        if (!empty($error_message)) {
            echo "<div class='error-message'>$error_message</div>";
        }
        ?>
        <p>Don't have an account? <a href="Sign_up.php">Register here</a></p>
    </form>
</div>

</body>
</html>
