<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Fetch profile image
$stmt = $conn->prepare("SELECT profile_pic FROM admins WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result_admin = $stmt->get_result();
$admin = $result_admin->fetch_assoc();
$profile_img = $admin['profile_pic'] ?? 'default.png';

// Handle message status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $message_id = intval($_POST['message_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE contact_messages SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $message_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Message status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating message status.";
    }

    header("Location: messages.php");
    exit();
}

// Get message statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_messages,
        COUNT(CASE WHEN status = 'unread' THEN 1 END) as unread_messages,
        COUNT(CASE WHEN status = 'read' THEN 1 END) as read_messages,
        COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as new_messages
    FROM contact_messages
";
$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : [
    'total_messages' => 0,
    'unread_messages' => 0,
    'read_messages' => 0,
    'new_messages' => 0
];

// Pagination and filtering
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;
$status_filter = $_GET['status'] ?? '';

$where_clause = '';
$params = [];
$param_types = '';

if ($status_filter) {
    $where_clause = "WHERE status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM contact_messages $where_clause";
if ($params) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result ? $count_result->fetch_assoc()['total'] : 0;
} else {
    $count_result = $conn->query($count_query);
    $total_records = $count_result ? $count_result->fetch_assoc()['total'] : 0;
}

$total_pages = ceil($total_records / $per_page);

// Get messages with pagination
$query = "
    SELECT cm.*, u.name as user_name 
    FROM contact_messages cm
    LEFT JOIN users u ON cm.user_id = u.id
    $where_clause
    ORDER BY cm.created_at DESC 
    LIMIT ? OFFSET ?
";

$params[] = $per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$messages_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .profile-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .message-row.unread {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .message-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-unread {
            background: #fff3cd;
            color: #856404;
        }

        .status-read {
            background: #d4edda;
            color: #155724;
        }

        .status-replied {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="text-center mb-4">
                    <img src="../uploads/<?php echo htmlspecialchars($profile_img); ?>" class="profile-img mb-2" alt="Admin Image" />
                    <h6 class="text-white"><?php echo $_SESSION['admin_name']; ?></h6>
                </div>

                <h4 class="text-white mb-4">
                    <i class="fas fa-utensils me-2"></i>HungerHub Admin
                </h4>

                <nav class="nav flex-column">
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-bag me-2"></i>Orders
                    </a>
                    <a class="nav-link" href="menu_items.php">
                        <i class="fas fa-utensils me-2"></i>Menu Items
                    </a>
                    <a class="nav-link" href="payments.php">
                        <i class="fas fa-credit-card me-2"></i>Payments
                    </a>
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users me-2"></i>Customers
                    </a>
                    <a class="nav-link active" href="messages.php">
                        <i class="fas fa-envelope me-2"></i>Messages
                        <?php if ($stats['unread_messages'] > 0): ?>
                            <span class="badge bg-danger ms-1"><?= $stats['unread_messages'] ?></span>
                        <?php endif; ?>
                    </a>
                    <hr class="text-white-50">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-envelope me-2"></i>Messages & Feedback</h2>
                </div>

                <!-- Message Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                <h5><?= number_format($stats['total_messages']) ?></h5>
                                <small class="text-muted">Total Messages</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-envelope-open fa-2x text-warning mb-2"></i>
                                <h5><?= number_format($stats['unread_messages']) ?></h5>
                                <small class="text-muted">Unread Messages</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-check fa-2x text-success mb-2"></i>
                                <h5><?= number_format($stats['read_messages']) ?></h5>
                                <small class="text-muted">Read Messages</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-star fa-2x text-info mb-2"></i>
                                <h5><?= number_format($stats['new_messages']) ?></h5>
                                <small class="text-muted">This Week</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Messages</option>
                                    <option value="unread" <?= $status_filter == 'unread' ? 'selected' : '' ?>>Unread</option>
                                    <option value="read" <?= $status_filter == 'read' ? 'selected' : '' ?>>Read</option>
                                    <option value="replied" <?= $status_filter == 'replied' ? 'selected' : '' ?>>Replied</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="messages.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Messages Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Messages</h5>
                        <span class="badge bg-primary"><?= number_format($total_records) ?> messages</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($messages_result && $messages_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>From</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($message = $messages_result->fetch_assoc()): ?>
                                            <tr class="message-row <?= $message['status'] ?>">
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($message['user_name'] ?? $message['name'] ?? 'Unknown') ?></strong>
                                                        <br><small class="text-muted"><?= htmlspecialchars($message['email'] ?? '') ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($message['subject'] ?? 'No Subject') ?></strong>
                                                </td>
                                                <td>
                                                    <div style="max-width: 300px;">
                                                        <?= htmlspecialchars(substr($message['message'] ?? '', 0, 100)) ?>
                                                        <?= strlen($message['message'] ?? '') > 100 ? '...' : '' ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small><?= date('M d, Y H:i', strtotime($message['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <span class="message-status status-<?= $message['status'] ?>">
                                                        <?= ucfirst($message['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary"
                                                            onclick="viewMessage(<?= $message['id'] ?>)"
                                                            title="View Message">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if ($message['status'] == 'unread'): ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                                                <input type="hidden" name="status" value="read">
                                                                <button type="submit" name="update_status"
                                                                    class="btn btn-outline-success"
                                                                    title="Mark as Read">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No messages found</h5>
                                <p class="text-muted">Customer messages will appear here when they contact you.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer">
                            <nav>
                                <ul class="pagination pagination-sm justify-content-center mb-0">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Details Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="messageDetails">
                    <!-- Message details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function viewMessage(messageId) {
            // Simple alert for now since we don't have the get_message_details.php file
            alert('Viewing message ID: ' + messageId + '. This feature can be enhanced with detailed message view.');
        }
    </script>
</body>

</html>