<?php
include 'connection.php';

// SQL query to get the album, artist, and songs details
$sql = "
SELECT 
    ar.name AS artist_name, 
    a.name AS album_name, 
    a.poster AS album_poster, 
    s.id AS song_id,
    s.song_name, 
    s.song_mp3
FROM 
    albums a 
JOIN 
    artists ar ON a.artist_id = ar.id 
JOIN 
    songs s ON s.album_id = a.id 
WHERE 
    a.name = 'The Tortured Poets Department';
";

$result = $conn->query($sql);
$albumDetails = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $albumDetails[] = $row;
    }
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
    <title>The Tortured Poets Department</title>
    <link rel="stylesheet" href="albumstyles.css">
</head>
<body>
    <div class="album-container">
        <?php if (!empty($albumDetails)): ?>
            <div class="album-header">
                <img src="<?php echo $albumDetails[0]['album_poster']; ?>" alt="Album Poster" class="album-poster">
                <div class="album-info">
                    <h2><?php echo $albumDetails[0]['album_name']; ?></h2>
                    <p><?php echo $albumDetails[0]['artist_name']; ?></p>
                </div>
            </div>
<div class="song-list">
    <?php foreach ($albumDetails as $song): ?>
        <div class="song-item">
            <div class="song-info">
                <!-- Favorite Icon -->
                <span class="favorite-icon" data-song-id="<?php echo $song['song_id']; ?>">
                    <i class="fa fa-heart-o"></i> <!-- Empty heart -->
                </span>
                
                <h3>
                    <a href="song_details.php?song_id=<?php echo $song['song_id']; ?>">
                        <?php echo $song['song_name']; ?>
                    </a>
                </h3>
                <audio controls>
                    <source src="<?php echo $song['song_mp3']; ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
                <a href="song_details.php?song_id=<?php echo $song['song_id']; ?>" class="btn-music-video">Music Video</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
        <?php else: ?>
            <p>No album details found.</p>
        <?php endif; ?>
    </div>
</body>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.heart-icon').click(function() {
                var songId = $(this).data('song-id');
                var icon = $(this);
                
                $.ajax({
                    url: 'favorite_page.php',
                    method: 'POST',
                    data: { song_id: songId },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            icon.toggleClass('favorited');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    }
                });
            });
        });
    </script>
</script>
</html>
