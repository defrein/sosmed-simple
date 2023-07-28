<?php

include 'koneksi.php';


if (isset($_POST['message_id']) && isset($_POST['reply_content'])) {

    $message_id = $_POST['message_id'];
    $reply_content = $_POST['reply_content'];

    $query = "INSERT INTO replies (message_id, reply_content) VALUES ('$message_id', '$reply_content')";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
