<?php
/**
 * Panel Administracyjny CMS.
 * Umożliwia zarządzanie: Podstronami (Lab 4), Kategoriami (Lab 10) i Produktami (Lab 11).
 * Wersja: v1.11
 */

session_start();
include('cfg.php');

// ------------------------------------------------------------------
// SEKCJA: Logowanie
// ------------------------------------------------------------------

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

if (isset($_POST['login_email']) && isset($_POST['login_pass'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo "Błędny login lub hasło!<br>";
        echo FormularzLogowania();
        exit();
    }
}

if (!isset($_SESSION['zalogowany'])) {
    echo FormularzLogowania();
    exit();
}

echo "<h1>Panel Administracyjny (v1.11 - Sklep)</h1>";
echo '<style>
    table {border-collapse: collapse; width: 100%; margin-bottom: 20px;}
    th, td {border: 1px solid #ddd; padding: 8px; text-align: left;}
    th {background-color: #f2f2f2;}
    .btn {padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 3px; font-size: 0.9em;}
    .btn-red {background: #DC3545;}
    .sekcja-admin {border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background: #fafafa;}
</style>';

// ------------------------------------------------------------------
// SEKCJA: Funkcje zarządzania stronami (CMS)
// ------------------------------------------------------------------

function ListaPodstron() {
    global $link;
    $query = "SELECT * FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);

    echo '<table>';
    echo '<tr><th>ID</th><th>Tytuł</th><th>Opcje</th></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
        echo '<td>
                <a href="admin.php?funkcja=usun&id='.$row['id'].'" class="btn btn-red">Usuń</a>
                <a href="admin.php?funkcja=edytuj&id='.$row['id'].'" class="btn">Edytuj</a>
              </td>';
        echo '</tr>';
    }
    echo '</table>';
}

function EdytujPodstrone() {
    global $link;
    if (isset($_POST['edytuj_zapisz'])) {
        $id = (int)$_POST['id'];
        $tytul = mysqli_real_escape_string($link, $_POST['page_title']);
        $tresc = mysqli_real_escape_string($link, $_POST['page_content']);
        $aktywna = isset($_POST['status']) ? 1 : 0;
        
        $query = "UPDATE page_list SET page_title='$tytul', page_content='$tresc', status=$aktywna WHERE id=$id LIMIT 1";
        if(mysqli_query($link, $query)){
            echo "<p style='color:green;'>Zaktualizowano podstronę ID: $id.</p>";
        } else {
            echo "Błąd edycji: " . mysqli_error($link);
        }
        return;
    }

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM page_list WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        echo '<h3>Edytuj podstronę</h3>';
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="id" value="'.$row['id'].'">';
        echo 'Tytuł: <input type="text" name="page_title" value="'.htmlspecialchars($row['page_title']).'"><br><br>';
        echo 'Treść: <textarea name="page_content" rows="10" cols="50">'.htmlspecialchars($row['page_content']).'</textarea><br><br>';
        echo 'Aktywna: <input type="checkbox" name="status" '.($row['status']==1 ? 'checked' : '').'><br><br>';
        echo '<input type="submit" name="edytuj_zapisz" value="Zapisz zmiany">';
        echo '</form>';
    }
}

function DodajNowaPodstrone() {
    global $link;
    if (isset($_POST['dodaj_zapisz'])) {
        $tytul = mysqli_real_escape_string($link, $_POST['page_title']);
        $tresc = mysqli_real_escape_string($link, $_POST['page_content']);
        $aktywna = isset($_POST['status']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$tytul', '$tresc', $aktywna)";
        if(mysqli_query($link, $query)){
            echo "<p style='color:green;'>Dodano nową podstronę.</p>";
        } else {
             echo "Błąd dodawania: " . mysqli_error($link);
        }
        return;
    }

    echo '<h3>Dodaj nową podstronę</h3>';
    echo '<form method="post" action="">';
    echo 'Tytuł: <input type="text" name="page_title"><br><br>';
    echo 'Treść: <textarea name="page_content" rows="10" cols="50"></textarea><br><br>';
    echo 'Aktywna: <input type="checkbox" name="status" checked><br><br>';
    echo '<input type="submit" name="dodaj_zapisz" value="Dodaj">';
    echo '</form>';
}

function UsunPodstrone() {
    global $link;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "DELETE FROM page_list WHERE id=$id LIMIT 1";
        mysqli_query($link, $query);
        echo "<p style='color:green;'>Usunięto podstronę ID: $id.</p>";
    }
}

// ------------------------------------------------------------------
// SEKCJA: Sklep - Zarządzanie Kategoriami (Lab 10)
// ------------------------------------------------------------------

function PokazKategorie($matka = 0, $ile_wciec = 0) {
    global $link;
    $query = "SELECT * FROM categories WHERE matka = '$matka' ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $wciecie = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $ile_wciec);
            $strzalka = ($ile_wciec > 0) ? "-> " : "<b>MATKA:</b> ";
            
            echo '<div style="margin: 5px 0;">' . $wciecie . $strzalka . htmlspecialchars($row['nazwa']) . ' 
                <span style="font-size:0.8em;">
                    [<a href="admin.php?funkcja=kategorie_edytuj&id='.$row['id'].'">Edytuj</a>] 
                    [<a href="admin.php?funkcja=kategorie_usun&id='.$row['id'].'" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a>]
                </span>
            </div>';
            PokazKategorie($row['id'], $ile_wciec + 1);
        }
    }
}

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
        echo '<a href="admin.php?funkcja=kategorie_pokaz">Wróć do listy</a>';
        return;
    }
    echo '<h3>Dodaj Kategorię</h3>';
    echo '<form method="post" action="">';
    echo 'Nazwa: <input type="text" name="nazwa" required><br>';
    echo 'Matka ID (0 dla głównej): <input type="number" name="matka" value="0"><br><br>';
    echo '<input type="submit" name="dodaj_kat_zapisz" value="Dodaj Kategorię">';
    echo '</form>';
}

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
        echo 'Matka: <input type="number" name="matka" value="'.$row['matka'].'"><br><br>';
        echo '<input type="submit" name="edytuj_kat_zapisz" value="Zapisz zmiany">';
        echo '</form>';
    }
}

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
// SEKCJA: Sklep - Zarządzanie Produktami (Lab 11)
// ------------------------------------------------------------------

/**
 * Wyświetla listę produktów i status dostępności.
 */
function PokazProdukty() {
    global $link;
    $query = "SELECT * FROM produkty ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    echo '<table>';
    echo '<tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Cena (Netto/Brutto)</th>
            <th>Ilość</th>
            <th>Status</th>
            <th>Opcje</th>
          </tr>';
    
    while ($row = mysqli_fetch_array($result)) {
        // Obliczenie ceny brutto (Cena + VAT)
        $cenaBrutto = $row['cena_netto'] + ($row['cena_netto'] * $row['podatek_vat'] / 100);
        
        // Warunki dostępności (Lab 11 - Zadanie 1)
        $dostepny = true;
        if ($row['status_dostepnosci'] != 1) $dostepny = false;
        if ($row['ilosc_dostepnych_sztuk'] <= 0) $dostepny = false;
        
        // Sprawdzenie daty wygaśnięcia
        $dataWygasniecia = $row['data_wygasniecia'];
        if ($dataWygasniecia && $dataWygasniecia != '0000-00-00') {
            if (strtotime($dataWygasniecia) < time()) $dostepny = false;
        }

        $statusHtml = $dostepny ? 
            '<span style="color:green; font-weight:bold;">DOSTĘPNY</span>' : 
            '<span style="color:red; font-weight:bold;">NIEDOSTĘPNY</span>';

        // Jeśli niedostępny, pokaż powód (opcjonalnie dla admina)
        if (!$dostepny) {
            $powody = [];
            if ($row['status_dostepnosci'] != 1) $powody[] = "Wyłączony";
            if ($row['ilosc_dostepnych_sztuk'] <= 0) $powody[] = "Brak w mag.";
            if ($dataWygasniecia && strtotime($dataWygasniecia) < time()) $powody[] = "Wygasł";
            $statusHtml .= '<br><small>('.implode(', ', $powody).')</small>';
        }

        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td><b>' . htmlspecialchars($row['tytul']) . '</b><br><small>Kat ID: '.$row['kategoria'].'</small></td>';
        echo '<td>' . $row['cena_netto'] . ' zł / ' . number_format($cenaBrutto, 2) . ' zł</td>';
        echo '<td>' . $row['ilosc_dostepnych_sztuk'] . ' szt.</td>';
        echo '<td>' . $statusHtml . '</td>';
        echo '<td>
                <a href="admin.php?funkcja=produkty_usun&id='.$row['id'].'" class="btn btn-red" onclick="return confirm(\'Usunąć produkt?\')">Usuń</a>
                <a href="admin.php?funkcja=produkty_edytuj&id='.$row['id'].'" class="btn">Edytuj</a>
              </td>';
        echo '</tr>';
    }
    echo '</table>';
}

/**
 * Dodawanie produktu
 */
function DodajProdukt() {
    global $link;
    if (isset($_POST['dodaj_prod_zapisz'])) {
        $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
        $opis = mysqli_real_escape_string($link, $_POST['opis']);
        $cena = (float)$_POST['cena_netto'];
        $vat = (float)$_POST['podatek_vat'];
        $ilosc = (int)$_POST['ilosc'];
        $status = (int)$_POST['status'];
        $kategoria = (int)$_POST['kategoria'];
        $gabaryt = mysqli_real_escape_string($link, $_POST['gabaryt']);
        $zdjecie = mysqli_real_escape_string($link, $_POST['zdjecie']);
        $data_wyg = mysqli_real_escape_string($link, $_POST['data_wygasniecia']);
        
        if (empty($data_wyg)) $data_wyg = "NULL"; else $data_wyg = "'$data_wyg'";

        $query = "INSERT INTO produkty (tytul, opis, data_utworzenia, data_wygasniecia, cena_netto, podatek_vat, ilosc_dostepnych_sztuk, status_dostepnosci, kategoria, gabaryt_produktu, zdjecie) 
                  VALUES ('$tytul', '$opis', NOW(), $data_wyg, '$cena', '$vat', '$ilosc', '$status', '$kategoria', '$gabaryt', '$zdjecie')";
        
        if (mysqli_query($link, $query)) {
            echo "<p style='color:green;'>Dodano produkt.</p>";
        } else {
            echo "Błąd SQL: " . mysqli_error($link);
        }
        echo '<a href="admin.php?funkcja=produkty_pokaz">Wróć do listy produktów</a>';
        return;
    }

    echo '<h3>Dodaj Nowy Produkt</h3>';
    echo '<form method="post" action="">';
    echo '<table>';
    echo '<tr><td>Tytuł:</td><td><input type="text" name="tytul" required></td></tr>';
    echo '<tr><td>Opis:</td><td><textarea name="opis"></textarea></td></tr>';
    echo '<tr><td>Cena netto:</td><td><input type="number" step="0.01" name="cena_netto" required></td></tr>';
    echo '<tr><td>VAT (%):</td><td><input type="number" step="0.01" name="podatek_vat" value="23"></td></tr>';
    echo '<tr><td>Ilość sztuk:</td><td><input type="number" name="ilosc" required></td></tr>';
    echo '<tr><td>Status:</td><td>
            <select name="status">
                <option value="1">Dostępny (1)</option>
                <option value="0">Niedostępny (0)</option>
            </select>
          </td></tr>';
    echo '<tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria" required> <small>(Sprawdź ID w zakładce Kategorie)</small></td></tr>';
    echo '<tr><td>Gabaryt:</td><td><input type="text" name="gabaryt" placeholder="np. mały, duży, paleta"></td></tr>';
    echo '<tr><td>Link do zdjęcia:</td><td><input type="text" name="zdjecie"></td></tr>';
    echo '<tr><td>Data wygaśnięcia:</td><td><input type="date" name="data_wygasniecia"> <small>(Opcjonalne)</small></td></tr>';
    echo '<tr><td></td><td><input type="submit" name="dodaj_prod_zapisz" value="Dodaj Produkt" class="btn"></td></tr>';
    echo '</table>';
    echo '</form>';
}

/**
 * Edycja produktu
 */
function EdytujProdukt() {
    global $link;
    if (isset($_POST['edytuj_prod_zapisz'])) {
        $id = (int)$_POST['id'];
        $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
        $opis = mysqli_real_escape_string($link, $_POST['opis']);
        $cena = (float)$_POST['cena_netto'];
        $vat = (float)$_POST['podatek_vat'];
        $ilosc = (int)$_POST['ilosc'];
        $status = (int)$_POST['status'];
        $kategoria = (int)$_POST['kategoria'];
        $gabaryt = mysqli_real_escape_string($link, $_POST['gabaryt']);
        $zdjecie = mysqli_real_escape_string($link, $_POST['zdjecie']);
        $data_wyg = mysqli_real_escape_string($link, $_POST['data_wygasniecia']);
        
        if (empty($data_wyg)) $data_wyg = "NULL"; else $data_wyg = "'$data_wyg'";

        // Aktualizujemy dane oraz ustawiamy data_modyfikacji na NOW()
        $query = "UPDATE produkty SET 
                    tytul='$tytul', opis='$opis', data_modyfikacji=NOW(), data_wygasniecia=$data_wyg, 
                    cena_netto='$cena', podatek_vat='$vat', ilosc_dostepnych_sztuk='$ilosc', 
                    status_dostepnosci='$status', kategoria='$kategoria', gabaryt_produktu='$gabaryt', zdjecie='$zdjecie' 
                  WHERE id='$id' LIMIT 1";

        if (mysqli_query($link, $query)) {
            echo "<p style='color:green;'>Zaktualizowano produkt.</p>";
        } else {
            echo "Błąd SQL: " . mysqli_error($link);
        }
        echo '<a href="admin.php?funkcja=produkty_pokaz">Wróć do listy produktów</a>';
        return;
    }

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM produkty WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        echo '<h3>Edytuj Produkt (ID: '.$id.')</h3>';
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="id" value="'.$id.'">';
        echo '<table>';
        echo '<tr><td>Tytuł:</td><td><input type="text" name="tytul" value="'.htmlspecialchars($row['tytul']).'" required></td></tr>';
        echo '<tr><td>Opis:</td><td><textarea name="opis">'.htmlspecialchars($row['opis']).'</textarea></td></tr>';
        echo '<tr><td>Cena netto:</td><td><input type="number" step="0.01" name="cena_netto" value="'.$row['cena_netto'].'" required></td></tr>';
        echo '<tr><td>VAT (%):</td><td><input type="number" step="0.01" name="podatek_vat" value="'.$row['podatek_vat'].'"></td></tr>';
        echo '<tr><td>Ilość sztuk:</td><td><input type="number" name="ilosc" value="'.$row['ilosc_dostepnych_sztuk'].'" required></td></tr>';
        echo '<tr><td>Status:</td><td>
                <select name="status">
                    <option value="1" '.($row['status_dostepnosci']==1 ? 'selected' : '').'>Dostępny (1)</option>
                    <option value="0" '.($row['status_dostepnosci']==0 ? 'selected' : '').'>Niedostępny (0)</option>
                </select>
              </td></tr>';
        echo '<tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria" value="'.$row['kategoria'].'" required></td></tr>';
        echo '<tr><td>Gabaryt:</td><td><input type="text" name="gabaryt" value="'.htmlspecialchars($row['gabaryt_produktu']).'"></td></tr>';
        echo '<tr><td>Link do zdjęcia:</td><td><input type="text" name="zdjecie" value="'.htmlspecialchars($row['zdjecie']).'"></td></tr>';
        echo '<tr><td>Data wygaśnięcia:</td><td><input type="date" name="data_wygasniecia" value="'.$row['data_wygasniecia'].'"></td></tr>';
        echo '<tr><td></td><td><input type="submit" name="edytuj_prod_zapisz" value="Zapisz zmiany" class="btn"></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

function UsunProdukt() {
    global $link;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "DELETE FROM produkty WHERE id='$id' LIMIT 1";
        if (mysqli_query($link, $query)) {
            echo "<p style='color:green;'>Usunięto produkt ID: $id.</p>";
        } else {
            echo "Błąd usuwania: " . mysqli_error($link);
        }
        echo '<a href="admin.php?funkcja=produkty_pokaz">Wróć do listy</a>';
    }
}

// ------------------------------------------------------------------
// SEKCJA: Routing (obsługa akcji)
// ------------------------------------------------------------------

echo '<div class="sekcja-admin">';
if (isset($_GET['funkcja'])) {
    $funkcja = $_GET['funkcja'];
    
    // Podstrony
    if ($funkcja == 'usun') UsunPodstrone();
    elseif ($funkcja == 'edytuj') EdytujPodstrone();
    elseif ($funkcja == 'dodaj') DodajNowaPodstrone();
    
    // Kategorie
    elseif ($funkcja == 'kategorie_pokaz') {
        echo '<h3>Kategorie Sklepu</h3>';
        echo '<a href="admin.php?funkcja=kategorie_dodaj" class="btn">Dodaj Kategorię</a><br><br>';
        PokazKategorie();
    } 
    elseif ($funkcja == 'kategorie_dodaj') DodajKategorie();
    elseif ($funkcja == 'kategorie_edytuj') EdytujKategorie();
    elseif ($funkcja == 'kategorie_usun') UsunKategorie();

    // Produkty (Lab 11)
    elseif ($funkcja == 'produkty_pokaz') {
        echo '<h3>Produkty w Sklepie</h3>';
        echo '<a href="admin.php?funkcja=produkty_dodaj" class="btn">Dodaj Nowy Produkt</a><br><br>';
        PokazProdukty();
    }
    elseif ($funkcja == 'produkty_dodaj') DodajProdukt();
    elseif ($funkcja == 'produkty_edytuj') EdytujProdukt();
    elseif ($funkcja == 'produkty_usun') UsunProdukt();

    echo '<br><hr><a href="admin.php" class="btn">Wróć do Menu Głównego</a>';
} else {
    // Menu Główne
    echo '<h2>Menu Główne</h2>';
    
    echo '<h3>Zarządzanie Treścią (CMS)</h3>';
    echo '<a href="admin.php?funkcja=dodaj" class="btn">Dodaj Podstronę</a> ';
    echo '<a href="admin.php?funkcja=lista" class="btn">Lista Podstron</a><br><br>';
    ListaPodstron();
    
    echo '<hr>';
    
    echo '<h3>Sklep Internetowy</h3>';
    echo '<a href="admin.php?funkcja=kategorie_pokaz" class="btn">Zarządzaj Kategoriami (Lab 10)</a> ';
    echo '<a href="admin.php?funkcja=produkty_pokaz" class="btn">Zarządzaj Produktami (Lab 11)</a>';
}
echo '</div>';
?>