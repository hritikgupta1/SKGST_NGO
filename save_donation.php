<?php
require 'db.php';
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO donations (name,email,phone,address,pan_number,amount,payment_id,status) 
                           VALUES (:name,:email,:phone,:address,:pan_number,:amount,:payment_id,'Success')");
    $stmt->execute([
        ":name" => $data["name"],
        ":email" => $data["email"],
        ":phone" => $data["phone"],
        ":address" => $data["address"],
        ":pan_number" => $data["pan"],
        ":amount" => $data["amount"],
        ":payment_id" => $data["payment_id"]
    ]);

    $id = $pdo->lastInsertId();
    $row = $pdo->query("SELECT * FROM donations WHERE id = $id")->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "donation" => $row]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
