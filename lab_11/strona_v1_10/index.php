<?php
  /**
   * Główny plik indeksowy.
   * Łączy konfigurację, logikę wyświetlania i szablon HTML.
   * Wersja: v1.8
   */

  // Raportowanie błędów - w produkcji wyłączamy NOTICE, w dev przydaje się ALL
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

  include('cfg.php');
  include('showpage.php');

  // Logika wyboru strony - zabezpieczenie przed pustym ID
  if(empty($_GET['idp'])) {
      $stronaId = 1;
  } else {
      // Rzutowanie na int zabezpiecza przed złośliwymi ciągami znaków w URL
      $stronaId = (int)$_GET['idp']; 
  }
?>

<!doctype html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="Projekt 1 - CMS Żółwie">
  <meta name="keywords" content="HTML5, CSS3, JavaScript, PHP">
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
            <input type="button" value="żółty" onclick="changeBackground('#FFF000')">
            <input type="button" value="czarny" onclick="changeBackground('#000000')">
            <input type="button" value="biały" onclick="changeBackground('#FFFFFF')">
            <input type="button" value="zielony" onclick="changeBackground('#00FF00')">
            <input type="button" value="niebieski" onclick="changeBackground('#0000FF')">
            <input type="button" value="pomarańczowy" onclick="changeBackground('#FF8000')">
            <input type="button" value="szary" onclick="changeBackground('#c0c0c0')">
            <input type="button" value="czerwony" onclick="changeBackground('#FF0000')">
        </div>
      </nav>
    </div>
  </header>

  <main class="container" role="main">
    <?php
      // Dynamiczne ładowanie treści z bazy
      echo PokazPodstrone($stronaId);
    ?>
  </main>

<?php
include('contact.php');

// Obsługa formularza kontaktowego
if (isset($_POST['wyslij_kontakt'])) {
    WyslijMailKontakt("admin@twojastrona.pl");
} else {
    echo PokazKontakt();
}

// Obsługa przypomnienia hasła
if (isset($_GET['action']) && $_GET['action'] == 'przypomnij_haslo') {
    PrzypomnijHaslo("admin@twojastrona.pl");
}
?>

  <footer class="site-footer" role="contentinfo">
    <div class="container">
      <div class="small">stopka</div>
      <a href="index.php?idp=7">Kontakt</a>
    </div>
  </footer>

  <script>
    $(document).ready(function() {
      // Animacja powiększania obrazków w rankingu
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

    // Animacja nagłówka
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