<?php
/**
 * Plik konfiguracyjny bazy danych.
 * Zawiera stałe i zmienne niezbędne do nawiązania połączenia z bazą MySQL.
 * Wersja: v1.8
 */

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza   = 'moja_strona';

// Dane logowania do panelu admina (hardcoded - do zmiany na bazę w przyszłości)
$login = "admin";
$pass  = "haslo123";

// Nawiązanie połączenia z bazą danych
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Sprawdzenie połączenia
if (!$link) {
    // W produkcji nie należy wyświetlać szczegółowych błędów użytkownikowi,
    // ale na etapie deweloperskim jest to pomocne.
    echo '<b>Przerwane połączenie z bazą danych!</b>';
    exit();
}

// Ustawienie kodowania znaków na UTF-8, aby polskie znaki wyświetlały się poprawnie
mysqli_set_charset($link, "utf8");
?>