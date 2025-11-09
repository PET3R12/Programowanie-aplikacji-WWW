<?php
  include "cfg.php";
  include "showpage.php";
  $id_strony = 2;
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
  if($_GET['idp'] =='')$id_strony = 2;
  if($_GET['idp'] =='glowna')$id_strony = 2;
  if($_GET['idp'] =='ranking_zolwi')$id_strony = 5;
  if($_GET['idp'] =='orankingu')$id_strony = 3;
  if($_GET['idp'] =='prehistoryczne')$id_strony = 4;
  if($_GET['idp'] =='tabela')$id_strony = 6;
  if($_GET['idp'] =='filmy')$id_strony = 1;

  $strona = PokazPodstrone($id_strony, $link);
?>

<!doctype html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="Projekt 1">
  <meta name="keywords" content="HTML5, CSS3, JavaScript">
  <meta name="author" content="Piotr Ostaszewski">
  <title>Żółwie</title>
  <link rel="stylesheet" href="style/strona.css">
  <script src="style/timedate.js" type="text/javascript"></script>
  <script src="style//kolorujtlo.js" type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="home" onload="startclock()">
  <header class="site-header" role="banner">
    <div class="header-inner">
      <div class="header_time">
        <div id="data"></div>
        <div id="zegarek"></div>
      </div>
      <a class="logo-link" href="index.php?idp=glowna"><img class="logo" src="images/logo.jpg"></a>
      <div class="site-title">Żółwie</div>
      <nav aria-label="Główne menu" class="main-nav" role="navigation">
        <a class="active" href="index.php?idp=glowna">Strona główna</a>
        <a href="index.php?idp=ranking_zolwi">Ranking żółwii</a>
        <a href="index.php?idp=prehistoryczne">Prehistoryczne żółwie</a>
        <a href="index.php?idp=tabela">Tabela porównań żółwii</a>
        <a href="index.php?idp=orankingu">O rankingu</a>
        <a href="index.php?idp=filmy">Filmy</a>
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
      echo $strona;
    ?>
  </main>

  <footer class="site-footer" role="contentinfo">
    <div class="container">
      <div class="small">stopka</div>
      <div class="small">Kontakt:</div>
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
        $(this). animate({
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
