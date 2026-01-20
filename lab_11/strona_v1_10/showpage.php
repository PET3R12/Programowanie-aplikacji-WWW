<?php
/**
 * Funkcja do wyświetlania treści podstrony z bazy danych.
 * * @param int|string $id - ID strony do wyświetlenia
 * @return string - Treść strony lub komunikat błędu
 */
function PokazPodstrone($id)
{
    global $link;

    // ZABEZPIECZENIE: Oczyszczanie danych wejściowych przed użyciem w SQL.
    // htmlspecialchars chroni przed XSS, mysqli_real_escape_string przed SQL Injection.
    $id_clear = mysqli_real_escape_string($link, htmlspecialchars($id));

    // Zapytanie z LIMIT 1 dla optymalizacji (szukamy tylko jednego rekordu)
    $query  = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1";
    $result = mysqli_query($link, $query);

    // Sprawdzenie czy strona istnieje
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $web = $row['page_content'];
    } else {
        $web = '[nie_znaleziono_strony]';
    }

    return $web;
}
?>