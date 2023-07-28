<?php
include 'koneksi.php';

if (isset($_POST['reply_id']) && isset($_POST['edited_reply_content'])) {
    $reply_id = $_POST['reply_id'];
    $edited_reply_content = $_POST['edited_reply_content'];

    $query = "UPDATE replies SET reply_content = '$edited_reply_content' WHERE reply_id = '$reply_id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
