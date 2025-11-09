<?php
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $baza = 'moja_strona';

    $link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);
    if (!$link) echo '<b>przerwane połączenie </b>';
    if (!$link) {
        die('<b>przerwane połączenie: </b>' . mysqli_connect_error());
    }
    mysqli_set_charset($link, "utf8");
?>