<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="footer.css">
    <style>
        .delete-button {
            background-color: #f44336; /* Red */
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .delete-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<?php

require "connection.php";
session_start();
// Check if user is logged in
if (!isset($_SESSION['LoggedIn']) || !$_SESSION['LoggedIn']) {
    header("Location: login.php");
    exit();
}
$user_id=$_SESSION['id'];
// Fetch user details from database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT); // Use session ID
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$user) {
        // If no user found, destroy session and redirect to login
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    // Log error
    error_log("Profile Fetch Error: " . $e->getMessage());
    // Redirect with error
    header("Location: login.php?error=database_error");
    exit();
}
?>
<body>
    <?php
    require "nav.php";

    ?>


    <div class="profile-container">

        <div class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="active" data-section="profile">
                    <i class="fas fa-user"></i> Personal Info
                </a></li>
                <li><a href="#" data-section="orders">
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a></li>
                <li><a href="#" data-section="settings">
                    <i class="fas fa-cog"></i> Account Settings
                </a></li>
            </ul>
        </div>


        <div class="profile-content">

            <div id="profile-section" class="section">
                <div class="profile-header">
                <?php if (empty($user['photo'])): ?>
                <img src="Unknown_person.jpg" alt="Unknown Profile Picture" class="profile-pic">
                    <?php else: ?>

                                <img src="<?php echo '/Final/userprofile/' . htmlspecialchars(basename($user['photo'])); ?>" alt="Profile Picture" class="profile-pic">

                    <?php endif; ?>
                    <div>
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                </div>
            </div>

            <div class="personal-info">
                <div class="info-item">
                    <strong>Full Name</strong>
                    <p><?php echo htmlspecialchars($user['name']); ?></p>
                </div>
                <div class="info-item">
                    <strong>Email</strong>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="info-item">
                    <strong>Phone</strong>
                    <p><?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
                <div class="info-item">
                    <strong>Address</strong>
                    <p><?php echo htmlspecialchars($user['street'] . ', ' . $user['city']); ?></p>
                </div>
            </div>
        </div>
        <?php
// Assuming $pdo is your PDO connection that's already open

try {
    // Prepared statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id");

    // Bind the user_id parameter
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch all orders for the user
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Now $orders contains all the orders for the specified user

} catch(PDOException $e) {
    // Handle any database errors
    echo "Error: " . $e->getMessage();
}
?>

               <div id="orders-section" class="section" style="display:none;">
    <h2>My Orders</h2>
    <table class="orders-table w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Order ID</th>

                <th class="border p-2 text-left">Quantity</th>
                <th class="border p-2 text-left">Size</th>
                <th class="border p-2 text-left">Order Date</th>
                <th class="border p-2 text-left">Price</th>
                <th class="border p-2 text-left">Status</th>
                <th class="border p-2 text-left">Photo</th>
                <th class="border p-2 text-left">Cancel Order</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Assuming $orders is the array from the database
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    echo "<tr>";
                    echo "<td class='border p-2'>" . htmlspecialchars($order['order_id']) . "</td>";

                    echo "<td class='border p-2'>" . htmlspecialchars($order['quantity']) . "</td>";
                    echo "<td class='border p-2'>" . htmlspecialchars($order['size']) . "</td>";
                    echo "<td class='border p-2'>" . htmlspecialchars($order['order_date']) . "</td>";
                    echo "<td class='border p-2'>$" . htmlspecialchars($order['price']) . "</td>";
                    echo "<td class='border p-2'>" . htmlspecialchars($order['status']) . "</td>";
                    echo "<td class='border p-2'><img src='" . htmlspecialchars($order['photo']) . "' alt='Order Photo' class='w-16 h-16 object-cover'></td>";
                    echo "<td class='border p-2'>";
                    // Only show cancel button if the order status is not 'completed' or 'shipped'
                   
if ($order['status'] === 'active') {
    $orderDate = new DateTime($order['order_date']);
    $now = new DateTime();
    $isSameDay = $orderDate->format('Y-m-d') === $now->format('Y-m-d');
    
    echo "<form action='cancelorder.php' method='post' onsubmit='return confirmCancel(this)'>";
    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($order['order_id']) . "'>";
    echo "<button type='submit' class='delete-button' " . (!$isSameDay ? "disabled title='Cancellations allowed only on the same day'" : "") . ">";
    echo "Cancel";
    echo "</button>";
    echo "</form>";
    
    if (!$isSameDay) {
        echo "<small class='text-red-500 block'>Cancellation unavailable after midnight</small>";
    }
}
echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' class='text-center p-4'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


                <div id="settings-section" class="section" style="display:none;">
    <h2>Account Settings</h2>
    <form action="./actions/edit_action.php" method="POST" enctype="multipart/form-data">
        <div class="personal-info">
        <div class="info-item">
                    <label for="name">Change Name</label>
                    <input id="name" name="name" placeholder="New Name">
                </div>
                <div class="info-item">
                    <label for="email">Change Email</label>
                    <input type="email" id="email" name="email" placeholder="New email">
                </div>
                <div class="info-item">
                    <label for="password">Change Password</label>
                    <input type="password" id="password" name="password" placeholder="New password">
                </div>
                <div class="info-item">
                    <label for="phone">Change Phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="New phone number">
                </div>
                <div class="info-item">
                    <label for="profile-photo">Change Profile Photo</label>
                    <input type="file" id="profile-photo" name="profile_photo" accept="image/*">
                </div>
                <div class="save">
                <button type="submit">Save Changes</button></div>
            </div>


            <div class="personal-info">
                <div class="info-item">
                    <h3>Change Address</h3>
                    <label for="street-name">Street Name</label>
                    <input type="text" id="street-name" name="street_name" placeholder="Street Name">

                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="City">
                </div>
            </div>


                </div>


            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all sidebar menu items
    const menuItems = document.querySelectorAll('.sidebar-menu a');

    // Get all sections
    const sections = document.querySelectorAll('.section');

    // Initially hide all sections except the profile section
    sections.forEach(section => {
        if (section.id !== 'profile-section') {
            section.style.display = 'none';
        }
    });

    // Add click event to each menu item
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all menu items
            menuItems.forEach(menu => menu.classList.remove('active'));

            // Add active class to clicked item
            this.classList.add('active');

            // Get the section to show
            const sectionToShow = this.getAttribute('data-section');

            // Hide all sections
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show the selected section
            const selectedSection = document.getElementById(`${sectionToShow}-section`);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
        });
    });

    // Ensure the default (profile) section is active on page load
    document.querySelector('.sidebar-menu a[data-section="profile"]').classList.add('active');
});

</script>


    <?php require "footer.php"; ?>

   <script src="nav.js"></script>
   <script src="profile.js"></script>

</body>
</html>