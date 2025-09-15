<?php
require 'db.php';
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

try {
    // Default to donation if type not sent
    $type = isset($data["type"]) && in_array($data["type"], ["donation", "membership"])
        ? $data["type"]
        : "donation";

    if ($type === "donation") {
        $stmt = $pdo->prepare("INSERT INTO donations 
            (name, email, phone, address, pan_number, amount, payment_id, status, created_on) 
            VALUES (:name, :email, :phone, :address, :pan_number, :amount, :payment_id, 'Success', NOW())");
    } else {
        $stmt = $pdo->prepare("INSERT INTO memberships 
            (name, email, phone, address, pan_number, amount, payment_id, status, created_on) 
            VALUES (:name, :email, :phone, :address, :pan_number, :amount, :payment_id, 'Active', NOW())");
    }

    $stmt->execute([
        ":name"       => $data["name"],
        ":email"      => $data["email"],
        ":phone"      => $data["phone"],
        ":address"    => $data["address"],
        ":pan_number" => $data["pan"],
        ":amount"     => $data["amount"],
        ":payment_id" => $data["payment_id"]
    ]);

    $id = $pdo->lastInsertId();
    $row = $stmt->queryString; // for debugging only (remove in prod)

    echo json_encode([
        "success" => true,
        "message" => ucfirst($type) . " saved successfully",
        "id" => $id
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
