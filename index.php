<?php
include 'koneksi.php';

$all_contents = mysqli_query($koneksi, "SELECT 
m.message_id,
m.content AS message_content,
m.created_at AS message_created_at,
c.category_id,
c.category_name AS category_name,
r.reply_id,
r.reply_content AS reply_content,
r.created_at AS reply_created_at
FROM
messages m
LEFT JOIN
categories c ON m.category_id = c.category_id
LEFT JOIN
replies r ON m.message_id = r.message_id");

// Check for query execution errors
if (!$all_contents) {
    die("Error: " . mysqli_error($koneksi));
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sosmed Simple </title>
    <!-- Bootstrap Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- FontAwesome Icons Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Sosmed Simple By Wahni Adnani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Form tambah Message -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Message</h5>
                        <form action="process_new_message.php" method="POST">
                            <div class="mb-3">
                                <label for="message_content" class="form-label">Message Content</label>
                                <input type="text" class="form-control" id="message_content" name="message_content"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Select Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" disabled selected>Select a category</option>
                                    <?php
                                    $categories_query = mysqli_query($koneksi, "SELECT * FROM categories");
                                    while ($category = mysqli_fetch_assoc($categories_query)) {
                                        echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Message</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Form tambah Category -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Category</h5>
                        <form action="process_new_category.php" method="POST">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <?php
                $current_message_id = 0;
                while ($row = mysqli_fetch_assoc($all_contents)) {
                    // Check if it's a new message group
                    if ($current_message_id != $row['message_id']) {
                        // Close previous message group if it exists
                        if ($current_message_id != 0) {
                            echo '</div></div></div>';
                        }
                ?>
                <!-- Start a new message group (accordion-item) -->
                <div class="accordion-item">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= $row['category_name']; ?></h5>
                            <!-- Message content -->
                            <p class="card-text"><?= $row['message_content']; ?></p>
                            <button
                                class="btn btn-sm btn-<?php echo empty($row['reply_content']) ? 'light' : 'secondary'; ?> show-replies"
                                data-bs-toggle="collapse" data-bs-target="#collapse-<?= $row['message_id']; ?>">
                                Show Replies
                            </button>

                            <form action="process_delete_message.php" method="POST" style="display: inline;">
                                <input type="hidden" name="message_id" value="<?= $row['message_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-warning edit-message-btn" data-bs-toggle="modal"
                                data-bs-target="#editMessageModal" data-message-id="<?= $row['message_id']; ?>"
                                data-message-content="<?= $row['message_content']; ?>"
                                data-message-category-id="<?= $row['category_id']; ?>"
                                data-message-category-name="<?= $row['category_name']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Add "Tambah Reply" button to open modal -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addReplyModal" data-message-id="<?= $row['message_id']; ?>">
                                Add Reply
                            </button>
                        </div>
                        <div id="collapse-<?= $row['message_id']; ?>" class="accordion-collapse collapse">
                            <div class="replies-container">
                                <?php
                                }

                                // Check if there is a reply associated with the message
                                if (!empty($row['reply_content'])) {
                                    ?>
                                <!-- Display reply content and buttons -->
                                <div class="card">
                                    <div class="card-body bg-light">
                                        <p><?= $row['reply_content']; ?></p>
                                        <button class="btn btn-sm btn-success d-inline-block" data-bs-toggle="modal"
                                            data-bs-target="#editReplyModal" data-reply-id="<?= $row['reply_id']; ?>"
                                            data-reply-content="<?= $row['reply_content']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="delete_reply.php" method="POST"
                                            class="delete-reply-form d-inline-block">
                                            <input type="hidden" name="reply_id" value="<?= $row['reply_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                                }

                                // Set the current message ID to check for new message groups
                                $current_message_id = $row['message_id'];
                            }

                            // Close the last reply container and accordion-item
                            if ($current_message_id != 0) {
                                echo '</div></div></div>';
                            }
                                ?>
                            </div>
                        </div>
                    </div>



                    <!-- Edit Message Modal -->
                    <div class="modal fade" id="editMessageModal" tabindex="-1" aria-labelledby="editMessageModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editMessageModalLabel">Edit Message</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="process_edit_message.php" method="POST">
                                        <input type="hidden" id="editMessageId" name="message_id">
                                        <div class="mb-3">
                                            <label for="edited_message_content" class="form-label">Message
                                                Content</label>
                                            <input type="text" class="form-control" id="edited_message_content"
                                                name="edited_message_content" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edited_message_category" class="form-label">Select
                                                Category</label>
                                            <select class="form-select" id="edited_message_category"
                                                name="edited_message_category" required>
                                                <?php
                                                        // ambil semua category di database
                                                        $categories = mysqli_query($koneksi, "SELECT * FROM categories");
                                                        while ($category = mysqli_fetch_assoc($categories)) {
                                                            // cek apakah id_category nya sama dengan id_category dari message yang sedang diedit?
                                                            $selected = ($category['category_id'] == $row['category_id']) ? 'selected' : '';
                                                            echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' . $category['category_name'] . '</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Adding Reply -->
                    <div class="modal fade" id="addReplyModal" tabindex="-1" aria-labelledby="addReplyModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addReplyModalLabel">Add New Reply</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="process_reply.php" method="POST">
                                        <input type="hidden" name="message_id" id="addReplyMessageId">
                                        <div class="mb-3">
                                            <label for="reply_content" class="form-label">Reply Content</label>
                                            <input type="text" class="form-control" name="reply_content"
                                                id="reply_content" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Edit Reply -->
                    <div class="modal fade" id="editReplyModal" tabindex="-1" aria-labelledby="editReplyModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editReplyModalLabel">Edit Reply</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="process_edit_reply.php" method="POST" class="edit-reply-form">
                                        <input type="hidden" name="reply_id" id="editReplyId">
                                        <div class="mb-3">
                                            <label for="edited_reply_content" class="form-label">Reply Content</label>
                                            <input type="text" class="form-control" name="edited_reply_content"
                                                id="edited_reply_content">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    const editMessageModal = document.getElementById('editMessageModal');
                    editMessageModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const messageId = button.getAttribute('data-message-id');
                        const messageContent = button.getAttribute('data-message-content');
                        const categoryId = button.getAttribute('data-message-category-id');
                        const categoryName = button.getAttribute('data-message-category-name');

                        const editMessageIdInput = editMessageModal.querySelector('#editMessageId');
                        const editedMessageContentInput = editMessageModal.querySelector(
                            '#edited_message_content');
                        const editedMessageCategorySelect = editMessageModal.querySelector(
                            '#edited_message_category');

                        editMessageIdInput.value = messageId;
                        editedMessageContentInput.value = messageContent;

                        // Set the selected option in the category select dropdown
                        for (let i = 0; i < editedMessageCategorySelect.options.length; i++) {
                            if (editedMessageCategorySelect.options[i].value === categoryId) {
                                editedMessageCategorySelect.options[i].selected = true;
                                break;
                            }
                        }
                    });
                    </script>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const addReplyModal = document.getElementById('addReplyModal');
                        addReplyModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget;
                            const messageId = button.getAttribute('data-message-id');

                            const addReplyMessageIdInput = addReplyModal.querySelector(
                                '#addReplyMessageId');
                            const replyContentInput = addReplyModal.querySelector('#reply_content');

                            addReplyMessageIdInput.value = messageId;
                            replyContentInput.value = ''; // Reset input value when opening the modal
                        });
                    });
                    </script>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const editReplyModal = document.getElementById('editReplyModal');
                        editReplyModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget;
                            const replyId = button.getAttribute('data-reply-id');
                            const replyContent = button.getAttribute('data-reply-content');

                            const editReplyIdInput = document.getElementById('editReplyId');
                            const editedReplyContentInput = document.getElementById(
                                'edited_reply_content');

                            editReplyIdInput.value = replyId;
                            editedReplyContentInput.value = replyContent;
                        });
                    });
                    </script>




                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
                        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
                        crossorigin="anonymous">
                    </script>

</body>

</html>