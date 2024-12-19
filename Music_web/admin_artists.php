<?php
include 'connection.php';

// Query to retrieve artists
$artists_result = mysqli_query($conn, "SELECT * FROM artists");
?>
<link rel="stylesheet" href="admin_tables.css">
<h2>Artists</h2>
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
            <button>Delete</button>
        </td>
    </tr>
    <?php } ?>
</table>


