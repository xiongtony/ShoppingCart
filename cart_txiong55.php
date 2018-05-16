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

        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
        } else { // $_SESSION does not exist
            $cart = Array();
        }
        
        if (isset($_POST['remove'])) {
            $remove = $_POST['remove'];
        } else {
            $remove = Array();
        }
        
        $totalPrice = 0;
        $prodIDStr = "";
        $_SESSION['numItems'] = 0;
     
        
        foreach ($_POST as $productID => $quantity) {
            
            if (is_numeric($quantity) && $quantity > 0 && !isset($remove[$productID])) {
                
                $cart[$productID] = $quantity;

            } else if ($quantity == 0 || isset($remove[$productID])){ 
                
                unset($cart[$productID]);
            }
            
        }
        
        // Connect to DB server and select our DB
        require 'dbConnect.php';
        
        foreach ($cart as $productID => $quantity) {
            
            $_SESSION['numItems'] += $quantity;
            
            $prodIDStr .= "$productID,";
   
        }
        
        if (empty($cart)){
?>
        <h3>Your shopping cart is empty!</h3>
<?php
        } else {
            
            $prodIDStr = rtrim($prodIDStr, ",");
            
            try {
          
                
            $cartResult = $pdo->query("SELECT * FROM products WHERE prodid IN ($prodIDStr) ORDER BY category, prodid");
            
            
        } catch (PDOException $ex) {
            $error = "Error fetching product info: " . $ex->getMessage();
            include 'error.html.php';
            exit();
        }
?>
        
        <br><br>
        <form action="cart_txiong55.php" method="post">
            <table>
                <tr class="header">
                    <th>Remove</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Quantity</th>
                    <th style="background-color: #fff">&nbsp;</th>
                </tr>
<?php

        while ($row = $cartResult->fetch()) {
            
            $productID = strip_tags($row['prodid']);
            $qty = $cart[$productID];
            
            $imageLocation = htmlspecialchars(strip_tags($row['loc']));
            $description = htmlspecialchars(strip_tags($row['description']));
            $price = htmlspecialchars(strip_tags($row['price']));
            
            
            $subTotal = $price * $qty;
            $totalPrice += $subTotal;
            
            $price  = sprintf("%.2f", $price);
            
            $subTotal = sprintf("%.2f", $subTotal);

?>
                <tr>
                    <td><input type="checkbox" name="remove[<?=$productID?>]" value="1"></td>
                    <td><img src="<?=$imageLocation?>" alt="image of <?=$description?>"></td>
                    <td class="desc"><?=$description?></td>
                    <td class="price">$<?=$price?></td>
                    <td>$<?=$subTotal?></td>
                    <td class="qty">
                        <input type="text" name="<?=$productID?>" id="quantityForProduct<?=$productID?>" value="<?=$qty?>" size="3">
                    </td>
                </tr>        
<?php
            
        }
        
        $totalPrice = sprintf("%.2f", $totalPrice);
?>
                <tr>
                    <td colspan="5" style="text-align: right;">Total: $<?=$totalPrice?></td>
                    <td><?=$_SESSION['numItems']?></td>
                </tr>
                <tr>
                    
                    <td colspan="6" id="submitCell">
                        <input type="submit" name="checkout" value="Checkout">
                        <input type="submit" name="update" value="Update Cart">
                    </td>
                </tr>
<?php
        
        } // end cart not empty
        
        $_SESSION['cart'] = $cart;
?>
            </table>
        </form>
        <br>
        <a href="index.html">Continue Shopping</a>
    </body>
</html>
