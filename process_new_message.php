<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['message_content']) && isset($_POST['category_id'])) {

        include 'koneksi.php';
        $message_content = mysqli_real_escape_string($koneksi, $_POST['message_content']);
        $category_id = mysqli_real_escape_string($koneksi, $_POST['category_id']);

        $insert_query = "INSERT INTO messages (content, category_id, created_at) VALUES ('$message_content', '$category_id', NOW())";
        $result = mysqli_query($koneksi, $insert_query);

        if ($result) {
            header('Location: index.php?success=1');
            exit();
        } else {
            header('Location: index.php?error=1');
            exit();
        }
    } else {
        header('Location: index.php?error=1');
        exit();
    }
}
