<?php
include 'connection.php';

// Query to retrieve artists
$artists_result = mysqli_query($conn, "SELECT * FROM artists");

// Query to retrieve albums
$albums_result = mysqli_query($conn, "SELECT * FROM albums");

// Query to retrieve songs
$songs_result = mysqli_query($conn, "SELECT * FROM songs");

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action_artist"])) {
        // Add new artist
        $artist_name = mysqli_real_escape_string($conn, $_POST["artist_name"]);
        $artist_photo = $_FILES["artist_photo"]["name"];
        $target_dir = "uploads/artists/";
        $target_file = $target_dir . basename($artist_photo);
        move_uploaded_file($_FILES["artist_photo"]["tmp_name"], $target_file);

        $query = "INSERT INTO artists (name, photo) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $artist_name, $target_file);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["action_album"])) {
        // Add new album
        $album_name = mysqli_real_escape_string($conn, $_POST["album_name"]);
        $album_poster = $_FILES["album_poster"]["name"];
        $artist_id = (int) $_POST["artist_id"];
        $target_dir = "uploads/albums/";
        $target_file = $target_dir . basename($album_poster);
        move_uploaded_file($_FILES["album_poster"]["tmp_name"], $target_file);

        $query = "INSERT INTO albums (name, poster, artist_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $album_name, $target_file, $artist_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["action_song"])) {
        // Add new song
        $song_name = mysqli_real_escape_string($conn, $_POST["song_name"]);
        $lyrics = mysqli_real_escape_string($conn, $_POST["lyrics"]);
        $album_id = (int) $_POST["album_id"];

        $query = "INSERT INTO songs (song_name, lyrics, album_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $song_name, $lyrics, $album_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["action_song_mp3"])) {
        // Update song MP3
        $id_song_mp3 = (int) $_POST["song_id_mp3"];
        $song_mp3_new = $_FILES["song_mp3_new"]["name"];
        $target_dir = "uploads/songs/mp3/";
        $target_file = $target_dir . basename($song_mp3_new);
        move_uploaded_file($_FILES["song_mp3_new"]["tmp_name"], $target_file);

        $query = "UPDATE songs SET song_mp3 = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $target_file, $id_song_mp3);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["action_song_video"])) {
        // Update song video
        $id_song_video = (int) $_POST["song_id_video"];
        $music_video_new = $_FILES["music_video_new"]["name"];
        $target_dir = "uploads/songs/videos/";
        $target_file = $target_dir . basename($music_video_new);
        move_uploaded_file($_FILES["music_video_new"]["tmp_name"], $target_file);

        $query = "UPDATE songs SET music_video = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $target_file, $id_song_video);
        mysqli_stmt_execute($stmt);
    }
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Database Admin Dashboard</title>
    <link rel="stylesheet" href="adminDashboard_style.css">
</head>
<body>
    <div class="navbar">
        <a href="admin_artists.php">Artists</a>
        <a href="admin_albums.php">Albums</a>
        <a href="admin_songs.php">Songs</a>
    </div>
    <div class="container">
        <h1>Music Database Admin Dashboard</h1>

        <h2>Add New Artist</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="artist_name">Name:</label>
            <input type="text" id="artist_name" name="artist_name" required><br><br>
            <label for="artist_photo">Photo:</label>
            <input type="file" id="artist_photo" name="artist_photo" required><br><br>
            <input type="submit" name="action_artist" value="Add Artist">
        </form>

        <h2>Add New Album</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="album_name">Name:</label>
            <input type="text" id="album_name" name="album_name" required><br><br>
            <label for="album_poster">Poster:</label>
            <input type="file" id="album_poster" name="album_poster" required><br><br>
            <label for="artist_id">Artist ID:</label>
            <input type="text" id="artist_id" name="artist_id" required><br><br>
            <input type="submit" name="action_album" value="Add Album">
        </form>

        <h2>Add New Song</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="song_name">Name:</label>
            <input type="text" id="song_name" name="song_name" required><br><br>
            <label for="lyrics">Lyrics:</label>
            <textarea id="lyrics" name="lyrics" required></textarea><br><br>
            <label for="album_id">Album ID:</label>
            <input type="text" id="album_id" name="album_id" required><br><br>
            <input type="submit" name="action_song" value="Add Song">
        </form>

        <h2>Update Song MP3</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="song_id_mp3">Song ID:</label>
            <input type="text" id="song_id_mp3" name="song_id_mp3" required><br><br>
            <label for="song_mp3_new">MP3 File:</label>
            <input type="file" id="song_mp3_new" name="song_mp3_new" required><br><br>
            <input type="submit" name="action_song_mp3" value="Update MP3">
        </form>

        <h2>Update Song Video</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="song_id_video">Song ID:</label>
            <input type="text" id="song_id_video" name="song_id_video" required><br><br>
            <label for="music_video_new">Video File:</label>
            <input type="file" id="music_video_new" name="music_video_new" required><br><br>
            <input type="submit" name="action_song_video" value="Update Video">
        </form>
    </div>
</body>
</html>
