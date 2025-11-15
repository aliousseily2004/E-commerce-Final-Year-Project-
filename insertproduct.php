<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Insertion Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .photo-upload {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .photo-upload input {
            flex-grow: 1;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        @media (max-width: 600px) {
            .form-container {
                padding: 15px;
            }
            .photo-upload {
                flex-direction: column;
            }
        }
        .size-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 5px;
}

.size-option {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.size-option:hover {
    border-color: #4CAF50;
    background-color: #f8f8f8;
}

.size-option input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}

@media (max-width: 600px) {
    .size-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 400px) {
    .size-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
    </style>
</head>
<body>
    <div class="form-container">
    <form id="productForm" action="./actions/insertproductaction.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
        <h1 style="text-align: center;">Add Product</h1>
            <label for="category">Product category</label>
            <select id="category" name="category" required>
                <option value="">Select category</option>
                <option value="Men">Men</option>
                <option value="Women">Women</option>
                <option value="Kids">Kids</option>
                <option value="Accessories">Accessories</option>
            </select>
        </div>
        <div class="form-group">
            <label for="subcategory">Product SubCategory</label>
            <select id="subcategory" name="subcategory" required>
                <option value="">Select SubCategory</option>
                <option value="Shoes">Shoes</option>
                <option value="Jeans">Jeans</option>
                <option value="T-Shirts">T-Shirt</option>
                <option value="Jackets">Jacket</option>
                <option value="Hoodies">Hoodies</option>
                <option value="Bags">Bags</option>
                <option value="Hats">Hats</option>
                <option value="Watches">Watches</option>
                <option value="Sunglasses">Sunglasses</option>
            </select>
        </div>


        <div class="form-group">
            <label>Product Photos (1-4)</label>
            <div class="photo-upload">
                <input type="file" name="photo1" accept="image/*">
                <input type="file" name="photo2" accept="image/*">
                <input type="file" name="photo3" accept="image/*">
                <input type="file" name="photo4" accept="image/*">
            </div>
        </div>

        <div class="form-group">
            <label for="title">Product Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="text" id="price" name="price" required>
        </div>

        <div class="form-group">
            <label for="discount">Discount (%)</label>
            <input type="number" id="discount" name="discount" min="0" max="100" value="0">
        </div>
        <div class="form-group">
            <label for="stock">stock </label>
            <input type="number" id="stock" name="stock" >
        </div>

        <div class="form-group">
    <label>Size (Select Multiple)</label>
    <div class="size-grid">
        <label class="size-option">
            <input type="checkbox" name="size[]" value="Small"> Small
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="Medium"> Medium
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="Large"> Large
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="ExtraLarge"> XL
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="36"> 36
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="37"> 37
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="38"> 38
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="39"> 39
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="40"> 40
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="41"> 41
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="42"> 42
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="43"> 43
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="24"> 24
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="25"> 25
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="26"> 26
        </label>
        <label class="size-option">
            <input type="checkbox" name="size[]" value="27"> 27
        </label>
    </div>
</div>

        <div class="form-group">
            <label for="attribute1">Attribute 1</label>
            <input type="text" id="attribute1" name="attribute1">
        </div>

        <div class="form-group">
            <label for="detail1">Detail 1</label>
            <input type="text" id="detail1" name="detail1">
        </div>

        <div class="form-group">
            <label for="attribute2">Attribute 2</label>
            <input type="text" id="attribute2" name="attribute2">
        </div>

        <div class="form-group">
            <label for="detail2">Detail 2</label>
            <input type="text" id="detail2" name="detail2">
        </div>

        <div class="form-group">
            <label for="attribute3">Attribute 3</label>
            <input type="text" id="attribute3" name="attribute3">
        </div>

        <div class="form-group">
            <label for="detail3">Detail 3</label>
            <input type="text" id="detail3" name="detail3">
        </div>

        <div class="form-group">
            <label for="about1">About Description 1</label>
            <textarea id="about1" name="about1" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="about2">About Description 2</label>
            <textarea id="about2" name="about2" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="about3">About Description 3</label>
            <textarea id="about3" name="about3" rows="3"></textarea>
        </div>

        <button type="submit" class="submit-btn">Submit Product</button>
    </form>
    </div>


</body>
</html>