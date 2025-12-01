<?php
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

  include('cfg.php');
  include('showpage.php');

  if(empty($_GET['idp'])) {
      $stronaId = 1;
  } else {
      $stronaId = $_GET['idp'];
  }
?>

<!doctype html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="Projekt 1">
  <meta name="keywords" content="HTML5, CSS3, JavaScript">
  <meta name="author" content="Piotr Ostaszewski">
  <title>Żółwie CMS</title>
  
  <link rel="stylesheet" href="style/strona.css">
  <link rel="stylesheet" href="style.css" />
  <script src="style/timedate.js" type="text/javascript"></script>
  <script src="style/kolorujtlo.js" type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="home" onload="startclock()">

  <header class="site-header" role="banner">
    <div class="header-inner">
      
      <div class="header_time">
        <div id="data"></div>
        <div id="zegarek"></div>
      </div>

      <a class="logo-link" href="index.php?idp=1"><img class="logo" src="images/logo.jpg" alt="Logo"></a>
      <div class="site-title">Żółwie</div>
      
      <nav aria-label="Główne menu" class="main-nav" role="navigation">
        <a href="index.php?idp=1">Strona główna</a>
        <a href="index.php?idp=5">Ranking żółwii</a>
        <a href="index.php?idp=3">Prehistoryczne żółwie</a>
        <a href="index.php?idp=4">Tabela porównań</a>
        <a href="index.php?idp=2">O rankingu</a>
        <a href="index.php?idp=6">Filmy</a>
        <a href="admin.php">Panel Admina</a>
        
        <div class="kolory">
            <INPUT TYPE="button" VALUE="żółty" ONCLICK="changeBackground('#FFF000')">
            <INPUT TYPE="button" VALUE="czarny" ONCLICK="changeBackground('#000000')">
            <INPUT TYPE="button" VALUE="biały" ONCLICK="changeBackground('#FFFFFF')">
            <INPUT TYPE="button" VALUE="zielony" ONCLICK="changeBackground('#00FF00')">
            <INPUT TYPE="button" VALUE="niebieski" ONCLICK="changeBackground('#0000FF')">
            <INPUT TYPE="button" VALUE="pomarańczowy" ONCLICK="changeBackground('#FF8000')">
            <INPUT TYPE="button" VALUE="szary" ONCLICK="changeBackground('#c0c0c0')">
            <INPUT TYPE="button" VALUE="czerwony" ONCLICK="changeBackground('#FF0000')">
        </div>
      </nav>
    </div>
  </header>

  <main class="container" role="main">
    <?php
      echo PokazPodstrone($stronaId);
    ?>
  </main>

<?php
include('contact.php');

if (isset($_POST['wyslij_kontakt'])) {
    WyslijMailKontakt("admin@twojastrona.pl");
} else {
    echo PokazKontakt();
}

if (isset($_GET['action']) && $_GET['action'] == 'przypomnij_haslo') {
    PrzypomnijHaslo("admin@twojastrona.pl");
}
?>

  <footer class="site-footer" role="contentinfo">
    <div class="container">
      <div class="small">stopka</div>
      <div href="index.php?idp=7">Kontakt</div>
    </div>
  </footer>

  <script>
    $(document).ready(function() {
      $(".ranking-item img").on({
        "mouseover": function() {
          $(this).css({
            "z-index": "10",
            "position": "relative"
          }).animate({
            width: $(this).width() * 1.8
          }, 400);
        },
        "mouseout": function() {
          $(this).animate({
            width: $(this).width() / 1.8
          }, 400, function() {
            $(this).css("z-index", "1");
          });
        }
      });
    });
  </script>
  <script>
    $("#intro-h2").on("click", function(){
        $(this).animate({
            width: "500px",
            opacity: 0.4,
            fontSize: "3em",
            borderwidth: "10px"
        }, 1500);
    });
  </script>
<?php
  $nr_indeksu = '175316';
  $nrGrupy = 'ISI2';
  echo 'Piotr Ostaszewski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
?>

</body>
</html>