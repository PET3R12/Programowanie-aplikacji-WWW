<?php
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
  if($_GET['idp'] =='')$strona = 'html/glowna.html';
  if($_GET['idp'] =='glowna')$strona = 'html/glowna.html';
  if($_GET['idp'] =='ranking_zolwi')$strona = 'html/ranking_zolwi.html';
  if($_GET['idp'] =='orankingu')$strona = 'html/orankingu.html';
  if($_GET['idp'] =='prehistoryczne')$strona = 'html/prehistoryczne.html';
  if($_GET['idp'] =='tabela')$strona = 'html/tabela_porownan.html';
  
  $page = $_GET['page'] ?? 'glowna';
  $allowed_pages = ['glowna', 'ranking_zolwii', 'prehistoryczne', 'tabela', 'orankingu'];
  $file_to_include = 'podstrony/' . $page . '.html';
?>

<!doctype html>
<html lang="pl">
<!-- http://localhost/moj_projekt/strona1v3 -->
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
      include($strona);
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
