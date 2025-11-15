<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Ensure PDO connection is properly included
    require_once "../connection.php";

    // Verify PDO connection
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Database connection failed");
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Validate required fields
        $required_fields = ['category', 'subcategory', 'title', 'price'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Sanitize and validate input data
        $category = filter_var($_POST['category']);
        $subcategory = filter_var($_POST['subcategory']);
        $title = filter_var($_POST['title']);
        $price = filter_var($_POST['price']);
        
        // Additional fields with null coalescing
        $attribute1 = $_POST['attribute1'] ?? '';
        $detail1 = $_POST['detail1'] ?? '';
        $attribute2 = $_POST['attribute2'] ?? '';
        $detail2 = $_POST['detail2'] ?? '';
        $attribute3 = $_POST['attribute3'] ?? '';
        $detail3 = $_POST['detail3'] ?? '';
        $about1 = $_POST['about1'] ?? '';
        $about2 = $_POST['about2'] ?? '';
        $about3 = $_POST['about3'] ?? '';
        $stock = $_POST['stock'] ?? '';

        // Initialize photo variables
        $photos = ['', '', '', ''];
        $target_dir = "C:/xampp/htdocs/Final/uploads/";

        // Ensure upload directory exists and is writable
        if (!is_dir($target_dir)) {
            throw new Exception("Upload directory does not exist: $target_dir");
        }

        if (!is_writable($target_dir)) {
            throw new Exception("Upload directory is not writable: $target_dir");
        }

        // Photo upload handling
        for ($i = 1; $i <= 4; $i++) {
            $photo_key = "photo" . $i;
            
            // Check if file was uploaded
            if (isset($_FILES[$photo_key]) && $_FILES[$photo_key]['error'] == UPLOAD_ERR_OK) {
                $original_filename = basename($_FILES[$photo_key]['name']);
                $fileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

                // Enhanced filename to prevent overwriting
                $unique_filename = uniqid() . '.' . $fileType;
                $target_file = $target_dir . $unique_filename;

                // Validate file type
                $allowed_types = ['jpeg', 'jpg', 'png', 'gif'];
                if (!in_array($fileType, $allowed_types)) {
                    throw new Exception("Unsupported file type for $photo_key: $fileType");
                }

                // Check file size (limit to 10MB)
                if ($_FILES[$photo_key]['size'] > 10000000) {
                    throw new Exception("File size too large for $photo_key");
                }

                // Validate image
                $image_info = getimagesize($_FILES[$photo_key]['tmp_name']);
                if (!$image_info) {
                    throw new Exception("Invalid image for $photo_key");
                }

                // Move the uploaded file
                if (move_uploaded_file($_FILES[$photo_key]['tmp_name'], $target_file)) {
                    $photos[$i-1] = $unique_filename; // Store the filename
                } else {
                    throw new Exception("Error moving uploaded file for $photo_key");
                }
            }
        }

        // Prepare PDO statement for product insertion
        $sql = "INSERT INTO product (
            category, subcategory, photo1, photo2, photo3, photo4,
            title, price, Attribute1, Detail1, Attribute2, Detail2,
            Attribute3, Detail3, About1, About2, About3, stock
        ) VALUES (
            :category, :subcategory, :photo1, :photo2, :photo3, :photo4,
            :title, :price, :Attribute1, :Detail1, :Attribute2, :Detail2,
            :Attribute3, :Detail3, :About1, :About2, :About3, :stock
        )";
        
        try {
            // Begin a transaction
            $pdo->beginTransaction();

            // Prepare statement
            $stmt = $pdo->prepare($sql);
            
            // Price handling (remove $ sign and convert to float)
            $price = floatval(str_replace('$', '', $_POST['price']));
        
            // Bind parameters with default empty string to prevent null
            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            $stmt->bindValue(':subcategory', $subcategory, PDO::PARAM_STR);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price, PDO::PARAM_STR);
            $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
            
            // Attributes and details
            $stmt->bindValue(':Attribute1', $attribute1, PDO::PARAM_STR);
            $stmt->bindValue(':Detail1', $detail1, PDO::PARAM_STR);
            $stmt->bindValue(':Attribute2', $attribute2, PDO::PARAM_STR);
            $stmt->bindValue(':Detail2', $detail2, PDO::PARAM_STR);
            $stmt->bindValue(':Attribute3', $attribute3, PDO::PARAM_STR);
            $stmt->bindValue(':Detail3', $detail3, PDO::PARAM_STR);
            
            // About sections
            $stmt->bindValue(':About1', $about1, PDO::PARAM_STR);
            $stmt->bindValue(':About2', $about2, PDO::PARAM_STR);
            $stmt->bindValue(':About3', $about3, PDO::PARAM_STR);
            
            // Photos (use empty string if no photo)
            $stmt->bindValue(':photo1', $photos[0], PDO::PARAM_STR);
            $stmt->bindValue(':photo2', $photos[1], PDO::PARAM_STR);
            $stmt->bindValue(':photo3', $photos[2], PDO::PARAM_STR);
            $stmt->bindValue(':photo4', $photos[3], PDO::PARAM_STR);
        
            // Execute the product insertion
            if (!$stmt->execute()) {
                // Rollback the transaction
                $pdo->rollBack();
                
                // Get detailed error info
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Product insertion failed: " . print_r($errorInfo, true));
            }

            // Get the auto-generated product_id
            $product_id = $pdo->lastInsertId();

            // Discount handling
            $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
            $selected_sizes = isset($_POST['size']) ? $_POST['size'] : [];

            // Ensure it's an array
            if (!is_array($selected_sizes)) {
                $selected_sizes = [$selected_sizes];
            }
            
            error_log("Selected Sizes: " . print_r($selected_sizes, true));
            
            // Create an associative array to map all possible sizes
            $size_columns = [
                'Small' => 0, 'Medium' => 0, 'Large' => 0, 'ExtraLarge' => 0,
                '36' => 0, '37' => 0, '38' => 0, '39' => 0,
                '40' => 0, '41' => 0, '42' => 0, '43' => 0,
                '24' => 0, '25' => 0, '26' => 0, '27' => 0
            ];
            
            // Set selected sizes to 1
            foreach ($selected_sizes as $size) {
                // Trim and sanitize the size
                $size = trim($size);
                
                // Check if the size exists in our size columns
                if (array_key_exists($size, $size_columns)) {
                    $size_columns[$size] = 1; // Mark the size as selected
                } else {
                    error_log("Invalid size detected: " . $size);
                }
            }
            
            // Prepare the attributes insert statement
            $attr_sql = "INSERT INTO productsattributes (
                product_id, 
                Small, Medium, `Large`, ExtraLarge, 
                `36`, `37`, `38`, `39`, 
                `40`, `41`, `42`, `43`, 
                `24`, `25`, `26`, `27`, 
                discount
            ) VALUES (
                :product_id, 
                :Small, :Medium, :Large, :ExtraLarge, 
                :36, :37, :38, :39, 
                :40, :41, :42, :43, 
                :24, :25, :26, :27, 
                :discount
            )";
            
            // Prepare the attributes statement
            $attr_stmt = $pdo->prepare($attr_sql);
            
            // Bind parameters for attributes
            $attr_stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            
            // Bind each size column
            $attr_stmt->bindValue(':Small', $size_columns['Small'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':Medium', $size_columns['Medium'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':Large', $size_columns['Large'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':ExtraLarge', $size_columns['ExtraLarge'], PDO::PARAM_INT);
            
            $attr_stmt->bindValue(':36', $size_columns['36'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':37', $size_columns['37'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':38', $size_columns['38'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':39', $size_columns['39'], PDO::PARAM_INT);
            
            $attr_stmt->bindValue(':40', $size_columns['40'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':41', $size_columns['41'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':42', $size_columns['42'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':43', $size_columns['43'], PDO::PARAM_INT);
            
            $attr_stmt->bindValue(':24', $size_columns['24'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':25', $size_columns['25'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':26', $size_columns['26'], PDO::PARAM_INT);
            $attr_stmt->bindValue(':27', $size_columns['27'], PDO::PARAM_INT);
            
            // Bind discount
            $attr_stmt->bindValue(':discount', $discount, PDO::PARAM_STR);
            // Execute the attributes statement
            if (!$attr_stmt->execute()) {
                // Rollback the transaction
                $pdo->rollBack();
                
                // Detailed error logging
                $errorInfo = $attr_stmt->errorInfo();
                throw new Exception("Failed to insert size attributes: " . print_r($errorInfo, true));
            }

            // Commit the transaction
            $pdo->commit();

            header("Location: ../dashbord.php");
          
            exit;

        } catch (PDOException $e) {
            // Rollback the transaction in case of any error
            $pdo->rollBack();
            throw new Exception("PDO Error: " . $e->getMessage());
        }
    } else {
        throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    // Log the full error details
    error_log("Full Error: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Return detailed error response
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage(),
        'debug_info' => [
            'post_data' => $_POST,
            'files_data' => $_FILES
        ]
    ]);
    exit;
}
?>
