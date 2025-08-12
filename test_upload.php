<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['video'])) {
        echo "Upload successful. Temp path: " . $_FILES['video']['tmp_name'];
    } else {
        echo "No file uploaded.";
    }
} else {
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="video" />
    <input type="submit" value="Upload" />
</form>
<?php } ?>
