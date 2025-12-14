<?php
// DB connection
$host = "localhost";
$user = "root";
$pass = "asdf1998"; // your password if any
$db   = "Warehouse_inv";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all rows
$sql = "SELECT * FROM rfid_logs ORDER BY id DESC";
$result = $conn->query($sql);

echo "<h2>RFID Logs</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>ID</th><th>Date</th><th>Time</th><th>RFID</th>
        <th>Status</th><th>Item</th><th>Size</th><th>MFG</th><th>EXP</th>
      </tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td><td>{$row['date']}</td><td>{$row['time']}</td>
                <td>{$row['rfid']}</td><td>{$row['status']}</td>
                <td>{$row['item']}</td><td>{$row['size']}</td>
                <td>{$row['mfg']}</td><td>{$row['exp']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No records found</td></tr>";
}

echo "</table>";
$conn->close();
?>
