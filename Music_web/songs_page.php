<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Sign_in.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle adding/removing from favorites
if (isset($_POST['favorite_song_id'])) {
    $song_id = intval($_POST['favorite_song_id']);
    
    // Check if the song is already in favorites
    $stmt = $conn->prepare('SELECT id FROM favorites WHERE user_id = ? AND song_id = ?');
    $stmt->bind_param('ii', $user_id, $song_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Add to favorites if not already in the list
        $stmt = $conn->prepare('INSERT INTO favorites (user_id, song_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $user_id, $song_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'added']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } else {
        // Remove from favorites if already in the list
        $stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND song_id = ?');
        $stmt->bind_param('ii', $user_id, $song_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'removed']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    exit;
}

// Fetch all songs and check if they are favorited
$songs_query = "
    SELECT songs.id AS song_id, songs.song_name, songs.song_mp3, albums.name AS album_name, artists.name AS artist_name,
    (SELECT COUNT(*) FROM favorites WHERE favorites.song_id = songs.id AND favorites.user_id = ?) AS is_favorite
    FROM songs
    JOIN albums ON songs.album_id = albums.id
    JOIN artists ON albums.artist_id = artists.id
    ORDER BY songs.song_name";

$stmt = $conn->prepare($songs_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$songs_result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Songs</title>
    <link rel="stylesheet" href="songs_styles.css">
</head>
<body>
	<button class="back-btn" onclick="goBack()">&#x2190; Back</button>

    <div class="container">
        <h1>All Songs</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for songs or artists..." onkeyup="searchSongs()">
        </div>

        <div class="song-list" id="songList">
            <?php while ($song = $songs_result->fetch_assoc()) { ?>
                <div class="song-item">
                    <h3><?php echo htmlspecialchars($song['song_name']); ?></h3>
                    <p><?php echo htmlspecialchars($song['artist_name']); ?></p>
                    <audio controls>
                        <source src="<?php echo htmlspecialchars($song['song_mp3']); ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <form method="POST" class="favorite-form">
                        <input type="hidden" name="favorite_song_id" value="<?php echo $song['song_id']; ?>">
                        <button type="submit" class="heart-btn <?php echo $song['is_favorite'] ? 'favorite' : ''; ?>">
                            &#10084;
                        </button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // Search bar functionality
        function searchSongs() {
            var input, filter, songItems, songTitle, i;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            songItems = document.getElementsByClassName("song-item");

            for (i = 0; i < songItems.length; i++) {
                songTitle = songItems[i].getElementsByTagName("h3")[0];
                if (songTitle) {
                    if (songTitle.innerHTML.toUpperCase().indexOf(filter) > -1) {
                        songItems[i].style.display = "";
                    } else {
                        songItems[i].style.display = "none";
                    }
                }
            }
        }

        // Change the color of the heart icon after adding to favorites
        document.querySelectorAll('.favorite-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                }).then(response => response.text())
                .then(data => {
                    // Update the heart icon color after adding to favorites
                    this.querySelector('.heart-btn').classList.add('favorite');
                    alert('Song added to favorites!');
                }).catch(error => console.error('Error:', error));
            });
        });
		
		function goBack() {
          window.history.back();
}

    </script>
</body>
</html>
