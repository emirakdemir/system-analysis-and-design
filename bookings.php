<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['cancel'])){

   $booking_id = $_POST['booking_id'];
   $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);

   $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_booking->execute([$booking_id]);

   if($verify_booking->rowCount() > 0){
      $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_booking->execute([$booking_id]);
      $success_msg[] = 'rezervasyon başarıyla iptal edildi!';
   }else{
      $warning_msg[] = 'rezervasyon zaten iptal edilmiş!';
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Rezervasyonlar</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- booking section starts  -->

<section class="bookings">

   <h1 class="heading">rezervasyonlarım</h1>

   <div class="box-container">

   <?php
      $select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ?");
      $select_bookings->execute([$user_id]);
      if($select_bookings->rowCount() > 0){
         while($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>Ad Soyad : <span><?= $fetch_booking['name']; ?></span></p>
      <p>Email : <span><?= $fetch_booking['email']; ?></span></p>
      <p>Telefon Numarası : <span><?= $fetch_booking['number']; ?></span></p>
      <p>Giriş Tarihi : <span><?= $fetch_booking['check_in']; ?></span></p>
      <p>Çıkış Tarihi : <span><?= $fetch_booking['check_out']; ?></span></p>
      <p>Odalar : <span><?= $fetch_booking['rooms']; ?></span></p>
      <p>Yetişkin Sayısı : <span><?= $fetch_booking['adults']; ?></span></p>
      <p>Çocuk Sayısı : <span><?= $fetch_booking['childs']; ?></span></p>
      <p>Rezervasyon id: <span><?= $fetch_booking['booking_id']; ?></span></p>
      <form action="" method="POST">
         <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
         <input type="submit" value="rezervasyonu İptal et" name="cancel" class="btn" onclick="return confirm('Rezervasyonu İptal Etmek İstiyor musunuz?');">
      </form>
   </div>
   <?php
    }
   }else{
   ?>   
   <div class="box" style="text-align: center;">
      <p style="padding-bottom: .5rem; text-transform:capitalize;">Rezervasyon Bulunamadı!!</p>
      <a href="index.php#reservation" class="btn">Yeni Rezervasyon</a>
   </div>
   <?php
   }
   ?>
   </div>

</section>

<!-- booking section ends -->


























<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>