<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'Odalar Müsait Değil';
   }else{
      $success_msg[] = 'Odalar Müsait';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'Seçtiğiniz tarihlerde müsait oda bulunmamaktadır!';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'Oda Şimdiden Rezerve Edildi!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'Rezervasyon Başarılı!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'Mesaj Zaten Gönderildi!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'Mesaj Başarıyla Gönderildi!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Blue Dreams Resort</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>Lüks Odalar</h3>
               <a href="#availability" class="btn">Oda Uygunluğunu Kontrol Et</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>Yiyecek ve İçecekler</h3>
               <a href="#reservation" class="btn">Rezervasyon Yap</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>Lüks Salonlar</h3>
               <a href="#contact" class="btn">Bizimle İletişime Geçin</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Giriş Tarihi <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Çıkış Tarihi <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Yetişkin <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 Yetişkin</option>
               <option value="2">2 Yetişkin</option>
               <option value="3">3 Yetişkin</option>
               <option value="4">4 Yetişkin</option>
               <option value="5">5 Yetişkin</option>
               <option value="6">6 Yetişkin</option>
            </select>
         </div>
         <div class="box">
            <p>Çocuk <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 Çocuk</option>
               <option value="1">1 Çocuk</option>
               <option value="2">2 Çocuk</option>
               <option value="3">3 Çocuk</option>
               <option value="4">4 Çocuk</option>
               <option value="5">5 Çocuk</option>
               <option value="6">6 Çocuk</option>
            </select>
         </div>
         <div class="box">
            <p>Oda Tipi <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">Tek Kişilik Oda</option>
               <option value="2">İki Kişilik Oda</option>
               <option value="3">Süit Oda</option>
               <option value="4">Vip Oda</option>
               <option value="5">Aile Odası</option>
            </select>
         </div>
      </div>
      <input type="submit" value="tarihi kontrol et" name="check" class="btn">
   </form>++

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about-img-1.jpg" alt="">
      </div>
      <div class="content">
         <h3>Otelimiz</h3>
         <p>Blue Dreams Resort,  5 adet yüzme havuzu ve 1 adet kaydıraklı havuzu ile her isteğe hitap ediyor. Tatilde dahi sağlıklı yaşam ve spor alışkanlıklarından ödün vermek istemeyenler için Tenis kortu, Su Sporları, gün içerisinde farklı spor aktiviteleri ve sonrasında rahatlamanızı sağlayacak Spa hizmetleri ile kendinizi şımartabilirsiniz.</p>
         <a href="#reservation" class="btn">Şimdi Rezervasyon Yap</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about-img-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Yeme & İçme</h3>
         <p>Blue Dreams Resort’un Ultra Herşey Dahil konseptinde tatilinize eşlik edecek muhteşem kokteyller ve dünya mutfaklarından eşsiz lezzetler sizleri bekliyor, zengin açık büfemizin yanı sıra 3 adet a’la carte restoranımız ve 2 adet snack restoranımızda her damak zevkine uygun lezzetler bulacaksınız.</p>
         <a href="#contact" class="btn">Bizimle İletişime Geçin</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about-img-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>Blue Dreams Resort</h3>
         <p>Bodrum’un en güzel koylarından biri olan Torba Zeytinlikahve’de 55.000 m2 alan üzerinde doğa ile iç içe ve Bodrum mimarisine özgü denize sıfır tesisimizde tatiliniz için hayal ettiğiniz her şeyi bulacaksınız, serinlemek ve dinlenmek için 700 m uzunluğunda kumsal, iskeleleri ve özel Cabana alanları bulunan tesisimiz farklı gündüz ve akşam aktiviteleri ile eğlenceyi de sağlıyor.
Tesisimizde başlayan eğlenceyi gecenin ilerleyen saatlerine taşımak isterseniz 10 km uzaklıkta olan Bodrum şehir merkezine araç ile 10 dakikada ulaşabiliyorsunuz. Eşsiz konumu ile ulaşımı kolay olan otelimiz Milas-Bodrum Havaalanına ise sadece 25 km uzaklıkta.</p>
         <a href="#availability" class="btn">Oda Uygunluğunu Kontrol Et</a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>Yeme & İçme</h3>
         <p>Tatilinize eşlik edecek muhteşem kokteyller ve dünya mutfaklarından eşsiz lezzetler sizleri bekliyor..</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>Manzara</h3>
         <p>Bodrum'un muhteşem manzarası sizleri bekliyor..</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>Sahil</h3>
         <p>Sahil hemen yanı başınızda sizleri bekliyor..</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>Dekorasyon</h3>
         <p>Modern Türk / Akdeniz mimarisiyle dekore edilmiş otelimiz sizleri bekliyor..</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>Yüzme</h3>
         <p>Sahil ve otelimizin havuzu keyifli bir gün için sizi bekliyor..</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>Plaj</h3>
         <p>Plaj keyfi sizleri bekliyor..</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>Rezervasyon Yap</h3>
      <div class="flex">
         <div class="box">
            <p>Adınız Soyadınız <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="adınızı ve soyadınızı giriniz" class="input">
         </div>
         <div class="box">
            <p>E-Mail Adresiniz <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="e-mail adresinizi giriniz" class="input">
         </div>
         <div class="box">
            <p>Telefon Numaranız <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="telefon numaranızı giriniz" class="input">
         </div>
         <div class="box">
            <p>Odalar <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>Tek Kişilik Oda</option>
               <option value="2">İki Kişilik Oda</option>
               <option value="3">Süit Oda</option>
               <option value="4">Vip Oda</option>
               <option value="5">Aile Odası</option>
            </select>
         </div>
         <div class="box">
            <p>Giriş Tarihi <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Çıkış Tarihi <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Yetişkin <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 Yetişkin</option>
               <option value="2">2 Yetişkin</option>
               <option value="3">3 Yetişkin</option>
               <option value="4">4 Yetişkin</option>
               <option value="5">5 Yetişkin</option>
               <option value="6">6 Yetişkin</option>
            </select>
         </div>
         <div class="box">
            <p>Çocuk <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 Çocuk</option>
               <option value="1">1 Çocuk</option>
               <option value="2">2 Çocuk</option>
               <option value="3">3 Çocuk</option>
               <option value="4">4 Çocuk</option>
               <option value="5">5 Çocuk</option>
               <option value="6">6 Çocuk</option>
            </select>
         </div>
      </div>
      <input type="submit" value="şimdi rezervasyon yap" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>bize mesaj gönderin</h3>
         <input type="text" name="name" required maxlength="50" placeholder="adınızı ve soyadınızı giriniz" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="e-mail adresinizi giriniz" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="telefon numaranızı giriniz" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="mesajınızı yazınız" cols="30" rows="10"></textarea>
         <input type="submit" value="mesajı gönder" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">Sıkça sorulan sorular</h3>
         <div class="box active">
            <h3>Nasıl iptal edilir?</h3>
            <p>Dilerseniz otelimizi arayarak, dilerseniz sistem üzerinden rezervasyonunuzu iptal edebilirsiniz.</p>
         </div>
         <div class="box">
            <h3>Boş yer var mı?</h3>
            <p>Sistem üzerinde dilediğiniz tarihler arasında boş oda olup olmadığını sorgulayabilirsiniz.</p>
         </div>
         <div class="box">
            <h3>Ödeme yöntemleri nelerdir?</h3>
            <p>Sistem üzerinde ödemenizi başarıyla tamamlayabilir, rezervasyonunuzu yapabilirsiniz.</p>
         </div>
         <div class="box">
            <h3>Kupon kodları nasıl kullanılır?</h3>
            <p>Kupon kodları kişiye özeldir. Otelimizi ziyaret ettiğinizde resepsiyonda kupon kodunuzu tanımlatabilirsiniz.</p>
         </div>
         <div class="box">
            <h3>Servis saatleri ne zamandır?</h3>
            <p>Otelimizin kahvaltı saatleri 07.00 - 11.00 saatleri arasındadır. Öğle yemeği saatleri 12.00 - 16.00 saatleri arasındadır. Akşam yemeği saatleri 17.00 - 22.00 saatleri arasındadır.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>Mateusz B</h3>
            <p>Szczera opinia o hotelu
Witam, staram się zawsze po pobycie w jakimkolwiek hotelu o szczerą opinie by ułatwić innym wybór. Przedstawię to w postaci plusów i minusów. 
Plusy:
super baseny a szczególnie główny z widokiem na morze
Idealny element allinclusive ultra, który idealnie się sprawdza w kwestii wszelakiego rodzaju drinków , tu tez się należy wielka pochwała barmanów którzy obsługiwali wieczorem. Profesjonalne podejście do każdego, pasja w tym co robi i zawsze miła rozmowa.
Jedzenie bardzo dobre i dość solidny wybór, ja osobiście wiele rzeczy nie lubię a zawsze znalazłem dla siebie , bardzo przyjemna obsługa oraz jeden z kelnerów , który mówił w j. Polskim i zawsze grzecznie nas obsługiwał. Nazywają go Mariusz ( nauczyciel matematyki ) oraz wielki plus dla całej reszty.
Fajny dodatek w kwestii aquaparku, niby tylko kilka zjeżdżali ale jak dla mnie fajny bajer.Fajna Silownia z wieloma sprzętami !</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>Azize G</h3>
            <p>Harika otel
Oteli çok beğendik gerek konumu olsun gerek yemekleri personelin gulryuzlullugu olsun çok iyi
Yemeklerin cesitliligi oldukça güzel ve yeterli
Odaların temizliği gayet başarılı
Havuzlar temiz ve bakımlı
Animasyon aktiviteleri gayet eğlenceli çok memnun kaldık hepsine tek tek teşekkür ederiz
Hüseyin asrın pelin özlem sıla aziz hepiniz pırlanta gibisiniz iyimi geldik tanıdık sizi
Ama ayrıca zumba her ne kadar katilamasamda izlemesi çok keyifliydi
Harikasinizzzzzzz.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>Marzena P</h3>
            <p>Amazing holidays with family
The hotel location is amazing. Everyday day I was watching my sea view morning time when wake up. Bartenders team, waiters and SPA team was wonderfull and very helpful. Staff speaking bacis polish which was a nice surprise for us even english language is not a problem for us.
A small disadvatnage was the room cleanless but manager of the hotel was very helpfull and ready to assist any time for the customers.
He was making the drinks himself and serving the the dishes as well.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>Melisa T</h3>
            <p>Muhteşem bir tatil
bir otel faciasından sonra bize cenneti yaşatan bir otel. Öncelikle bize yaşattığınız bu deneyim için çok teşekkür ederiz. Kapıdan içeri girdiğimiz anda bizi özel hissettirdiler. 600 kişilik 70 dönümlük bir otelde öyle özel hissettiriyorlar ki inanamazsınız. Özellikle müdür Burak beye çok çok teşekkür ederiz. Bu kadar ilgili, her alanda aktif, her işin başında olan böylesine bir müdür çok zor bulunur. Bence otelin en büyük şansı Burak Bey ve çalışanları. Otelin her köşesi geceleri resmen çamaşır suyuyla yıkanıyor, heryer ve çalışanlar o kadar temiz ki… fiyat - performans olarak mukayese edeceksem; kesinlikle bütün beklentilerimizi karşıladı. Sadece tek eleştirim sahil kısmında kalitesiz kağıt bardaklardan alkol servis ediliyor. Bunun yerine kaliteli mika bardak veya kadehler ile servis edilebilir. Tekrar tüm çalışanlara ve Burak beye ayrı ayrı çok teşekkür ediyorum..</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>Berkay A</h3>
            <p>Vakantie
Hotel is goed
Enkel het eten valt iets tegen
Animatie is leuk vooral özlem, huseyin en asrin ve pelin hebben onze vakantie leuker gemaakt. Mannen van de bars zijn goed. En we hebben een gratis upgrade gekregen naar een luxe kamer. Receptie super vriendelijk.
Top vakantie</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>Eda Güneri B</h3>
            <p>Ailece sevdiğimiz otel
Biz ailece bir haftamızı bu muhteşem havuzlu otele ayırıyoruz.Fakat bu sene yaşadığımız üzücü bir durumu paylaşmak istiyorum.. Restaurant müd denilen Cemal bey yüzünden akşamımızı çok kötü geçirip Bodruma indik Bu akşam yoğunluktan dolayı olmayan masayı ailece beklemeye koyulmuştuk bir masa boştu sorduk oraya patron gelicek dendi Eşimde hatta garsonlara şakayla patron müşterisine verir masayı dedi sonrasında beklemeye devam ederken masaya gelen yeni müşteriler oturdu Bizde garsonlara çocuklar biz sorup bekliyorduk ya başkalarını oturttunuz derken garsonlarda ellerinden geldiğince anlayışla açıklamalar yaparken gelen müdürleri Cemal bey evet ne oldu boştu ayırdık şimdide verdik deyince şok olduk hatta eşimde yemeden çıktık böyle bir cevap başkası olsa kavgaya kadar giderdi.Otelde eşim gittikten sonra devam etmeyecektik ama yıllardır rezervasyonlarımız ile ilgilenen Rez Md Fatih Beyin ilgisi upgrade jesti için kalmaya devam ettik Otele en büyük katkıyı sağlayan sorumluluk sahibi Fatih Beye çok tşk ederiz.</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->




<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>