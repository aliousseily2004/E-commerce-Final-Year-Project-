<?php
require "connection.php";
session_start();

if (isset($_GET['comment_id'])) {
    $comment_id = $_GET['comment_id'];

    try {
        // Prepare the SQL statement to fetch replies for the given comment ID, ordered by reply date
        $stmt = $pdo->prepare("SELECT r.id, r.user_id, u.name AS user_name, r.reply_text, r.reply_date
                                FROM replies r
                                JOIN users u ON r.user_id = u.id
                                WHERE r.comment_id = :comment_id
                                ORDER BY r.reply_date DESC");

        // Bind the comment ID parameter
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch all replies as an associative array
        $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if there are any replies
        if ($replies) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>View Replies</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        background-color: #f9f9f9;
                    }

                    h2 {
                        color: #333;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                        background-color: #fff;
                    }

                    th, td {
                        border: 1px solid #ddd;
                        padding: 10px;
                        text-align: left;
                    }

                    th {
                        background-color: #f2f2f2;
                    }

                    tr:hover {
                        background-color: #f1f1f1;
                    }

                    a {
                        display: inline-block;
                        margin-top: 20px;
                        padding: 10px 15px;
                        background-color: #007bff;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                    }

                    a:hover {
                        background-color: #0056b3;
                    }
                </style>
            </head>
            <body>
            <section id="repliesSection">
                <h2>Replies for Comment ID: <?php echo htmlspecialchars($comment_id); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Reply Text</th>
                            <th>Reply Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through the replies and display them
                        foreach ($replies as $reply) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($reply['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($reply['user_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($reply['reply_text']) . "</td>";
                            echo "<td>" . htmlspecialchars($reply['reply_date']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <br>
                <a href="javascript:history.back()">Back to Comments</a>
            </section>
            </body>
            </html>
            <?php
        } else {
            // Display a message if there are no replies
            echo "<p>No replies for this comment yet.</p>";
            echo "<br>";
            echo "<a href='javascript:history.back()'>Back to Comments</a>";
        }
    } catch (PDOException $e) {
        // Handle any database errors
        echo "Error fetching replies: " . htmlspecialchars($e->getMessage());
    }
} else {
    // If comment_id is not provided, redirect or display an error message
    echo "Invalid request. No comment ID provided.";
    echo "<br>";
    echo "<a href='javascript:history.back()'>Back to Comments</a>"; // Added Back button
}
?>