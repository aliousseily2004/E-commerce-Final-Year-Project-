<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../connection.php";

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Invalid request method. Expected POST.");
    }

    // Retrieve and validate product ID
    if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
        throw new Exception("Invalid or missing product ID.");
    }
    $product_id = (int)$_POST['product_id'];

    // Retrieve form data
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $subcategory = filter_input(INPUT_POST, 'subcategory', FILTER_SANITIZE_STRING);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT) ?? 0;
    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT) ?? 0;

    // Attributes and details
    $attribute1 = filter_input(INPUT_POST, 'attribute1', FILTER_SANITIZE_STRING) ?? null;
    $detail1 = filter_input(INPUT_POST, 'detail1', FILTER_SANITIZE_STRING) ?? null;
    $attribute2 = filter_input(INPUT_POST, 'attribute2', FILTER_SANITIZE_STRING) ?? null;
    $detail2 = filter_input(INPUT_POST, 'detail2', FILTER_SANITIZE_STRING) ?? null;
    $attribute3 = filter_input(INPUT_POST, 'attribute3', FILTER_SANITIZE_STRING) ?? null;
    $detail3 = filter_input(INPUT_POST, 'detail3', FILTER_SANITIZE_STRING) ?? null;
    $about1 = filter_input(INPUT_POST, 'about1', FILTER_SANITIZE_STRING) ?? null;
    $about2 = filter_input(INPUT_POST, 'about2', FILTER_SANITIZE_STRING) ?? null;
    $about3 = filter_input(INPUT_POST, 'about3', FILTER_SANITIZE_STRING) ?? null;

    // Image handling
    $photo_paths = [];
    $photo_keys = ['photo1', 'photo2', 'photo3', 'photo4'];
    $upload_dir = "C:/xampp/htdocs/final/uploads/Editedimages/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    foreach ($photo_keys as $key) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$key]['tmp_name'];
            $file_type = strtolower(pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION));

            if (!in_array($file_type, ['jpeg', 'jpg', 'png', 'gif'])) {
                throw new Exception("Invalid file type for $key.");
            }

            $unique_name = $key . '_' . uniqid() . '.' . $file_type;
            $target_path = $upload_dir . $unique_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $photo_paths[$key] = "Editedimages/" . $unique_name;
            } else {
                throw new Exception("Failed to upload $key.");
            }
        }
    }

    // Database operations
    $pdo->beginTransaction();

    // Fetch existing images
    $stmt = $pdo->prepare("SELECT photo1, photo2, photo3, photo4 FROM product WHERE id = ?");
    $stmt->execute([$product_id]);
    $existing_images = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update product table
    $sql = "UPDATE product SET 
                category = ?, 
                subcategory = ?, 
                title = ?, 
                price = ?, 
                Attribute1 = ?, 
                Detail1 = ?, 
                Attribute2 = ?, 
                Detail2 = ?, 
                Attribute3 = ?, 
                Detail3 = ?, 
                About1 = ?, 
                About2 = ?, 
                About3 = ?, 
                
                stock = ?";

    // Add photo updates
    $params = [
        $category, $subcategory, $title, $price, 
        $attribute1, $detail1, $attribute2, $detail2, 
        $attribute3, $detail3, $about1, $about2, $about3, $stock
    ];

    foreach ($photo_keys as $key) {
        if (isset($photo_paths[$key])) {
            $sql .= ", $key = ?";
            $params[] = $photo_paths[$key];
        }
    }

    $sql .= " WHERE id = ?";
    $params[] = $product_id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update sizes and discount
    $size_columns = [
        'Small' => 0, 'Medium' => 0, 'Large' => 0, 'ExtraLarge' => 0,
        '36' => 0, '37' => 0, '38' => 0, '39' => 0,
        '40' => 0, '41' => 0, '42' => 0, '43' => 0,
        '24' => 0, '25' => 0, '26' => 0, '27' => 0
    ];

    $selected_sizes = $_POST['size'] ?? [];
    foreach ($selected_sizes as $size) {
        if (isset($size_columns[$size])) {
            $size_columns[$size] = 1;
        }
    }

    $attr_sql = "UPDATE productsattributes SET
                    Small = ?, Medium = ?, `Large` = ?, ExtraLarge = ?,
                    `36` = ?, `37` = ?, `38` = ?, `39` = ?,
                    `40` = ?, `41` = ?, `42` = ?, `43` = ?,
                    `24` = ?, `25` = ?, `26` = ?, `27` = ?,
                    discount = ?
                WHERE product_id = ?";

    $attr_params = [
        $size_columns['Small'], $size_columns['Medium'], $size_columns['Large'], $size_columns['ExtraLarge'],
        $size_columns['36'], $size_columns['37'], $size_columns['38'], $size_columns['39'],
        $size_columns['40'], $size_columns['41'], $size_columns['42'], $size_columns['43'],
        $size_columns['24'], $size_columns['25'], $size_columns['26'], $size_columns['27'],
        $discount, $product_id
    ];

    $stmt = $pdo->prepare($attr_sql);
    $stmt->execute($attr_params);

    // Delete old images if replaced
    foreach ($photo_keys as $key) {
        if (isset($photo_paths[$key]) && !empty($existing_images[$key])) {
            $old_path = "C:/xampp/htdocs/final/uploads/" . $existing_images[$key];
            if (file_exists($old_path)) {
                unlink($old_path);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);

} catch (Exception $e) {
  
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}