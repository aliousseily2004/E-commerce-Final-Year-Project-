<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
</head>
<body>
    <h1>Delete Product by ID</h1>
    <form  method="POST" action="./actions/deleteproductaction.php">
        <label for="product_id">Product ID:</label>
        <input type="text" id="product_id" name="product_id" required>
        <input type="submit" value="Delete Product">
    </form>
</body>
</html>