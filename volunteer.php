<?php
header('Content-Type: application/json');
require 'db.php';  // includes PDO connection ($pdo)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // If form sends JSON
        $data = json_decode(file_get_contents('php://input'), true);

        // If form is a normal <form>, fallback to $_POST
        $name     = $data['name']     ?? ($_POST['name'] ?? '');
        $email    = $data['email']    ?? ($_POST['email'] ?? '');
        $phone    = $data['phone']    ?? ($_POST['phone'] ?? '');
        $interest = $data['interest'] ?? ($_POST['interest'] ?? '');
        $message  = $data['message']  ?? ($_POST['message'] ?? '');

        $stmt = $pdo->prepare("
            INSERT INTO volunteer_form (name, email, phone, interest, message) 
            VALUES (:name, :email, :phone, :interest, :message)
        ");

        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':phone'    => $phone,
            ':interest' => $interest,
            ':message'  => $message
        ]);

        echo json_encode(["message" => "Thank you! Your interest has been submitted."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database Error: " . $e->getMessage()]);
    }
}
?>
