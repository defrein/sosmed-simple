<!-- process_delete_message.php -->

<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message_id"])) {
        $message_id = $_POST["message_id"];

        // hapus replies dulu
        $delete_replies_query = "DELETE FROM replies WHERE message_id = '$message_id'";
        $result_replies = mysqli_query($koneksi, $delete_replies_query);

        // hapus message yang dipilih
        $delete_message_query = "DELETE FROM messages WHERE message_id = '$message_id'";
        $result_message = mysqli_query($koneksi, $delete_message_query);

        if ($result_replies && $result_message) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>