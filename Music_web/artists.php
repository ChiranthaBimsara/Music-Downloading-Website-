<?php
include 'connection.php';

// Handle form submissions for adding, editing, and deleting artists
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_artist"])) {
        $artist_name = mysqli_real_escape_string($conn, $_POST["artist_name"]);
        $artist_photo = $_FILES["artist_photo"]["name"];
        $target_dir = "uploads/artists/";
        $target_file = $target_dir . basename($artist_photo);
        move_uploaded_file($_FILES["artist_photo"]["tmp_name"], $target_file);

        $query = "INSERT INTO artists (name, photo) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $artist_name, $target_file);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["edit_artist"])) {
        $artist_id = (int)$_POST["artist_id"];
        $artist_name = mysqli_real_escape_string($conn, $_POST["artist_name"]);
        if (!empty($_FILES["artist_photo"]["name"])) {
            $artist_photo = $_FILES["artist_photo"]["name"];
            $target_dir = "uploads/artists/";
            $target_file = $target_dir . basename($artist_photo);
            move_uploaded_file($_FILES["artist_photo"]["tmp_name"], $target_file);

            $query = "UPDATE artists SET name = ?, photo = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $artist_name, $target_file, $artist_id);
        } else {
            $query = "UPDATE artists SET name = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $artist_name, $artist_id);
        }
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST["delete_artist"])) {
        $artist_id = (int)$_POST["artist_id"];
        $query = "DELETE FROM artists WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $artist_id);
        mysqli_stmt_execute($stmt);
    }
}

// Fetch artists
$artists_result = mysqli_query($conn, "SELECT * FROM artists");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Artists</title>
    <link rel="stylesheet" href="adminDashboard_style.css">
</head>
<body>
    <div class="container">
        <h1>Artists</h1>

        <h2>Artists List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
            <?php while ($artist = mysqli_fetch_assoc($artists_result)) { ?>
            <tr>
                <td><?php echo $artist["id"]; ?></td>
                <td><?php echo $artist["name"]; ?></td>
                <td><img src="<?php echo $artist["photo"]; ?>" width="50" height="50"></td>
                <td>
                    <button onclick="editArtist(<?php echo $artist['id']; ?>, '<?php echo $artist['name']; ?>', '<?php echo $artist['photo']; ?>')">Edit</button>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;">
                        <input type="hidden" name="artist_id" value="<?php echo $artist['id']; ?>">
                        <input type="submit" name="delete_artist" value="Delete">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h2>Add New Artist</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="artist_name">Name:</label>
            <input type="text" id="artist_name" name="artist_name" required><br><br>
            <label for="artist_photo">Photo:</label>
            <input type="file" id="artist_photo" name="artist_photo" required><br><br>
            <input type="submit" name="add_artist" value="Add Artist">
        </form>
    </div>

    <script>
        function editArtist(id, name, photo) {
            // Add logic to open a modal or navigate to an edit page
            // Pre-fill the form with artist's data
        }
    </script>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
