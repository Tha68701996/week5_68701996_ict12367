<?php
include 'condb.php';

function ensureAssetSchema($conn) {
    $conn->exec("CREATE TABLE IF NOT EXISTS assets (
        asset_id INT AUTO_INCREMENT PRIMARY KEY,
        asset_code VARCHAR(50) NOT NULL,
        asset_name VARCHAR(255) NOT NULL,
        purchase_date DATE DEFAULT NULL,
        price DECIMAL(10,2) DEFAULT 0,
        stock INT NOT NULL DEFAULT 0,
        image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $columns = $conn->query("SHOW COLUMNS FROM assets")->fetchAll(PDO::FETCH_COLUMN, 0);

    if (!in_array('stock', $columns, true)) {
        $conn->exec("ALTER TABLE assets ADD COLUMN stock INT NOT NULL DEFAULT 0 AFTER price");
    }

    if (!in_array('image', $columns, true)) {
        $conn->exec("ALTER TABLE assets ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER stock");
    }
}

try {
    ensureAssetSchema($conn);

    $action = $_POST['action'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
        switch ($action) {
            case 'add':
                $asset_code = $_POST['asset_code'] ?? '';
                $asset_name = $_POST['asset_name'] ?? '';
                $purchase_date = $_POST['purchase_date'] ?? null;
                $price = $_POST['price'] ?? 0;
                $stock = $_POST['stock'] ?? 0;

                $filename = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $targetDir = __DIR__ . '/uploads/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    $filename = time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $filename);
                }

                $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, purchase_date, price, stock, image)
                    VALUES (:asset_code, :asset_name, :purchase_date, :price, :stock, :image)");
                $stmt->bindParam(':asset_code', $asset_code);
                $stmt->bindParam(':asset_name', $asset_name);
                $stmt->bindParam(':purchase_date', $purchase_date);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':image', $filename);

                echo json_encode($stmt->execute() ? ["success" => true, "message" => "เพิ่มสินค้าสำเร็จ"] : ["success" => false, "message" => "เพิ่มสินค้าล้มเหลว"]);
                break;

            case 'update':
                $asset_id = $_POST['asset_id'] ?? null;
                $asset_code = $_POST['asset_code'] ?? '';
                $asset_name = $_POST['asset_name'] ?? '';
                $purchase_date = $_POST['purchase_date'] ?? null;
                $price = $_POST['price'] ?? 0;
                $stock = $_POST['stock'] ?? 0;

                $imageSQL = '';
                $filename = null;

                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $targetDir = __DIR__ . '/uploads/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    $filename = time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $filename);
                    $imageSQL = ', image = :image';
                }

                $sql = "UPDATE assets SET
                        asset_code = :asset_code,
                        asset_name = :asset_name,
                        purchase_date = :purchase_date,
                        price = :price,
                        stock = :stock
                        $imageSQL
                    WHERE asset_id = :asset_id";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':asset_code', $asset_code);
                $stmt->bindParam(':asset_name', $asset_name);
                $stmt->bindParam(':purchase_date', $purchase_date);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':asset_id', $asset_id);

                if ($filename !== null) {
                    $stmt->bindParam(':image', $filename);
                }

                echo json_encode($stmt->execute() ? ["success" => true, "message" => "แก้ไขสินค้าสำเร็จ"] : ["success" => false, "message" => "แก้ไขสินค้าล้มเหลว"]);
                break;

            case 'delete':
                $asset_id = $_POST['asset_id'] ?? null;
                $stmt = $conn->prepare("DELETE FROM assets WHERE asset_id = :asset_id");
                $stmt->bindParam(':asset_id', $asset_id);

                echo json_encode($stmt->execute() ? ["success" => true, "message" => "ลบสินค้าสำเร็จ"] : ["success" => false, "message" => "ลบสินค้าล้มเหลว"]);
                break;

            default:
                echo json_encode(["success" => false, "message" => "Action ไม่ถูกต้อง"]);
                break;
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM assets ORDER BY asset_id DESC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $products]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
