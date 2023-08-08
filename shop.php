<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

if (isset($_POST['add_to_cart'])) {
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_stock = $_POST['product_stock'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->bind_param('si', $product_name, $user_id);
   $check_cart_numbers->execute();
   $check_result = $check_cart_numbers->get_result();
   if ($check_result->num_rows > 0) {
      $message[] = 'Product already added to cart!';

   } else {
      $update_stock_query = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE name = ?");
      $update_stock_query->bind_param('is', $product_quantity, $product_name);
      $update_stock_query->execute();

      if ($update_stock_query->affected_rows > 0) {
         $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, name, price, stock, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
         $insert_cart->bind_param('issiis', $user_id, $product_name, $product_price, $product_stock, $product_quantity, $product_image);
         $insert_cart->execute();

         $message[] = 'Product added to cart!';
      } else {
         $message[] = 'Failed to update stock!';
      }
   }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="style.css">
   <style>
.price {
   margin-top: 20px;
   color: white;
}

.original-price {
   font-size: 20px;
   text-decoration: line-through;
   color: white;
}

.discounted-price {
   font-size: 20px;
   color: white;
   font-weight: bold;
}

.discount-percentage {
   font-size: 16px;
   color: white;
   margin-top: 5px;
}

   </style>

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>Our Shop</h3>
      <p><a href="home.php">Home</a> / Shop</p>
   </div>

   <section class="products">

      <h1 class="title">Latest Products</h1>

      <div class="box-container">
      <?php
$select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
if (mysqli_num_rows($select_products) > 0) {
   while ($fetch_products = mysqli_fetch_assoc($select_products)) {
      $discounted_price = $fetch_products['purchase_price'] - ($fetch_products['purchase_price'] * $fetch_products['discount_percentage'] / 100);
?>
      <div class="box">
         <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <div class="price">
            <?php if ($fetch_products['discount_percentage'] > 0) { ?>
               <span class="original-price">$<?php echo $fetch_products['purchase_price']; ?>/-</span><br>
               <span class="discounted-price">$<?php echo $discounted_price; ?>/-</span>
               <span class="discount-percentage"><?php echo $fetch_products['discount_percentage']; ?>% Off</span>
            <?php } else { ?>
               <span class="normal-price">$<?php echo $fetch_products['purchase_price']; ?>/-</span>
            <?php } ?>
         </div>
         <div class="stock">
            <?php if ($fetch_products['stock'] > 9) { ?>
               <span style="color: green;"><i class="fas fa-check"></i> In stock</span>
            <?php } elseif ($fetch_products['stock'] == 0) { ?>
               <span style="color: red;"><i class="fas fa-times"></i> Out of stock</span>
            <?php } else { ?>
               <span style="color: red;">Hurry, only <?php echo $fetch_products['stock']; ?> left</span>
            <?php } ?>
         </div>
         <?php if ($fetch_products['stock'] != 0) { ?>
            <form action="" method="post">
               <?php if ($fetch_products['discount_percentage'] > 0) { ?>
                  <input type="hidden" name="product_price" value="<?php echo $discounted_price; ?>">
               <?php } else { ?>
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['purchase_price']; ?>">
               <?php } ?>
               <input type="number" min="1" name="product_quantity" value="1" class="qty">
               <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
               <input type="hidden" name="product_stock" value="<?php echo $fetch_products['stock']; ?>">
               <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
               <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
            </form>
         <?php } ?>
      </div>
<?php
   }
} else {
   echo '<p class="empty">No products added yet!</p>';
}
?>



      </div>

   </section>

   <?php include 'footer.php'; ?>

   <!-- Custom JS file link -->
   <script src="script.js"></script>

</body>

</html>
