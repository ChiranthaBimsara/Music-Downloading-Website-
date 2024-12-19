<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Sign_in.php');
    exit;
}

require 'connection.php'; // Database connection

// Get album ID from URL parameter
$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($album_id === 0) {
    // Handle error - no valid album ID provided
    die("No valid album ID provided");
}

// Fetch album details
$album_query = "SELECT albums.*, artists.name AS artist_name 
                FROM albums 
                JOIN artists ON albums.artist_id = artists.id
                WHERE albums.id = ?";
$stmt = $conn->prepare($album_query);
$stmt->bind_param('i', $album_id);
$stmt->execute();
$album = $stmt->get_result()->fetch_assoc();

if (!$album) {
    // Handle error - album not found
    die("Album not found");
}

// Fetch album tracks
$tracks_query = "SELECT * FROM songs WHERE album_id = ? ORDER BY song_name";
$stmt = $conn->prepare($tracks_query);
$stmt->bind_param('i', $album_id);
$stmt->execute();
$tracks_result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($album['artist_name'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="album_details_styles.css">
</head>
<body>
    <header>
        <!-- Add your header content here -->
    </header>

    <main>
        <div class="album-info">
    <div class="album-cover">
        <img src="<?php echo htmlspecialchars($album['poster'], ENT_QUOTES, 'UTF-8'); ?>" alt="Album Cover">
    </div>
    <div class="album-details">
        <h1><?php echo htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <h2><?php echo htmlspecialchars($album['artist_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>
</div>


        <div class="track-list">
            <?php while ($track = $tracks_result->fetch_assoc()) { ?>
                <div class="track-item">
                    <h3><?php echo htmlspecialchars($track['song_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <audio controls>
                        <source src="<?php echo htmlspecialchars($track['song_mp3'], ENT_QUOTES, 'UTF-8'); ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <a href="song_details.php?song_id=<?php echo $track['id']; ?>" class="music-video-btn">Music Video</a>
                </div>
            <?php } ?>
        </div>
    </main>

    <footer>
        <!-- Add your footer content here -->
    </footer>

    <script>
        function playMusicVideo(videoUrl) {
            // Implement music video playback functionality here
            alert("Music video URL: " + videoUrl);
            // You might want to open a modal or redirect to a video player page
        }
    </script>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>