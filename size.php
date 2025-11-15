<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Size</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="size.css">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="footer.css">
    
    
</head>
<body>
<?php
   require "nav.php";

   ?>
    <div class="size">
        

    <h1>Size Guide</h1>
            <div class="modal-content">
                
                <h2>Clothing Size Chart</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Chest (inches)</th>
                            <th>Waist (inches)</th>
                            <th>Hip (inches)</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <tr>
                            <td>S</td>
                            <td>34-36</td>
                            <td>28-30</td>
                            <td>36-38</td>
                        </tr>
                        <tr>
                            <td>M</td>
                            <td>38-40</td>
                            <td>32-34</td>
                            <td>40-42</td>
                        </tr>
                        <tr>
                            <td>L</td>
                            <td>42-44</td>
                            <td>36-38</td>
                            <td>44-46</td>
                        </tr>
                        <tr>
                            <td>XL</td>
                            <td>46-48</td>
                            <td>40-42</td>
                            <td>48-50</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
  
        <?php
   require "footer.php";

   ?>

   <script src="nav.js"></script>
   <script src="index.js"></script>
 
   
</body>
</html>