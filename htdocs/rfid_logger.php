<?php
header("Content-Type: application/json");
require_once 'config.php';

// --- Main Logic ---

// 1. Connect to RFID Database and Insert Log
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "RFID DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Extract and sanitize input
$rfid   = $data["rfid"] ?? null;
$status = $data["status"] ?? null;
$item   = $data["item"] ?? null; // Item is now required for inventory updates
$size   = $data["size"] ?? "-";
$mfg    = $data["mfg"] ?? "-";
$exp    = $data["exp"] ?? "-";

// Validate required fields
if (!$rfid || !$status || !$item) {
    echo json_encode(["status" => "error", "message" => "Missing required fields: rfid, status, and item are required."]);
    exit;
}

if ($rfid === "TEST1234") {
    echo json_encode(["status" => "success", "message" => "Test ping acknowledged"]);
    exit;
}

$date = date("Y-m-d");
$time = date("H:i:s");

// Prepare and execute the insert into rfid_logs
$sql_log = "INSERT INTO rfid_logs (date, time, rfid, status, item, size, mfg, exp)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_log = $conn->prepare($sql_log);
$stmt_log->bind_param("ssssssss", $date, $time, $rfid, $status, $item, $size, $mfg, $exp);

if (!$stmt_log->execute()) {
    echo json_encode(["status" => "error", "message" => "Log insert failed"]);
    $stmt_log->close();
    $conn->close();
    exit;
}
$stmt_log->close();
$conn->close();

// 2. Connect to Inventory DB and Update Stock (NEW LOGIC)
if ($status === 'Received' || $status === 'Dispatched') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Log inserted, but inventory DB connection failed"]);
        exit;
    }

    // Determine the stock operation (+1 for Received, -1 for Dispatched)
    $stock_operation = ($status === 'Received') ? "+ 1" : "- 1";
    $current_datetime = date("Y-m-d H:i:s");

    // Prepare the update query. We find the product by its name.
    $sql_update_inv = "UPDATE inventory 
                       SET stock = stock $stock_operation, last_update = ? 
                       WHERE product_name = ?";
    
    $stmt_inv = $conn->prepare($sql_update_inv);
    $stmt_inv->bind_param("ss", $current_datetime, $item);

    if ($stmt_inv->execute()) {
        if ($stmt_inv->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Log inserted and inventory updated successfully"]);
        } else {
            echo json_encode(["status" => "warning", "message" => "Log inserted, but product '{$item}' not found in inventory. No update occurred."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Log inserted, but inventory update failed"]);
    }

    $stmt_inv->close();
    $conn->close();
} else {
    // If status is neither Received nor Dispatched, just confirm the log insertion
    echo json_encode(["status" => "success", "message" => "Data logged, no inventory action needed"]);
}
?>