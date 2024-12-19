<?php
include 'connection.php';

// Handle form submissions for adding, editing, and deleting albums
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_album"])) {
        $album_name = mysqli_real_escape_string($conn, $_POST["album_name"]);
        $album_poster = $_FILES["album_poster"]["name"];
        $artist_id = (int)$_POST["artist_id"];
        $target_dir = "uploads/albums/";
        $target_file = $target_dir . basename($album_poster);
        move_uploaded_file($_FILES["album_poster"]["tmp_name"], $target_file);

        $query = "INSERT INTO albums (name, poster, artist_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $album_name, $target_file, $artist_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["edit_album"])) {
        $album_id = (int)$_POST["album_id"];
        $album_name = mysqli_real_escape_string($conn, $_POST["album_name"]);
        if (!empty($_FILES["album_poster"]["name"])) {
            $album_poster = $_FILES["album_poster"]["name"];
            $target_dir = "uploads/albums/";
            $target_file = $target_dir . basename($album_poster);
            move_uploaded_file($_FILES["album_poster"]["tmp_name"], $target_file);

            $query = "UPDATE albums SET name = ?, poster = ?, artist_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssii", $album_name, $target_file, $artist_id, $album_id);
        } else {
            $query = "UPDATE albums SET name = ?, artist_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sii", $album_name, $artist_id, $album_id);
        }
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["delete_album"])) {
        $album_id = (int)$_POST["album_id"];
        $query = "DELETE FROM albums WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $album_id);
        mysqli_stmt_execute($stmt);
    }
}

// Fetch albums
$albums_result = mysqli_query($conn, "SELECT * FROM albums");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Albums</title>
    <link rel="stylesheet" href="adminDashboard_style.css">
</head>
<body>
    <div class="container">
        <h1>Albums</h1>

        <h2>Albums List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Poster</th>
                <th>Artist ID</th>
                <th>Actions</th>
            </tr>
            <?php while ($album = mysqli_fetch_assoc($albums_result)) { ?>
            <tr>
                <td><?php echo $album["id"]; ?></td>
                <td><?php echo $album["name"]; ?></td>
                <td><img src="<?php echo $album["poster"]; ?>" width="50" height="50"></td>
                <td><?php echo $album["artist_id"]; ?></td>
                <td>
                    <button onclick="editAlbum(<?php echo $album['id']; ?>, '<?php echo $album['name']; ?>', '<?php echo $album['poster']; ?>', <?php echo $album['artist_id']; ?>)">Edit</button>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;">
                        <input type="hidden" name="album_id" value="<?php echo $album['id']; ?>">
                        <input type="submit" name="delete_album" value="Delete">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h2>Add New Album</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="album_name">Name:</label>
            <input type="text" id="album_name" name="album_name" required><br><br>
            <label for="album_poster">Poster:</label>
            <input type="file" id="album_poster" name="album_poster" required><br><br>
            <label for="artist_id">Artist ID:</label>
            <input type="text" id="artist_id" name="artist_id" required><br><br>
            <input type="submit" name="add_album" value="Add Album">
        </form>
    </div>

    <script>
        function editAlbum(id, name, poster, artist_id) {
            // Add logic to open a modal or navigate to an edit page
            // Pre-fill the form with album's data
        }
    </script>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
