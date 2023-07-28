<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message_id"]) && isset($_POST["edited_message_content"]) && isset($_POST["edited_message_category"])) {
        $message_id = $_POST["message_id"];
        $edited_message_content = $_POST["edited_message_content"];
        $edited_message_category = $_POST["edited_message_category"];

        // Prepare the UPDATE query with placeholders
        $update_message_query = "UPDATE messages SET content = ?, category_id = ? WHERE message_id = ?";

        // Prepare the statement
        $stmt = mysqli_prepare($koneksi, $update_message_query);

        // Bind parameters to the statement
        mysqli_stmt_bind_param($stmt, "sii", $edited_message_content, $edited_message_category, $message_id);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Check if the update was successful
        if ($result) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
