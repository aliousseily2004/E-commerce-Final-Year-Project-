<!DOCTYPE html>
<html lang="en">
    <?php
session_start();
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="footer.css">
    <style>
        .comment-container {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background-color: #f7f7f7;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        }

        .comment-container .flex-shrink-0 {
            flex-shrink: 0;
        }

        .comment-container img {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-container .flex-grow {
            flex-grow: 1;
        }

        .comment-container .items-center {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .comment-container h4 {
            font-weight: 600;
            color: #2d3748;
            margin-right: 0.75rem;
        }

        .comment-container .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .comment-container .text-gray-500 {
            color: #718096;
        }

        .comment-container .text-gray-700 {
            color: #4a5568;
        }

        .comment-container .leading-relaxed {
            line-height: 1.625;
        }

        .comment-container .mt-2 {
            margin-top: 0.5rem;
        }

        .comment-container .space-x-4 > * + * {
            margin-left: 1rem;
        }

        .comment-container button {
            color: #718096;
            transition: color 0.15s ease-in-out;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .comment-container button:hover {
            color: #3b82f6;
        }

        .reply-form {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #edf2f7;
            border-radius: 0.25rem;
        }

        .reply-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e0;
            border-radius: 0.25rem;
            box-sizing: border-box;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .reply-form button {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background-color 0.15s ease-in-out;
        }

        .reply-form button:hover {
            background-color: #2c64d4;
        }

        .reply-form .cancel-reply-button {
            background-color: transparent;
            color: #718096;
            border: 1px solid #cbd5e0;
            margin-left: 0.5rem;
        }

        .reply-form .cancel-reply-button:hover {
            color: #4a5568;
            border-color: #a0aec0;
        }
        .hidden{
            display: none;
        }
        .reply-container {
            display: flex;
            gap: 0.5rem;
            padding: 0.5rem;
            background-color: #f0f0f0;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
            margin-left: 2rem; /* Indent replies */
        }

        .reply-container .flex-shrink-0 {
            flex-shrink: 0;
        }

        .reply-container img {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .reply-container .flex-grow {
            flex-grow: 1;
        }

        .reply-container .reply-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .reply-container h6 {
            font-weight: bold;
            font-size: 0.875rem;
            margin-right: 0.5rem;
        }

        .reply-container .text-sm {
            font-size: 0.75rem;
            color: #777;
        }
    </style>
</head>
<body>

<?php
// Assuming you have a connection to the database

require "connection.php"; // Adjust the path as necessary

// Get the product_id from the URL
$productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$userId = $_SESSION['id'];
// Fetch comments for the specific product
$query = "SELECT 
    c.*, 
    u.name AS user_name, 
    u.photo AS user_photo,
    CASE WHEN l.comment_id IS NOT NULL THEN 1 ELSE 0 END AS is_liked
FROM 
    comments c
JOIN 
    users u ON c.user_id = u.id
LEFT JOIN 
    likes l ON c.id = l.comment_id AND l.user_id = ? -- Add the current user's ID here
WHERE 
    c.product_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId,$productId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to fetch replies for a given comment ID
function getReplies($pdo, $commentId) {
    $replyQuery = "SELECT r.*, u.name AS user_name, u.photo AS user_photo
                   FROM replies r
                   JOIN users u ON r.user_id = u.id
                   WHERE r.comment_id = ?
                   ORDER BY r.reply_date ASC";
    $replyStmt = $pdo->prepare($replyQuery);
    $replyStmt->execute([$commentId]);
    return $replyStmt->fetchAll(PDO::FETCH_ASSOC);
}

date_default_timezone_set('Asia/Beirut'); // Or your desired timezone

// Fetch comments for the specific product
foreach ($comments as $comment): ?>
    <div class="comment-container">
        <div class="flex-shrink-0">
            <img
                src="/final/userprofile/<?php echo !empty($comment['user_photo']) ? htmlspecialchars(basename($comment['user_photo'])) : 'Unknown_person.jpg'; ?>"
                alt="<?php echo htmlspecialchars($comment['user_name']); ?>"
                class="w-12 h-12 rounded-full object-cover"
            >
        </div>

        <div class="flex-grow">
            <div class="flex items-center mb-2">
                <h4 class="font-semibold text-gray-800 mr-3">
                    <?php echo htmlspecialchars($comment['user_name']); ?>
                </h4>
                <span class="text-gray-500 text-sm">
                    <?php
                    // Format date relative to current time
                    $commentDate = new DateTime($comment['commentDate']);
                    $commentDate->setTimezone(new DateTimeZone('Asia/Beirut'));

                    // Get the current time in the same timezone
                    $now = new DateTime('now', new DateTimeZone('Asia/Beirut'));
                    $interval = $now->diff($commentDate);


                    if ($interval->y > 0) {
                        echo $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->m > 0) {
                        echo $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->d > 0) {
                        echo $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->h > 0) {
                        echo $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->i > 0) {
                        echo $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                    } else {
                        echo 'Just now';
                    }
               

                    ?>

                </span>
            </div>
            <?php
// Check like status
$isLiked = false;

if (isset($_SESSION['id']) && isset($comment['id'])) {
    $userId = $_SESSION['id'];
    $commentId = $comment['id'];

    $checkStmt = $pdo->prepare("SELECT is_liked FROM likes WHERE user_id = ? AND comment_id = ?");
    $checkStmt->execute([$userId, $commentId]);
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array
    
    

    if ($result) {
        $isLiked = (bool) $result['is_liked']; // Access the 'is_liked' column
    }
}
?>
            
            
           <div class="comment">
            
         <p class="text-gray-700 leading-relaxed">
            
            <?php echo htmlspecialchars($comment['comment']); ?>
            
            </p>
            
            
            
           <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
            
           <form action="actions/likeaction.php" method="post">
            
          <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
            
            <button class="hover:text-blue-600 transition" type="submit">
         
           <?php echo $isLiked ? 'Unlike' : 'Like'; ?>
            
          </button>
            </form>
            
            <span><?php echo $comment['like_count']; ?> like<?php if ($comment['like_count'] != 1) echo 's'; ?></span>
            
            
            
          <button class="reply-button hover:text-blue-600 transition">
            
           Reply
            
       </button>
       <?php if ($_SESSION['id'] === $comment['user_id']): ?>
    <form action="actions/delete_comment.php" method="post" style="display: inline;">
        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
        <button type="submit" class="hover:text-red-600 transition">Delete</button>
    </form>

    <button class="edit-comment-button hover:text-green-600 transition">Edit</button>
<?php endif; ?>
            
        </div>

                <div class="reply-form mt-4 hidden">
                    <form action="actions/replyaction.php" method="post">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                        <textarea name="reply_text" placeholder="Write your reply..."></textarea>
                        <button type="submit">Submit Reply</button>
                        <button type="button" class="cancel-reply-button">Cancel</button>
                    </form>
                </div>
                <?php if ($_SESSION['id'] === $comment['user_id']): ?>
    <form action="actions/edit_comment.php" method="post" class="edit-comment-form hidden mt-3">
        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
        <textarea name="comment_text"><?php echo htmlspecialchars($comment['comment']); ?></textarea>
        <button type="submit">Update</button>
        <button type="button" class="cancel-edit-comment-button">Cancel</button>
    </form>
<?php endif; ?>

                <div class="replies-container mt-2">
                    <?php $replies = getReplies($pdo, $comment['id']); ?>
                    <?php foreach ($replies as $reply): ?>
                        <div class="reply-container">
                            <div class="flex-shrink-0">
                                <img
                                    src="/final/userprofile/<?php echo !empty($reply['user_photo']) ? htmlspecialchars(basename($reply['user_photo'])) : 'Unknown_person.jpg'; ?>"
                                    alt="<?php echo htmlspecialchars($reply['user_name']); ?>"
                                    class="w-6 h-6 rounded-full object-cover"
                                >
                            </div>
                            <div class="flex-grow">
                                <div class="reply-header">
                                    <h6 class="font-semibold"><?php echo htmlspecialchars($reply['user_name']); ?></h6>
                                    <span class="text-sm">
                                        <?php
                                        $replyDate = new DateTime($reply['reply_date']);
                                        $replyDate->setTimezone(new DateTimeZone('Asia/Beirut'));
                                        echo $replyDate->format('Y-m-d H:i');
                                        ?>
                                    </span>
                                </div>
                                <p class="text-gray-700 leading-relaxed"><?php echo htmlspecialchars($reply['reply_text']); ?></p>
                                <?php if ($_SESSION['id'] === $reply['user_id']): ?>
    <div class="mt-1 text-sm flex gap-3">
        <form action="actions/delete_reply.php" method="post">
            <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
            <button type="submit" class="hover:text-red-600 transition">Delete</button>
        </form>
        <button class="edit-reply-button hover:text-green-600 transition">Edit</button>
    </div>

    <form action="actions/edit_reply.php" method="post" class="edit-reply-form hidden mt-2">
        <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
        <textarea name="reply_text"><?php echo htmlspecialchars($reply['reply_text']); ?></textarea>
        <button type="submit">Update</button>
        <button type="button" class="cancel-edit-reply-button">Cancel</button>
    </form>
<?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

<?php endforeach; ?>
    <script>
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('reply-button')) {
                const commentDiv = event.target.closest('.comment');
                const replyForm = commentDiv.querySelector('.reply-form');
                replyForm.classList.toggle('hidden');
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('cancel-reply-button')) {
                const commentDiv = event.target.closest('.comment');
                const replyForm = commentDiv.querySelector('.reply-form');
                replyForm.classList.add('hidden');
            }
        });
    </script>
   <script>
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-comment-button')) {
            const commentDiv = event.target.closest('.comment');
            const editForm = commentDiv.querySelector('.edit-comment-form');
            editForm.classList.toggle('hidden');
        }

        if (event.target.classList.contains('cancel-edit-comment-button')) {
            const commentDiv = event.target.closest('.comment');
            const editForm = commentDiv.querySelector('.edit-comment-form');
            editForm.classList.add('hidden');
        }

        if (event.target.classList.contains('edit-reply-button')) {
            const replyDiv = event.target.closest('.reply-container');
            const editForm = replyDiv.querySelector('.edit-reply-form');
            editForm.classList.toggle('hidden');
        }

        if (event.target.classList.contains('cancel-edit-reply-button')) {
            const replyDiv = event.target.closest('.reply-container');
            const editForm = replyDiv.querySelector('.edit-reply-form');
            editForm.classList.add('hidden');
        }
    });
</script>
    <script src="product.js"></script>
</body>
</html>