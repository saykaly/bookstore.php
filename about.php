<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>






   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css">
   <!-- custom css file link  -->
  
    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">
   <link rel="stylesheet" href=" admin_style.css">
   
   
  
  
</head>
<body>
   


<?php include 'header.php'; ?>
<style>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.download-link {
  padding: 10px 20px;
  background-color: #337ab7;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
}

.download-link:hover {
  background-color: #23527c;
}

.download-link:active {
  background-color: #1d466a;
}

.center-link {
  text-align: center;
}

.center-link a {
  font-size: 24px; /* Adjust the font size as desired */
}

</style>


  

    


<section>

      

            <div class="main-img">
                <img src="./image/table.png" alt="">
            </div>
        </div>
        </section>
        <div class="center-link">
  <a href="download.php?file=book.pdf">Download pdf </a>
</div>


<div class="heading">
   <h3>about us</h3>
   <p> <a href="about.php">about</a> / about </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="image/about-img (1).jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet voluptatibus aut hic molestias, reiciendis natus fuga, cumque excepturi veniam ratione iure. Excepturi fugiat placeat iusto facere id officia assumenda temporibus?</p>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Impedit quos enim minima ipsa dicta officia corporis ratione saepe sed adipisci?</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>

</section>


    

        
    <?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="script.js"></script>



</body>
</html>



