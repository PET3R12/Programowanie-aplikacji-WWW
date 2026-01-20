<?php
/**
 * Moduł Sklepu Internetowego (Lab 12).
 * Zawiera logikę koszyka opartego o sesje oraz wyświetlanie produktów.
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------------
// LOGIKA KOSZYKA (Obsługa akcji)
// ------------------------------------------------------------------

// 1. Dodawanie do koszyka
if (isset($_POST['action']) && $_POST['action'] == 'dodaj') {
    $id_prod = (int)$_POST['id_prod'];
    $ile = (int)$_POST['ile'];

    if (!isset($_SESSION['koszyk'])) {
        $_SESSION['koszyk'] = array();
    }

    $znaleziono = false;
    foreach ($_SESSION['koszyk'] as $key => $item) {
        if ($item['id'] == $id_prod) {
            $_SESSION['koszyk'][$key]['ile'] += $ile;
            $znaleziono = true;
            break;
        }
    }

    if (!$znaleziono) {
        $_SESSION['koszyk'][] = array('id' => $id_prod, 'ile' => $ile, 'data' => time());
    }
    
    header("Location: index.php?idp=sklep");
    exit();
}

// 2. Usuwanie z koszyka
if (isset($_GET['action']) && $_GET['action'] == 'usun' && isset($_GET['index'])) {
    $index = (int)$_GET['index'];
    if (isset($_SESSION['koszyk'][$index])) {
        unset($_SESSION['koszyk'][$index]);
        $_SESSION['koszyk'] = array_values($_SESSION['koszyk']);
    }
    header("Location: index.php?idp=sklep");
    exit();
}

// 3. Aktualizacja ilości
if (isset($_POST['action']) && $_POST['action'] == 'aktualizuj') {
    if (isset($_POST['ilosci'])) {
        foreach ($_POST['ilosci'] as $index => $nowa_ilosc) {
            $nowa_ilosc = (int)$nowa_ilosc;
            if ($nowa_ilosc <= 0) {
                unset($_SESSION['koszyk'][$index]);
            } else {
                $_SESSION['koszyk'][$index]['ile'] = $nowa_ilosc;
            }
        }
        $_SESSION['koszyk'] = array_values($_SESSION['koszyk']);
    }
    header("Location: index.php?idp=sklep");
    exit();
}

// ------------------------------------------------------------------
// FUNKCJE WIDOKU
// ------------------------------------------------------------------

/**
 * Wyświetla listę produktów PODZIELONĄ NA KATEGORIE.
 */
function PokazProduktySklep() {
    global $link;

    echo '<div class="shop-container">';
    echo '<h1>Oferta Sklepu</h1>';

    // 1. Pobierz wszystkie kategorie główne
    $queryKat = "SELECT * FROM categories WHERE matka = 0 ORDER BY id ASC";
    $resultKat = mysqli_query($link, $queryKat);

    if (!$resultKat || mysqli_num_rows($resultKat) == 0) {
        echo 'Brak kategorii w sklepie.';
        return;
    }

    // 2. Dla każdej kategorii wyświetl nagłówek i jej produkty
    while ($rowKat = mysqli_fetch_array($resultKat)) {
        $catId = $rowKat['id'];
        $catName = htmlspecialchars($rowKat['nazwa']);

        // Zapytanie o produkty dla TEJ konkretnej kategorii
        $queryProd = "SELECT * FROM produkty WHERE kategoria = '$catId' AND status_dostepnosci = 1 AND ilosc_dostepnych_sztuk > 0";
        $resultProd = mysqli_query($link, $queryProd);

        // Jeśli kategoria ma produkty, wyświetl ją
        if (mysqli_num_rows($resultProd) > 0) {
            echo '<h2 style="border-bottom: 2px solid #f39c12; padding-bottom: 5px; margin-top: 30px;">' . $catName . '</h2>';
            echo '<div class="products-grid">';
            
            while ($row = mysqli_fetch_array($resultProd)) {
                $cenaBrutto = $row['cena_netto'] * (1 + $row['podatek_vat'] / 100);
                $img = $row['zdjecie'] ? $row['zdjecie'] : 'images/brak.jpg';

                echo '<div class="product-card">';
                echo '<img src="'.$img.'" alt="Produkt" style="width:100%; height:150px; object-fit:cover;">';
                echo '<h3>'.htmlspecialchars($row['tytul']).'</h3>';
                // echo '<p>'.htmlspecialchars($row['opis']).'</p>'; // Opcjonalnie opis
                echo '<p class="price">'.number_format($cenaBrutto, 2).' zł</p>';
                
                echo '<form method="post" action="index.php?idp=sklep">';
                echo '<input type="hidden" name="action" value="dodaj">';
                echo '<input type="hidden" name="id_prod" value="'.$row['id'].'">';
                echo '<input type="number" name="ile" value="1" min="1" max="'.$row['ilosc_dostepnych_sztuk'].'" style="width: 50px;">';
                echo '<br><input type="submit" value="Do koszyka" class="btn-shop">';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>'; // koniec grid
        }
    }
    
    echo '</div>';
}

/**
 * Wyświetla zawartość koszyka.
 */
function PokazKoszyk() {
    global $link;
    
    echo '<div class="cart-container" id="koszyk">';
    echo '<h2>Twój Koszyk <span style="font-size:0.6em">🛒</span></h2>';

    if (!isset($_SESSION['koszyk']) || empty($_SESSION['koszyk'])) {
        echo '<p>Koszyk jest pusty.</p>';
        echo '</div>';
        return;
    }

    echo '<form method="post" action="index.php?idp=sklep">';
    echo '<input type="hidden" name="action" value="aktualizuj">';
    echo '<table class="cart-table">';
    echo '<tr><th>Produkt</th><th>Cena (Brutto)</th><th>Ilość</th><th>Wartość</th><th>Usuń</th></tr>';

    $sumaCalakowita = 0;

    foreach ($_SESSION['koszyk'] as $index => $item) {
        $id = (int)$item['id'];
        $ile = (int)$item['ile'];

        $query = "SELECT tytul, cena_netto, podatek_vat FROM produkty WHERE id = '$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        $cenaNetto = (float)$row['cena_netto'];
        $vat = (float)$row['podatek_vat'];
        $cenaBrutto = $cenaNetto * (1 + $vat / 100);
        $wartoscPozycji = $cenaBrutto * $ile;

        $sumaCalakowita += $wartoscPozycji;

        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
        echo '<td>' . number_format($cenaBrutto, 2) . ' zł</td>';
        echo '<td><input type="number" name="ilosci['.$index.']" value="'.$ile.'" min="0" style="width:50px;"></td>';
        echo '<td>' . number_format($wartoscPozycji, 2) . ' zł</td>';
        echo '<td><a href="index.php?idp=sklep&action=usun&index='.$index.'" class="btn-remove">X</a></td>';
        echo '</tr>';
    }

    echo '<tr class="total-row"><td colspan="3">RAZEM DO ZAPŁATY:</td><td colspan="2">' . number_format($sumaCalakowita, 2) . ' zł</td></tr>';
    echo '</table>';
    echo '<br>';
    echo '<input type="submit" value="Przelicz koszyk" class="btn-update">';
    echo '<button type="button" onclick="alert(\'Zamówienie złożone! (symulacja)\')" class="btn-pay">Zamów i zapłać</button>';
    echo '</form>';
    echo '</div>';
}
?>