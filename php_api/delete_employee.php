<?php
include 'condb.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['emp_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ไม่พบรหัสพนักงาน"]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM employee WHERE emp_id = :emp_id");
    $stmt->execute([':emp_id' => $data['emp_id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลพนักงาน"]);
        exit;
    }

    echo json_encode(["success" => true, "message" => "ลบข้อมูลพนักงานเรียบร้อย"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลพนักงานได้"]);
}