<?php
include 'connection.php';

// Get song ID from query parameter
$song_id = isset($_GET['song_id']) ? intval($_GET['song_id']) : 0;

// SQL query to get the song details
$sql = "
SELECT 
    ar.name AS artist_name, 
    a.name AS album_name, 
    a.poster AS album_poster, 
    s.song_name, 
    s.song_mp3, 
    s.lyrics, 
    s.music_video 
FROM 
    songs s
JOIN 
    albums a ON s.album_id = a.id
JOIN 
    artists ar ON a.artist_id = ar.id
WHERE 
    s.id = $song_id;
";

$result = $conn->query($sql);
$songDetails = null;
if ($result->num_rows > 0) {
    // Fetch song details
    $songDetails = $result->fetch_assoc();
} else {
    echo "0 results";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $songDetails['song_name']; ?></title>
    <link rel="stylesheet" href="videostyles.css">
</head>
<body>
    <div class="song-details-container">
        <?php if (!empty($songDetails)): ?>
            <div class="song-header">
                <img src="<?php echo $songDetails['album_poster']; ?>" alt="Album Poster" class="album-poster">
                <div class="song-info">
                    <h2><?php echo $songDetails['song_name']; ?></h2>
                    <p><?php echo $songDetails['artist_name']; ?> - <?php echo $songDetails['album_name']; ?></p>
                </div>
            </div>
            <div class="song-content">
                <div class="song-lyrics">
                    <h3>Lyrics</h3>
                    <pre><?php echo $songDetails['lyrics']; ?></pre>
                </div>
                <div class="song-video">
                    <h3>Music Video</h3>
                    <video controls>
                        <source src="<?php echo $songDetails['music_video']; ?>" type="video/mp4">
                        Your browser does not support the video element.
                    </video>
                </div>
            </div>
        <?php else: ?>
            <p>Song details not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
