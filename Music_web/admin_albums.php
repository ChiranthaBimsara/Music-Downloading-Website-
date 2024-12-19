<?php
include 'connection.php';

// Query to retrieve albums
$albums_result = mysqli_query($conn, "SELECT * FROM albums");
?>

<link rel="stylesheet" href="admin_tables.css">
<h2>Albums</h2>
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
            <button>Delete</button>
        </td>
    </tr>
    <?php } ?>
</table>

