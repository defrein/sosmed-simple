<?php
include 'koneksi.php';

if (isset($_POST['reply_id'])) {
    // Tangkap data reply_id dari form
    $reply_id = $_POST['reply_id'];
    $query = "DELETE FROM replies WHERE reply_id = '$reply_id'";

    $result = mysqli_query($koneksi, $query);
    if ($result) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
