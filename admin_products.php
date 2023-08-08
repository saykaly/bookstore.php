<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

if (isset($_POST['add_product'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $purchase_price = mysqli_real_escape_string($conn, $_POST['purchase_price']);
   $price_before = mysqli_real_escape_string($conn, $_POST['price_before']);
   $stock = $_POST['stock'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $discount_percentage = $_POST['discount_percentage'];

   $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('query failed');

   if (mysqli_num_rows($select_product_name) > 0) {
      $message[] = 'Product name already added';
   } else {
      if ($stock > 100) {
         $message[] = 'Maximum stock limit exceeded (maximum 100)';
      } else {
         $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name, purchase_price, price_before, stock, image, discount_percentage) VALUES('$name', '$purchase_price', '$price_before', '$stock', '$image', '$discount_percentage')") or die('query failed');

         if ($add_product_query) {
            if ($image_size > 2000000) {
               $message[] = 'Image size is too large';
            } else {
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'Product added successfully!';
            }
         } else {
            $message[] = 'Product could not be added!';
         }
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('uploaded_img/' . $fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_products.php');
}

if (isset($_POST['update_product'])) {
   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_purchase_price = $_POST['update_purchase_price'];
   $update_stock = $_POST['update_stock'];

   mysqli_query($conn, "UPDATE `products` SET name = '$update_name', purchase_price = '$update_purchase_price', stock = '$update_stock' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = 'uploaded_img/' . $update_image;
   $update_old_image = $_POST['update_old_image'];

   if (!empty($update_image)) {
      if ($update_image_size > 2000000) {
         $message[] = 'Image file size is too large';
      } else {
         mysqli_query($conn, "UPDATE `products` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('uploaded_img/' . $update_old_image);
      }
   }

   header('location: admin_products.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="admin_style.css">

   <style>
.price {
   margin-top: 20px;
   color: white;
   text-decoration: line-through;
}


.discounted-price {
   font-size: 20px;
   color: white;
   font-weight: bold;
}

.discount-percentage {
   font-size: 20px;
   color: white;
   margin-top: 5px;
}

   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">Shop Products</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Add Product</h3>
      <input type="text" name="name" class="box" placeholder="Enter product name" required>
      <input type="number" min="0" name="purchase_price" class="box" placeholder="Enter product purchase price" required>
      <input type="number" min="0" name="price_before" class="box" placeholder="Enter price before" required>
      <input type="number" min="0" max="100" name="stock" class="box" placeholder="Enter product stock" required>
      <input type="number" min="0" max="100" name="discount_percentage" class="box" placeholder="Enter discount percentage" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="Add Product" name="add_product" class="btn">
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->

<section class="show-products">
<div class="box-container">

<?php
   $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
   if (mysqli_num_rows($select_products) > 0) {
      while ($fetch_products = mysqli_fetch_assoc($select_products)) {
         $discounted_price = $fetch_products['purchase_price'] - ($fetch_products['purchase_price'] * $fetch_products['discount_percentage'] / 100);
?>
<div class="box">
   <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
   <div class="name"><?php echo $fetch_products['name']; ?></div>
   <div class="price">$<?php echo $fetch_products['purchase_price']; ?>/-</div>
   <div class="price-before">$<?php echo $fetch_products['price_before']; ?>/- (Price Before)</div>
   <div class="discount"><?php echo $fetch_products['discount_percentage']; ?>% Off</div> <!-- Display the discount percentage -->
   <div class="offer-price">$<?php echo $discounted_price; ?>/-</div> <!-- Display the discounted price -->
   <div class="stock">
      <?php if($fetch_products['stock'] > 9){ ?>
         <span class="stock" style="color: green;"><i class="fas fa-check"></i> in stock</span>
      <?php }elseif($fetch_products['stock'] == 0){ ?>
         <span class="stock" style="color: red;"><i class="fas fa-times"></i> out of stock</span>
      <?php }else{ ?>
         <span class="stock" style="color: red;">hurry, only <?= $fetch_products['stock']; ?> left</span>
      <?php } ?>
   </div>
   <?php if($fetch_products['stock'] != 0){ ?>
      <!-- Display additional options if the product is in stock -->
   <?php }; ?>
   <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Update</a>
   <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
</div>
<?php
      }
   } else {
      echo '<p class="empty">No products added yet!</p>';
   }
?>

</div>


</section>



<!-- custom admin js file link  -->
<script src="admin_script.js"></script>

</body>
</html>