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

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('Query failed');

    if (mysqli_num_rows($check_cart_numbers) > 0) {
        $message[] = 'Already added to cart!';
    } else {
        $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, name, price, stock, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_cart->bind_param("isdiis", $user_id, $product_name, $product_price, $product_stock, $product_quantity, $product_image);
        $insert_cart->execute();
        $message[] = 'Product added to cart!';
    }

    $total_stock = ($product_stock - $product_quantity);

    if ($product_quantity > $product_stock) {
        $warning_msg[] = 'Only ' . $product_stock . ' stock is left';
    } else {
        $update_stock = $conn->prepare("UPDATE `products` SET stock = ? WHERE name = ?");
        $update_stock->bind_param("is", $total_stock, $product_name);
        $update_stock->execute();
        $success_msg[] = 'Added to cart!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Page</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Search Page</h3>
        <p><a href="home.php">Home</a> / Search</p>
    </div>

    <section class="search-form">
        <form action="" method="post">
            <input type="text" name="search" placeholder="Search products..." class="box">
            <input type="submit" name="submit" value="Search" class="btn">
        </form>
    </section>

    <section class="products" style="padding-top: 0;">

        <div class="box-container">
            <?php
            if (isset($_POST['submit'])) {
                $search_item = $_POST['search'];
                $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search_item%'") or die('Query failed');
                if (mysqli_num_rows($select_products) > 0) {
                    while ($fetch_product = mysqli_fetch_assoc($select_products)) {
            ?>
                        <div class="box">
                            <img class="image" src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="">
                            <div class="name"><?php echo $fetch_product['name']; ?></div>
                            <div class="price">
                                <?php if ($fetch_product['discount_percentage'] > 0) { ?>
                                    <span class="original-price">$<?php echo $fetch_product['purchase_price']; ?>/-</span><br>
                                    <?php
                                    $discounted_price = $fetch_product['purchase_price'] - ($fetch_product['purchase_price'] * $fetch_product['discount_percentage'] / 100);
                                    ?>
                                    <span class="discounted-price">$<?php echo $discounted_price; ?>/-</span>
                                    <span class="discount-percentage"><?php echo $fetch_product['discount_percentage']; ?>% Off</span>
                                <?php } else { ?>
                                    <span class="normal-price">$<?php echo $fetch_product['purchase_price']; ?>/-</span>
                                <?php } ?>
                            </div>
                            <div class="stock">
                                <?php if ($fetch_product['stock'] > 9) { ?>
                                    <span style="color: green;"><i class="fas fa-check"></i> In stock</span>
                                <?php } elseif ($fetch_product['stock'] == 0) { ?>
                                    <span style="color: red;"><i class="fas fa-times"></i> Out of stock</span>
                                <?php } else { ?>
                                    <span style="color: red;">Hurry, only <?php echo $fetch_product['stock']; ?> left</span>
                                <?php } ?>
                            </div>
                            <?php if ($fetch_product['stock'] != 0) { ?>
                                <form action="" method="post">
                                    <?php if ($fetch_product['discount_percentage'] > 0) { ?>
                                        <input type="hidden" name="product_price" value="<?php echo $discounted_price; ?>">
                                    <?php } else { ?>
                                        <input type="hidden" name="product_price" value="<?php echo $fetch_product['purchase_price']; ?>">
                                    <?php } ?>
                                    <input type="number" class="qty" name="product_quantity" min="1" value="1">
                                    <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                                    <input type="hidden" name="product_price" value="<?php echo $fetch_product['purchase_price']; ?>">
                                    <input type="hidden" name="product_stock" value="<?php echo $fetch_product['stock']; ?>">
                                    <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                                    <input type="submit" class="btn" value="Add to Cart" name="add_to_cart">
                                </form>
                            <?php } ?>
                        </div>
            <?php
                    }
                } else {
                    echo '<p class="empty">No result found!</p>';
                }
            } else {
                echo '<p class="empty">Search something!</p>';
            }

            ?>
        </div>

    </section>

    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="script.js"></script>

</body>

</html>
