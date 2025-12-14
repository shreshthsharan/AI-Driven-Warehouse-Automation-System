<?php
// Database config
$host = "localhost";
$user = "root";
$pass = "asdf1998";
$db   = "Warehouse_inv";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed");
}

$sql = "SELECT * FROM rfid_logs ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['date']}</td>
            <td>{$row['time']}</td>
            <td>{$row['rfid']}</td>
            <td>{$row['status']}</td>
            <td>{$row['item']}</td>
            <td>{$row['size']}</td>
            <td>{$row['mfg']}</td>
            <td>{$row['exp']}</td>
        </tr>";
    }
}
$conn->close();
?>
