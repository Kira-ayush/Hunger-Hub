<?php
session_start();
require 'db.php';

// First, ensure we have a test user for foreign key constraints
$test_user_id = null;

// Check if users table exists and has a user
$users_check = $conn->query("SHOW TABLES LIKE 'users'");
if ($users_check && $users_check->num_rows > 0) {
    // Check if any users exist
    $user_result = $conn->query("SELECT id FROM users LIMIT 1");
    if ($user_result && $user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        $test_user_id = $user['id'];
        echo "✅ Using existing user ID: " . $test_user_id . "<br>";
    } else {
        // Try to create a test user if possible
        $user_columns_result = $conn->query("SHOW COLUMNS FROM users");
        $user_columns = [];
        if ($user_columns_result) {
            while ($column = $user_columns_result->fetch_assoc()) {
                $user_columns[] = $column['Field'];
            }
        }

        // Try to create a minimal user record
        if (in_array('username', $user_columns) || in_array('email', $user_columns)) {
            $user_insert_columns = [];
            $user_insert_values = [];
            $user_param_types = '';
            $user_param_values = [];

            if (in_array('username', $user_columns)) {
                $user_insert_columns[] = 'username';
                $user_insert_values[] = '?';
                $user_param_types .= 's';
                $user_param_values[] = 'test_user';
            }

            if (in_array('email', $user_columns)) {
                $user_insert_columns[] = 'email';
                $user_insert_values[] = '?';
                $user_param_types .= 's';
                $user_param_values[] = 'test@example.com';
            }

            if (in_array('password', $user_columns)) {
                $user_insert_columns[] = 'password';
                $user_insert_values[] = '?';
                $user_param_types .= 's';
                $user_param_values[] = password_hash('test123', PASSWORD_DEFAULT);
            }

            if (in_array('created_at', $user_columns)) {
                $user_insert_columns[] = 'created_at';
                $user_insert_values[] = 'NOW()';
            }

            if (!empty($user_insert_columns)) {
                $user_sql = "INSERT INTO users (" . implode(', ', $user_insert_columns) . ") VALUES (" . implode(', ', $user_insert_values) . ")";
                $user_stmt = $conn->prepare($user_sql);

                if ($user_stmt && !empty($user_param_values)) {
                    $user_stmt->bind_param($user_param_types, ...$user_param_values);
                    if ($user_stmt->execute()) {
                        $test_user_id = $conn->insert_id;
                        echo "✅ Created test user ID: " . $test_user_id . "<br>";
                    }
                }
            }
        }
    }
}

if ($test_user_id) {
    $_SESSION['user_id'] = $test_user_id; // Set in session for payment use
}

// Continue with order creation...

// First, check what columns exist in the orders table
$columns_result = $conn->query("SHOW COLUMNS FROM orders");
$available_columns = [];
if ($columns_result) {
    while ($column = $columns_result->fetch_assoc()) {
        $available_columns[] = $column['Field'];
    }
}

echo "<h3>Orders Table Columns Available:</h3>";
echo "<p>" . implode(', ', $available_columns) . "</p>";

// Build dynamic INSERT based on available columns
$test_amount = 299.50;
$insert_columns = [];
$insert_values = [];
$param_types = '';
$param_values = [];

// Always try to include basic required columns
if (in_array('total_amount', $available_columns)) {
    $insert_columns[] = 'total_amount';
    $insert_values[] = '?';
    $param_types .= 'd';
    $param_values[] = $test_amount;
}

if (in_array('payment_status', $available_columns)) {
    $insert_columns[] = 'payment_status';
    $insert_values[] = '?';
    $param_types .= 's';
    $param_values[] = 'Pending';
}

if (in_array('created_at', $available_columns)) {
    $insert_columns[] = 'created_at';
    $insert_values[] = 'NOW()';
}

// Optional columns with fallback values
if (in_array('customer_name', $available_columns)) {
    $insert_columns[] = 'customer_name';
    $insert_values[] = '?';
    $param_types .= 's';
    $param_values[] = 'Test Customer';
}

if (in_array('email', $available_columns)) {
    $insert_columns[] = 'email';
    $insert_values[] = '?';
    $param_types .= 's';
    $param_values[] = 'test@example.com';
}

if (in_array('phone', $available_columns)) {
    $insert_columns[] = 'phone';
    $insert_values[] = '?';
    $param_types .= 's';
    $param_values[] = '1234567890';
}

if (in_array('address', $available_columns)) {
    $insert_columns[] = 'address';
    $insert_values[] = '?';
    $param_types .= 's';
    $param_values[] = 'Test Address';
}

// Only proceed if we have at least some columns to insert
if (count($insert_columns) > 0) {
    $sql = "INSERT INTO orders (" . implode(', ', $insert_columns) . ") VALUES (" . implode(', ', $insert_values) . ")";

    echo "<h3>SQL Query:</h3>";
    echo "<code>" . htmlspecialchars($sql) . "</code><br><br>";

    $stmt = $conn->prepare($sql);

    if ($stmt && !empty($param_values)) {
        $stmt->bind_param($param_types, ...$param_values);

        if ($stmt->execute()) {
            $order_id = $conn->insert_id;

            // Set up test session with the real order ID
            $_SESSION['pending_order_id'] = $order_id;
            $_SESSION['pending_amount'] = $test_amount;

            echo "✅ Test session set up successfully!<br>";
            echo "✅ Real order created in database<br>";
            echo "Order ID: " . $order_id . "<br>";
            echo "Amount: ₹" . number_format($test_amount, 2) . "<br><br>";

            echo '<a href="payment_upi.php" class="btn btn-primary">Go to UPI Payment Page</a><br><br>';
            echo '<a href="debug_payment.php" class="btn btn-secondary">Debug Payment Info</a>';
        } else {
            echo "❌ Error creating test order: " . $stmt->error . "<br><br>";
            echo '<a href="debug_payment.php" class="btn btn-secondary">Debug Payment Info</a>';
        }
    } else {
        echo "❌ Error preparing SQL statement<br><br>";
        echo '<a href="debug_payment.php" class="btn btn-secondary">Debug Payment Info</a>';
    }
} else {
    echo "❌ No suitable columns found in orders table<br><br>";
    echo '<a href="debug_payment.php" class="btn btn-secondary">Debug Payment Info</a>';
}
?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <h3>Test UPI Payment Setup</h3>
    <p>Session has been configured with test order data.</p>
</body>

</html>