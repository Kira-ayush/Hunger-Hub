<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Mark message as read
if (isset($_POST['mark_read'])) {
    $message_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $_SESSION['success'] = "Message marked as read.";
    header("Location: messages.php");
    exit();
}

// Delete message
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Message deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete message.";
    }
    header("Location: messages.php");
    exit();
}

$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Messages - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 data-aos="fade-right"><i class="fas fa-envelope"></i> Customer Messages</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary" data-aos="fade-left">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($message = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <div class="card h-100 shadow-sm <?= $message['is_read'] ? 'border-secondary' : 'border-primary' ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($message['name']) ?>
                                    <?php if (!$message['is_read']): ?>
                                        <span class="badge bg-danger ms-2">New</span>
                                    <?php endif; ?>
                                </h6>
                                <small class="text-muted"><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></small>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($message['email']) ?></p>
                                <p class="card-text"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <?php if (!$message['is_read']): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                        <button type="submit" name="mark_read" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Mark as Read
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Read</span>
                                <?php endif; ?>

                                <a href="?delete=<?= $message['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this message?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" data-aos="fade-up">
                <i class="fas fa-inbox"></i> No messages found.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>