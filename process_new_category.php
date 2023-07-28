<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['category_name'])) {
        include 'koneksi.php';

        $category_name = mysqli_real_escape_string($koneksi, $_POST['category_name']);

        $insert_query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
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
