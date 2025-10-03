<?php
require 'db.php';

echo "<h2>Database Table Structure Check</h2>";

// Check orders table structure
echo "<h3>Orders Table Structure:</h3>";
$result = $conn->query("DESCRIBE orders");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error checking orders table: " . $conn->error;
}

// Check if payments table exists
echo "<h3>Payments Table Structure:</h3>";
$result = $conn->query("DESCRIBE payments");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td><td>" . $row['Key'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "Payments table doesn't exist or error: " . $conn->error;
}
