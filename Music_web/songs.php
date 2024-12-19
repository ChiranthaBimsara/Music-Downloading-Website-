<?php
include 'connection.php';

// Handle form submissions for adding, editing, and deleting songs
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_song"])) {
        $song_name = mysqli_real_escape_string($conn, $_POST["song_name"]);
        $lyrics = mysqli_real_escape_string($conn, $_POST["lyrics"]);
        $song_mp3 = $_FILES["song_mp3"]["name"];
        $music_video = $_FILES["music_video"]["name"];
        $album_id = (int)$_POST["album_id"];
        
        // Define the directories to save the files
        $target_dir_mp3 = "uploads/songs/mp3/";
        $target_file_mp3 = $target_dir_mp3 . basename($song_mp3);
        $target_dir_video = "uploads/songs/videos/";
        $target_file_video = $target_dir_video . basename($music_video);
        
        // Move the uploaded files to the target directories
        if (move_uploaded_file($_FILES["song_mp3"]["tmp_name"], $target_file_mp3) &&
            move_uploaded_file($_FILES["music_video"]["tmp_name"], $target_file_video)) {
            
            // Insert the song into the database
            $query = "INSERT INTO songs (song_name, lyrics, song_mp3, music_video, album_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $song_name, $lyrics, $target_file_mp3, $target_file_video, $album_id);
            mysqli_stmt_execute($stmt);
        } else {
            echo "There was an error uploading the files.";
        }
    } elseif (isset($_POST["edit_song"])) {
        $song_id = (int)$_POST["song_id"];
        $song_name = mysqli_real_escape_string($conn, $_POST["song_name"]);
        $lyrics = mysqli_real_escape_string($conn, $_POST["lyrics"]);
        $album_id = (int)$_POST["album_id"];

        if (!empty($_FILES["song_mp3"]["name"]) || !empty($_FILES["music_video"]["name"])) {
            $song_mp3 = $_FILES["song_mp3"]["name"];
            $music_video = $_FILES["music_video"]["name"];
            $target_dir_mp3 = "uploads/songs/mp3/";
            $target_file_mp3 = $target_dir_mp3 . basename($song_mp3);
            $target_dir_video = "uploads/songs/videos/";
            $target_file_video = $target_dir_video . basename($music_video);
            
            if (!empty($_FILES["song_mp3"]["name"])) {
                move_uploaded_file($_FILES["song_mp3"]["tmp_name"], $target_file_mp3);
            }
            if (!empty($_FILES["music_video"]["name"])) {
                move_uploaded_file($_FILES["music_video"]["tmp_name"], $target_file_video);
            }

            $query = "UPDATE songs SET song_name = ?, lyrics = ?, song_mp3 = ?, music_video = ?, album_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssiii", $song_name, $lyrics, $target_file_mp3, $target_file_video, $album_id, $song_id);
        } else {
            $query = "UPDATE songs SET song_name = ?, lyrics = ?, album_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssii", $song_name, $lyrics, $album_id, $song_id);
        }
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["delete_song"])) {
        $song_id = (int)$_POST["song_id"];
        $query = "DELETE FROM songs WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $song_id);
        mysqli_stmt_execute($stmt);
    }
}

// Fetch songs
$songs_result = mysqli_query($conn, "SELECT * FROM songs");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Songs</title>
    <link rel="stylesheet" href="adminDashboard_style.css">
</head>
<body>
    <div class="container">
        <h1>Songs</h1>

        <h2>Songs List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Song Name</th>
                <th>Lyrics</th>
                <th>MP3</th>
                <th>Video</th>
                <th>Album ID</th>
                <th>Actions</th>
            </tr>
            <?php while ($song = mysqli_fetch_assoc($songs_result)) { ?>
            <tr>
                <td><?php echo $song["id"]; ?></td>
                <td><?php echo $song["song_name"]; ?></td>
                <td>
                    <?php
                    $lyrics_words = explode(" ", $song["lyrics"]);
                    $snippet = implode(" ", array_slice($lyrics_words, 0, 10));
                    echo $snippet . (count($lyrics_words) > 10 ? '...' : '');
                    ?>
                </td>
                <td><a href="<?php echo $song["song_mp3"]; ?>">MP3</a></td>
                <td><a href="<?php echo $song["music_video"]; ?>">Video</a></td>
                <td><?php echo $song["album_id"]; ?></td>
                <td>
                    <button onclick="editSong(<?php echo $song['id']; ?>, '<?php echo $song['song_name']; ?>', '<?php echo $song['lyrics']; ?>', '<?php echo $song['song_mp3']; ?>', '<?php echo $song['music_video']; ?>', <?php echo $song['album_id']; ?>)">Edit</button>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;">
                        <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                        <input type="submit" name="delete_song" value="Delete">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h2>Add New Song</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="song_name">Song Name:</label>
            <input type="text" id="song_name" name="song_name" required><br><br>
            <label for="lyrics">Lyrics:</label>
            <textarea id="lyrics" name="lyrics" required></textarea><br><br>
            <label for="song_mp3">MP3:</label>
            <input type="file" id="song_mp3" name="song_mp3" required><br><br>
            <label for="music_video">Video:</label>
            <input type="file" id="music_video" name="music_video" required><br><br>
            <label for="album_id">Album ID:</label>
            <input type="text" id="album_id" name="album_id" required><br><br>
            <input type="submit" name="add_song" value="Add Song">
        </form>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Song</h2>
            <form id="editSongForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" id="edit_song_id" name="song_id">
                <label for="edit_song_name">Song Name:</label>
                <input type="text" id="edit_song_name" name="song_name" required><br><br>
                <label for="edit_lyrics">Lyrics:</label>
                <textarea id="edit_lyrics" name="lyrics" required></textarea><br><br>
                <label for="edit_song_mp3">MP3:</label>
                <input type="file" id="edit_song_mp3" name="song_mp3"><br><br>
                <label for="edit_music_video">Video:</label>
                <input type="file" id="edit_music_video" name="music_video"><br><br>
                <label for="edit_album_id">Album ID:</label>
                <input type="text" id="edit_album_id" name="album_id" required><br><br>
                <input type="submit" name="edit_song" value="Save Changes">
            </form>
        </div>
    </div>

    <script>
        function editSong(id, song_name, lyrics, song_mp3, music_video, album_id) {
            document.getElementById('edit_song_id').value = id;
            document.getElementById('edit_song_name').value = song_name;
            document.getElementById('edit_lyrics').value = lyrics;
            document.getElementById('edit_album_id').value = album_id;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }
    </script>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
