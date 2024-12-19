<?php
require 'connection.php'; // Database connection

// Initialize variables
$username_err = $email_err = $password_err = $confirm_password_err = "";

// Check if the request is a POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validate username
    if (empty($username)) {
        $username_err = "Please enter a username.";
    } elseif (strlen($username) < 3) {
        $username_err = "Username must be at least 3 characters long.";
    }

    // Validate email
    if (empty($email)) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email.";
    }

    // Validate password
    if (empty($password)) {
        $password_err = "Please enter a password.";
    } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[@$!%*?&]/", $password)) {
        $password_err = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    }

    // Validate confirm password
    if (empty($confirm_password)) {
        $confirm_password_err = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $confirm_password_err = "Passwords do not match.";
    }

    // Proceed if there are no errors
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        // Check if the email already exists
        $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $checkEmailQuery);

        if (mysqli_num_rows($result) > 0) {
            $email_err = "This email is already registered. Please use a different email.";
        } else {
            // Insert the new user
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $username, $email, $hashed_password);

            if ($stmt->execute()) {
                echo "Registration successful! <a href='sign_in.php'>Login here</a>";
            } else {
                echo "Something went wrong!";
            }
        }
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
        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        }

        /* Submit button styling */
        input[type="submit"] {
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

        input[type="submit"]:hover {
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
    </style>
</head>
<body>

<div class="container">  
   <form id="registration-form" method="POST" action="Sign_up.php">  
    <h2>Registration Form</h2>  
    <label for="username">Username:</label>  
    <input type="text" id="username" name="username">  
    <span class="error-message" id="username-error"></span>  
    <br><br>  
    <label for="email">Email:</label>  
    <input type="email" id="email" name="email">  
    <span class="error-message" id="email-error"></span>  
    <br><br>  
    <label for="password">Password:</label>  
    <input type="password" id="password" name="password">  
    <span class="error-message" id="password-error"></span>  
    <br><br>  
    <label for="confirm-password">Confirm Password:</label>  
    <input type="password" id="confirm-password" name="confirm-password">  
    <span class="error-message" id="confirm-password-error"></span>  
    <br><br>  
    <input type="submit" value="Register">  
    <div class="success-message" id="success-message"></div>  
   </form>  
  </div>  

</body>
</html>
