<?php
include 'connection.php';


// Query to retrieve songs
$songs_result = mysqli_query($conn, "SELECT * FROM songs");
?>

<link rel="stylesheet" href="admin_tables.css">
<h2>Songs</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Lyrics</th>
        <th>Album ID</th>
        <th>Actions</th>
    </tr>
    <?php while ($song = mysqli_fetch_assoc($songs_result)) { 
        // Get the first five words of the lyrics
        $lyrics_preview = implode(' ', array_slice(explode(' ', $song["lyrics"]), 0, 5)) . '...';
    ?>
    <tr>
        <td><?php echo $song["id"]; ?></td>
        <td><?php echo $song["song_name"]; ?></td>
        <td><?php echo $lyrics_preview; ?></td>
        <td><?php echo $song["album_id"]; ?></td>
        <td>
            <button onclick="editSong(<?php echo $song['id']; ?>, '<?php echo $song['song_name']; ?>', '<?php echo $song['lyrics']; ?>', <?php echo $song['album_id']; ?>)">Edit</button>
            <button>Delete</button>
            <button onclick="updateSongMP3(<?php echo $song['id']; ?>)">Update MP3</button>
            <button onclick="updateSongVideo(<?php echo $song['id']; ?>)">Update Video</button>
        </td>
    </tr>
    <?php } ?>
</table>


