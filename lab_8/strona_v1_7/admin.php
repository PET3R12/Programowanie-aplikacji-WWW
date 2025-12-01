<?php
session_start();
include('cfg.php');

function FormularzLogowania() {
    $wynik = '
    <div class="logowanie">
     <h1 class="heading">Panel CMS:</h1>
     <div class="logowanie">
      <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
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

echo "<h1>Witaj w panelu administracyjnym (v1.6)</h1>";

function ListaPodstron() {
    global $link;
    $query = "SELECT * FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);

    echo '<table>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['page_title'] . '</td>';
        echo '<td><a href="admin.php?funkcja=usun&id='.$row['id'].'">Usuń</a></td>';
        echo '<td><a href="admin.php?funkcja=edytuj&id='.$row['id'].'">Edytuj</a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

function EdytujPodstrone() {
    global $link;
    
    if (isset($_POST['edytuj_zapisz'])) {
        $id = $_POST['id'];
        $tytul = $_POST['page_title'];
        $tresc = $_POST['page_content'];
        $aktywna = isset($_POST['status']) ? 1 : 0;
        
        $query = "UPDATE page_list SET page_title='$tytul', page_content='$tresc', status=$aktywna WHERE id=$id LIMIT 1";
        mysqli_query($link, $query);
        echo "Zaktualizowano podstronę.";
        return;
    }

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM page_list WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        echo '<h3>Edytuj podstronę</h3>';
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="id" value="'.$row['id'].'">';
        echo 'Tytuł: <input type="text" name="page_title" value="'.$row['page_title'].'"><br>';
        echo 'Treść: <textarea name="page_content">'.$row['page_content'].'</textarea><br>';
        echo 'Aktywna: <input type="checkbox" name="status" '.($row['status']==1 ? 'checked' : '').'><br>';
        echo '<input type="submit" name="edytuj_zapisz" value="Zapisz zmiany">';
        echo '</form>';
    }
}

function DodajNowaPodstrone() {
    global $link;

    if (isset($_POST['dodaj_zapisz'])) {
        $tytul = $_POST['page_title'];
        $tresc = $_POST['page_content'];
        $aktywna = isset($_POST['status']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$tytul', '$tresc', $aktywna)";
        mysqli_query($link, $query);
        echo "Dodano nową podstronę.";
        return;
    }

    echo '<h3>Dodaj nową podstronę</h3>';
    echo '<form method="post" action="">';
    echo 'Tytuł: <input type="text" name="page_title"><br>';
    echo 'Treść: <textarea name="page_content"></textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="status" checked><br>';
    echo '<input type="submit" name="dodaj_zapisz" value="Dodaj">';
    echo '</form>';
}

function UsunPodstrone() {
    global $link;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM page_list WHERE id=$id LIMIT 1";
        mysqli_query($link, $query);
        echo "Usunięto podstronę.";
    }
}

if (isset($_GET['funkcja'])) {
    $funkcja = $_GET['funkcja'];

    if ($funkcja == 'usun') {
        UsunPodstrone();
    } elseif ($funkcja == 'edytuj') {
        EdytujPodstrone();
    } elseif ($funkcja == 'dodaj') {
        DodajNowaPodstrone();
    }
    echo '<br><a href="admin.php">Wróć do listy</a>';
} else {
    echo '<a href="admin.php?funkcja=dodaj">Dodaj nową podstronę</a><br><br>';
    ListaPodstron();
}
?>