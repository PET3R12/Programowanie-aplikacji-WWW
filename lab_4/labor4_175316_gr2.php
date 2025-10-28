<?php
//http://localhost/moj_projekt/labor4_175316_gr2.php?name=idk
  session_start();
  $nr_indeksu = '175316';
  $nrGrupy = 'ISI2';
  echo 'Piotr Ostaszewski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
  echo '<br /> Zastosowanie metody include() <br />';
  include "text.txt";
  echo '<br />b) Warunki if, else, elseif, switch:';
  $a = 10;
  $b = 20;
  if ($a == $b)
  {
    echo '<br /> a i b są równe <br />';
  }
  elseif ($a > $b)
  {
    echo '<br /> a jest większe od b <br />';
  }
  else
  {
      echo '<br /> b jest większe od a <br />';
  }
  $tekst = 'jakiś tekst';

  echo '<br /> SWITCH: <br />';

  switch ($tekst)
  {
    case 'jakiś tekst':
      echo "git";
      break;
    case 'inny tekst':
      echo 'nie git';
      break;
    default:
    echo 'To nie jest tak, że jest git, albo nie jest git...';
  }
  echo '<br />c) Pętla while() i for():';
  for($i = 0; $i <=10; $i = $i+1)
  {
    echo $i;
    echo '<br />';
  }
  $i = 0;
  while($i <=10){
    echo $i;
    echo '<br />';
    $i = $i +2;
  }
  echo '<br /><br /> Zastosowanie typów zmiennych $_GET';
  echo '<br />Siema ' . htmlspecialchars($_GET["name"]) . '!';
?>
  <form method="POST" action="">
  <input type="text" name="name" placeholder="podaj imie">
  <input type="submit" value="git">
  </form>
<?php
  echo '<br /><br /> Zastosowanie typów zmiennych $_POST';
  echo '<br />Witaj ' . htmlspecialchars($_POST["name"]) . '!';


  echo '<br /><br /> Zastosowanie typów zmiennych $_SESSION';
  $_SESSION['login'] = "Janek";
  echo '<br /> Login = ';
  echo $_SESSION['login']; 
?>

