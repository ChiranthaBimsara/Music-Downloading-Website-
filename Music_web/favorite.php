<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Sign_in.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the favorite songs for the logged-in user
$favorites_query = "
    SELECT songs.id AS song_id, songs.song_name, songs.song_mp3, albums.name AS album_name, artists.name AS artist_name
    FROM favorites
    JOIN songs ON favorites.song_id = songs.id
    JOIN albums ON songs.album_id = albums.id
    JOIN artists ON albums.artist_id = artists.id
    WHERE favorites.user_id = ?
    ORDER BY songs.song_name";

$stmt = $conn->prepare($favorites_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$favorites_result = $stmt->get_result();

if (isset($_POST['remove_favorite_song_id'])) {
    $song_id = intval($_POST['remove_favorite_song_id']);
    
    // Remove the song from favorites
    $stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND song_id = ?');
    $stmt->bind_param('ii', $user_id, $song_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $message = "Song removed from favorites.";
    } else {
        $message = "Error removing song from favorites.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Songs</title>
    <link rel="stylesheet" href="songs_styles.css">
</head>
<body>
	<button class="back-btn" onclick="goBack()">&#x2190; Back</button>
    <div class="container">
        <h1>Your Favorite Songs</h1>

        <?php if ($favorites_result->num_rows > 0): ?>
            <div class="song-list">
                <?php while ($song = $favorites_result->fetch_assoc()) { ?>
                    <div class="song-item">
                        <h3><?php echo htmlspecialchars($song['song_name']); ?></h3>
                        <p><?php echo htmlspecialchars($song['artist_name']); ?></p>
                        <audio controls>
                            <source src="<?php echo htmlspecialchars($song['song_mp3']); ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        <!-- Remove from favorites button -->
                        <form method="POST" class="favorite-form">
                            <input type="hidden" name="remove_favorite_song_id" value="<?php echo $song['song_id']; ?>">
                            <button type="submit" class="heart-btn favorite">&#10084;</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
		
        <?php else: ?>
            <p>No favorite songs found.</p>
        <?php endif; ?>
    </div>
</body>
	<script>
	   
		function goBack() {
          window.history.back();
}
	</script>
</html>
