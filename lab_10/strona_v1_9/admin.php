<?php
/**
 * Panel Administracyjny CMS.
 * Umożliwia dodawanie, edycję i usuwanie podstron oraz kategorii sklepowych.
 * Wersja: v1.9 (z obsługą sklepu)
 */

session_start();
include('cfg.php');

// ------------------------------------------------------------------
// SEKCJA: Logowanie
// ------------------------------------------------------------------

/**
 * Generuje formularz logowania.
 * @return string HTML formularza
 */
function FormularzLogowania() {
    $wynik = '
    <div class="logowanie">
     <h1 class="heading">Panel CMS:</h1>
     <div class="logowanie">
      <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
       <table class="logowanie">
        <tr><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
        <tr><td class="log4_t">[haslo]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" name="x1_submit" class="logowanie" value="zaloguj" /></td></tr>
       </table>
      </form>
     </div>
    </div>
    ';
    return $wynik;
}

// Weryfikacja danych logowania
if (isset($_POST['login_email']) && isset($_POST['login_pass'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo "Błędny login lub hasło!<br>";
        echo FormularzLogowania();
        exit();
    }
}

// Blokada dostępu dla niezalogowanych
if (!isset($_SESSION['zalogowany'])) {
    echo FormularzLogowania();
    exit();
}

echo "<h1>Witaj w panelu administracyjnym (v1.9)</h1>";

// ------------------------------------------------------------------
// SEKCJA: Funkcje zarządzania stronami (CMS)
// ------------------------------------------------------------------

/**
 * Wyświetla listę wszystkich podstron z bazy danych.
 */
function ListaPodstron() {
    global $link;
    // LIMIT 100 zabezpiecza przed przeciążeniem przy dużej liczbie rekordów
    $query = "SELECT * FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);

    echo '<table>';
    echo '<tr><th>ID</th><th>Tytuł</th><th>Opcje</th></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['page_title']) . '</td>'; // htmlspecialchars chroni przed XSS w tytule
        echo '<td>
                <a href="admin.php?funkcja=usun&id='.$row['id'].'">Usuń</a> | 
                <a href="admin.php?funkcja=edytuj&id='.$row['id'].'">Edytuj</a>
              </td>';
        echo '</tr>';
    }
    echo '</table>';
}

/**
 * Obsługuje proces edycji podstrony (wyświetlenie formularza i zapis zmian).
 */
function EdytujPodstrone() {
    global $link;
    
    // Zapisywanie zmian po wysłaniu formularza
    if (isset($_POST['edytuj_zapisz'])) {
        $id = (int)$_POST['id']; // Rzutowanie na int dla bezpieczeństwa
        
        // ZABEZPIECZENIE: Escape string przeciwko SQL Injection
        $tytul = mysqli_real_escape_string($link, $_POST['page_title']);
        $tresc = mysqli_real_escape_string($link, $_POST['page_content']);
        $aktywna = isset($_POST['status']) ? 1 : 0;
        
        // Zapytanie UPDATE z LIMIT 1
        $query = "UPDATE page_list SET page_title='$tytul', page_content='$tresc', status=$aktywna WHERE id=$id LIMIT 1";
        
        if(mysqli_query($link, $query)){
            echo "Zaktualizowano podstronę ID: $id.";
        } else {
            echo "Błąd edycji: " . mysqli_error($link);
        }
        return;
    }

    // Wyświetlanie formularza edycji
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id']; // Zabezpieczenie ID
        $query = "SELECT * FROM page_list WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        echo '<h3>Edytuj podstronę</h3>';
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="id" value="'.$row['id'].'">';
        echo 'Tytuł: <input type="text" name="page_title" value="'.htmlspecialchars($row['page_title']).'"><br>';
        echo 'Treść: <textarea name="page_content" rows="10" cols="50">'.htmlspecialchars($row['page_content']).'</textarea><br>';
        echo 'Aktywna: <input type="checkbox" name="status" '.($row['status']==1 ? 'checked' : '').'><br>';
        echo '<input type="submit" name="edytuj_zapisz" value="Zapisz zmiany">';
        echo '</form>';
    }
}

/**
 * Obsługuje dodawanie nowej podstrony.
 */
function DodajNowaPodstrone() {
    global $link;

    if (isset($_POST['dodaj_zapisz'])) {
        // ZABEZPIECZENIE: Escape string
        $tytul = mysqli_real_escape_string($link, $_POST['page_title']);
        $tresc = mysqli_real_escape_string($link, $_POST['page_content']);
        $aktywna = isset($_POST['status']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$tytul', '$tresc', $aktywna)";
        
        if(mysqli_query($link, $query)){
            echo "Dodano nową podstronę.";
        } else {
             echo "Błąd dodawania: " . mysqli_error($link);
        }
        return;
    }

    echo '<h3>Dodaj nową podstronę</h3>';
    echo '<form method="post" action="">';
    echo 'Tytuł: <input type="text" name="page_title"><br>';
    echo 'Treść: <textarea name="page_content" rows="10" cols="50"></textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="status" checked><br>';
    echo '<input type="submit" name="dodaj_zapisz" value="Dodaj">';
    echo '</form>';
}

/**
 * Usuwa podstronę na podstawie ID.
 */
function UsunPodstrone() {
    global $link;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id']; // Rzutowanie na int - kluczowe dla bezpieczeństwa DELETE
        
        $query = "DELETE FROM page_list WHERE id=$id LIMIT 1";
        mysqli_query($link, $query);
        echo "Usunięto podstronę ID: $id.";
    }
}

// ------------------------------------------------------------------
// SEKCJA: Sklep - Zarządzanie Kategoriami (Lab 10)
// ------------------------------------------------------------------

/**
 * [cite_start]Funkcja rekurencyjna do generowania drzewa kategorii[cite: 19].
 * Realizuje wyświetlanie matek i dzieci z wcięciami.
 */
function PokazKategorie($matka = 0, $ile_wciec = 0) {
    global $link;
    
    // Pobieramy kategorie dla danej matki
    $query = "SELECT * FROM categories WHERE matka = '$matka' ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            // Wcięcia dla wizualizacji drzewa
            $wciecie = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $ile_wciec);
            $strzalka = ($ile_wciec > 0) ? "-> " : "<b>MATKA:</b> ";
            
            echo '<div>' . $wciecie . $strzalka . htmlspecialchars($row['nazwa']) . ' 
                <span style="font-size:0.8em;">
                    [<a href="admin.php?funkcja=kategorie_edytuj&id='.$row['id'].'">Edytuj</a>] 
                    [<a href="admin.php?funkcja=kategorie_usun&id='.$row['id'].'" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>]
                </span>
            </div>';
            
            // Rekurencja: wywołanie dla dzieci tej kategorii
            PokazKategorie($row['id'], $ile_wciec + 1);
        }
    }
}

/**
 * Obsługa dodawania nowej kategorii.
 * [cite_start]Pozwala zdefiniować czy kategoria jest matką (0) czy podkategorią[cite: 17, 18].
 */
function DodajKategorie() {
    global $link;
    
    if (isset($_POST['dodaj_kat_zapisz'])) {
        $matka = (int)$_POST['matka'];
        $nazwa = mysqli_real_escape_string($link, $_POST['nazwa']);
        
        $query = "INSERT INTO categories (matka, nazwa) VALUES ('$matka', '$nazwa')";
        if (mysqli_query($link, $query)) {
            echo "Dodano kategorię: $nazwa<br>";
        } else {
            echo "Błąd: " . mysqli_error($link);
        }
        // Przeładowanie, żeby widzieć zmiany
        echo '<a href="admin.php?funkcja=kategorie_pokaz">Wróć do listy</a>';
        return;
    }

    // Formularz
    echo '<h3>Dodaj Kategorię</h3>';
    echo '<form method="post" action="">';
    echo 'Nazwa: <input type="text" name="nazwa" required><br>';
    echo 'Matka (wpisz ID lub 0 dla głównej): <input type="number" name="matka" value="0"><br>';
    echo '<small>0 = Kategoria główna. Aby dodać podkategorię, wpisz ID matki (sprawdź na liście).</small><br><br>';
    echo '<input type="submit" name="dodaj_kat_zapisz" value="Dodaj Kategorię">';
    echo '</form>';
}

/**
 * Edycja kategorii.
 */
function EdytujKategorie() {
    global $link;
    
    if (isset($_POST['edytuj_kat_zapisz'])) {
        $id = (int)$_POST['id'];
        $matka = (int)$_POST['matka'];
        $nazwa = mysqli_real_escape_string($link, $_POST['nazwa']);
        
        $query = "UPDATE categories SET nazwa='$nazwa', matka='$matka' WHERE id='$id' LIMIT 1";
        if (mysqli_query($link, $query)) {
            echo "Zaktualizowano kategorię.<br>";
        } else {
            echo "Błąd: " . mysqli_error($link);
        }
        echo '<a href="admin.php?funkcja=kategorie_pokaz">Wróć do listy</a>';
        return;
    }

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM categories WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
        
        echo '<h3>Edytuj Kategorię</h3>';
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="id" value="'.$row['id'].'">';
        echo 'Nazwa: <input type="text" name="nazwa" value="'.htmlspecialchars($row['nazwa']).'"><br>';
        echo 'Matka: <input type="number" name="matka" value="'.$row['matka'].'"><br>';
        echo '<input type="submit" name="edytuj_kat_zapisz" value="Zapisz zmiany">';
        echo '</form>';
    }
}

/**
 * Usuwanie kategorii.
 */
function UsunKategorie() {
    global $link;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "DELETE FROM categories WHERE id='$id' LIMIT 1";
        if (mysqli_query($link, $query)) {
            echo "Usunięto kategorię ID: $id.<br>";
        } else {
            echo "Błąd usuwania: " . mysqli_error($link);
        }
        echo '<a href="admin.php?funkcja=kategorie_pokaz">Wróć do listy</a>';
    }
}

// ------------------------------------------------------------------
// SEKCJA: Routing (obsługa akcji)
// ------------------------------------------------------------------

if (isset($_GET['funkcja'])) {
    $funkcja = $_GET['funkcja'];
    
    // Obsługa Podstron (CMS)
    if ($funkcja == 'usun') {
        UsunPodstrone();
    } elseif ($funkcja == 'edytuj') {
        EdytujPodstrone();
    } elseif ($funkcja == 'dodaj') {
        DodajNowaPodstrone();
    } 
    // Obsługa Kategorii Sklepu (Lab 10)
    elseif ($funkcja == 'kategorie_pokaz') {
        echo '<h3>Drzewo Kategorii</h3>';
        echo '<a href="admin.php?funkcja=kategorie_dodaj">[Dodaj nową kategorię]</a><br><br>';
        PokazKategorie(); // Wywołanie rekurencyjne 
    } elseif ($funkcja == 'kategorie_dodaj') {
        DodajKategorie();
    } elseif ($funkcja == 'kategorie_edytuj') {
        EdytujKategorie();
    } elseif ($funkcja == 'kategorie_usun') {
        UsunKategorie();
    }

    echo '<br><hr><a href="admin.php">Wróć do głównego panelu</a>';
} else {
    // Główny widok panelu
    echo '<h2>Zarządzanie Podstronami (CMS)</h2>';
    echo '<a href="admin.php?funkcja=dodaj">Dodaj nową podstronę</a><br><br>';
    ListaPodstron();
    
    echo '<br><hr><br>';
    
    echo '<h2>Zarządzanie Kategoriami Sklepu (Lab 10)</h2>';
    echo '<a href="admin.php?funkcja=kategorie_pokaz">Zarządzaj Kategoriami</a>';
}
?>