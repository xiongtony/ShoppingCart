<?php
// If a session is not already in progress, start one...
if (!session_id()) {
    session_start();
}


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shopping Cart Catalog - Education Project Only</title>
        <link href="css/minismall.css" rel="stylesheet">
    </head>
    <body>
        <h2>Product Catalog - Education Project Only</h2>
        
        <?php
        
        // If a session variable named 'numItems' has never been
        // set, then initialize to 0.
        if (!isset($_SESSION['numItems'])) {
            $_SESSION['numItems'] = 0;
        }
        
        ?>
        <p>Your shopping cart contains <?=$_SESSION['numItems']?> item(s).</p>
        <a href="cart_txiong55.php">View your cart</a> |
        <a href="index.html">Back to product categories</a>
        
        <?php
        
        // Connect to DB server and select our DB
        require 'dbConnect.php';
        
        //
        // Query the DB for all category ids
        //
        try {
            
            $categoryResult = $pdo->query("SELECT catid FROM categories");
            
        } catch (PDOException $ex) {
            $error = "Error fetching category info: " . $ex->getMessage();
            include 'error.html.php';
            exit();
        }
        
        $catIDs = Array();  // empty array
        $ctr = 0;
        
        // Step through the result set (PDOStatement object)
        // and store each category id in our $catIDs array
        while ($row = $categoryResult->fetch()) {
            
            $catIDs[$ctr] = $row['catid'];
            $ctr++;
            
        }
        
        //
        // Check if incoming category is valid
        //
        if (isset($_GET['cat']) && in_array($_GET['cat'], $catIDs)) {
            
            //echo "<h3>Category $_GET[cat] is valid</h3>";
            $cat = $_GET['cat'];
            
        } else {
            //echo "<h3>Category $_GET[cat] is not valid</h3>";
            $cat = 1;
        }
        
        $_SESSION['cat'] = $cat; // remember the chosen category id
        
        //
        // Query for all products in the chosen category and
        // display them in a table
        //
        try {
            
            $itemsResult = $pdo->query("SELECT * FROM products WHERE products.category=$cat");
            
        } catch (PDOException $ex) {
            $error = "Error fetching product info: " . $ex->getMessage();
            include 'error.html.php';
            exit();
        }
        
        // Start our category's product table by generating the
        // first (header) row
        
        
        ?>
        
        <br><br>
        <form action="cart_txiong55.php" method="post">
            <table>
                <tr class="header">
                    <th>Image</th>
                    <th>Description</th>
                    <th>Price - USD</th>
                    <th style="background-color: #fff">&nbsp;</th>
                </tr>
                
        <?php
        //
        // Step through result set of products in this category
        // and display each product and its related info in its
        // own table row
        //
        while ($row = $itemsResult->fetch()) {
            
            // Convert any special HTML characters to their
            // HTML entites. Example: & --> &amp;
            // 
            // Also, strip out any HTML tags found in the data.
            $imageLocation = htmlspecialchars(strip_tags($row['loc']));
            $description = htmlspecialchars(strip_tags($row['description']));
            $price = htmlspecialchars(strip_tags($row['price']));
            
            $price = "\$" . number_format($price, 2);
            
            $productID = strip_tags($row['prodid']);
            
            //
            // Set $qty to contain what is in our session cart array
            // variable. If your session cart array element of
            // $_SESSION for this product is empty, set the $qty
            // variable to its default value of 0.
            //
            if (isset($_SESSION['cart'][$productID])) {
                $qty = $_SESSION['cart'][$productID];
            } else { // $_SESSION['cart'][$productID] does not exist
                $qty = 0;
            }
            
            // Build and display table row for this product
            //
            echo <<<TABLEROW
                <tr>
                    <td><img src="$imageLocation" alt="image of $description"></td>
                    <td class="desc">$description</td>
                    <td class="price">$price</td>
                    <td class="qty">
                        <label for="quantityForProduct$productID">Qty</label>
                        <input type="text" name="$productID" id="quantityForProduct$productID" value="$qty" size="3">
                    </td>
                </tr>        
TABLEROW;
            
        } // end while another row in our product result set
        
        ?>
                
                <tr>
                    <td colspan="4" id="submitCell">
                        <input type="submit" name="addCart" value="Add Items to Cart">
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <a href="cart_txiong55.php">View your cart</a> |
        <a href="index.html">Back to product categories</a>
    </body>
</html>
