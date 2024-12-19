<?php
require 'vendor\autoload.php';
require 'connection.php';
session_start();

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://yourdomain.com/google-login.php');
$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit;
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Get user info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $google_id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    // Check if the user exists in the database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = ? OR email = ?');
    $stmt->execute([$google_id, $email]);
    $user = $stmt->fetch();

    if (!$user) {
        // New user, insert into the database
        $stmt = $pdo->prepare('INSERT INTO users (username, email, google_id) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $google_id]);
        $user_id = $pdo->lastInsertId();
    } else {
        $user_id = $user['id'];
    }

    // Log the user in
    $_SESSION['user_id'] = $user_id;
    header('Location: dashboard.php');
    exit;
}
