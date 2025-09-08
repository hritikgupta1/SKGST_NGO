<?php
header('Content-Type: application/json');
require 'db.php';  // includes PDO connection ($pdo)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $name    = $data['name']    ?? ($_POST['name'] ?? '');
        $email   = $data['email']   ?? ($_POST['email'] ?? '');
        $mobile  = $data['mobile']  ?? ($_POST['mobile'] ?? ''); // note: lowercase 'mobile'
        $message = $data['message'] ?? ($_POST['message'] ?? '');

        $stmt = $pdo->prepare("
            INSERT INTO contact_form (name, email, mobile, message) 
            VALUES (:name, :email, :mobile, :message)
        ");

        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':mobile'  => $mobile,
            ':message' => $message
        ]);

        echo json_encode(["message" => " Message submitted successfully!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => " Database Error: " . $e->getMessage()]);
    }
}
?>
