<?php
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$sql = "SELECT date, status, item FROM rfid_logs";
$result = $conn->query($sql);

$statusData = [];
$itemData = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dateParts = explode('-', $row['date']);
        $monthKey = $dateParts[0] . '-' . $dateParts[1];

        $status = $row['status'];
        $item = $row['item'];

        $statusData[$monthKey][$status] = ($statusData[$monthKey][$status] ?? 0) + 1;
        $itemData[$monthKey][$item] = ($itemData[$monthKey][$item] ?? 0) + 1;
    }
}

$conn->close();

echo json_encode([
    "statusData" => $statusData,
    "itemData" => $itemData
]);
?>
