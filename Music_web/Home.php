<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Sign_in.php');
    exit;
}

require 'connection.php'; // Database connection

// Fetch recent songs with their album posters
$recent_songs_query = "SELECT songs.*, albums.name AS album_name, albums.poster AS album_poster, artists.name AS artist_name 
                       FROM songs 
                       JOIN albums ON songs.album_id = albums.id
                       JOIN artists ON albums.artist_id = artists.id
                       ORDER BY songs.id DESC LIMIT 4";
$recent_songs_result = mysqli_query($conn, $recent_songs_query);

// Fetch recent albums
$recent_albums_query = "SELECT albums.*, artists.name AS artist_name, artists.photo AS artist_photo 
                        FROM albums 
                        JOIN artists ON albums.artist_id = artists.id
                        ORDER BY albums.id DESC LIMIT 5";
$recent_albums_result = mysqli_query($conn, $recent_albums_query);

// Fetch artist list
$artists_query = "SELECT * FROM artists ORDER BY name";
$artists_result = mysqli_query($conn, $artists_query);

// Get user data
$stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Website</title>
    <link rel="stylesheet" href="homestyles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="uploads\Images\songflex.png" alt="Logo">
        </div>
        <nav>
            <a href="Home.php">Home</a>
			<a href="songs_page.php">Songs</a>
			<a href="favorite.php">Favorite</a>
            <a href="Profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Experience music like never before.</h1>
        <button>Explore Now</button>
    </section>

    <main>
        <!-- Recent Albums Section -->
        <section class="recent-albums">
    <h2>Recent Albums</h2>
    <div class="albums-list">
        <?php while ($album = mysqli_fetch_assoc($recent_albums_result)) { ?>
        <div class="album-item">
            <img src="<?php echo htmlspecialchars($album['poster'], ENT_QUOTES, 'UTF-8'); ?>" alt="Album Poster">
            <p>
                <a href="album_details.php?id=<?php echo $album['id']; ?>">
                    <?php echo htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </p>
            <p><?php echo htmlspecialchars($album['artist_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <?php } ?>
    </div>
</section>


        <!-- Recent Songs Section -->
        <section class="recent-songs">
            <h2>Recent Songs</h2>
            <div class="songs-list">
                <?php while ($song = mysqli_fetch_assoc($recent_songs_result)) { ?>
                <div class="song-item">
                    <img src="<?php echo htmlspecialchars($song['album_poster'], ENT_QUOTES, 'UTF-8'); ?>" alt="Album Poster">
                    <div class="song-details">
                        <p><?php echo htmlspecialchars($song['song_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php echo htmlspecialchars($song['artist_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <button onclick="playSong('<?php echo htmlspecialchars($song['song_mp3'], ENT_QUOTES, 'UTF-8'); ?>')">Play</button>
                    </div>
                </div>
                <?php } ?>
            </div>
        </section>

        <!-- Artists Section -->
        <section class="artists">
            <h2>Artists</h2>
            <div class="artists-list">
                <?php while ($artist = mysqli_fetch_assoc($artists_result)) { ?>
                <div class="artist-item">
                    <img src="<?php echo htmlspecialchars($artist['photo'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <p><?php echo htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <?php } ?>
            </div>
        </section>

        <!-- Happy Customers Section -->
        <section class="customers">
            <h2>Our Happy Customers</h2>
            <div class="testimonials-grid">
                <div class="testimonial-item">
                    <p>⭐⭐⭐⭐⭐</p>
                    <p>"The best music platform I've ever used!"</p>
                </div>
                <div class="testimonial-item">
                    <p>⭐⭐⭐⭐⭐</p>
                    <p>"I found all my favorite songs here."</p>
                </div>
                <div class="testimonial-item">
                    <p>⭐⭐⭐⭐⭐</p>
                    <p>"Amazing quality and user experience."</p>
                </div>
            </div>
        </section>
    </main>

    <div id="music-player">
        <div id="current-song-info">
            <img id="current-song-artist-photo" src="" alt="Artist Photo">
            <div>
                <p id="current-song-name"></p>
                <p id="current-song-artist"></p>
            </div>
        </div>
        <audio id="audio-player" controls></audio>
        <div id="current-song-lyrics"></div>
    </div>

    <footer>
        <div class="footer-content">
            <p>© 2024 Songflex Website. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function playSong(mp3) {
            var audioPlayer = document.getElementById('audio-player');
            audioPlayer.src = mp3;
            audioPlayer.play().catch(function(error) {
                console.error("Error playing the song: ", error);
                alert("There was an error playing the song. Please check the console for more details.");
            });
        }
    </script>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
