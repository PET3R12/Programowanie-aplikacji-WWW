<?php

function PokazKontakt()
{
    $wynik = '
    <div class="kontakt-formularz">
        <h2>Formularz kontaktowy</h2>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <div class="form-group">
                <label for="email">Twój e-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="temat">Temat:</label>
                <input type="text" id="temat" name="temat" required>
            </div>
            <div class="form-group">
                <label for="tresc">Treść wiadomości:</label>
                <textarea id="tresc" name="tresc" rows="5" required></textarea>
            </div>
            <input type="submit" name="wyslij_kontakt" value="Wyślij wiadomość">
        </form>
    </div>';

    return $wynik;
}

function WyslijMailKontakt($odbiorca)
{
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt();
    } else {
        $mail['subject']    = $_POST['temat'];
        $mail['body']       = $_POST['tresc'];
        $mail['sender']     = $_POST['email'];
        $mail['recipient']  = $odbiorca;

        $header  = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: <" . $mail['sender'] . ">\n";
        $header .= "X-Mailer: PRapWWW mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <" . $mail['sender'] . ">\n";
        mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

        echo '[wiadomosc_wyslana]';
    }
}

function PrzypomnijHaslo($emailOdbiorcy)
{
    $haslo = "twoje_tajne_haslo_admina_123"; 
    
    $mail['subject']    = "Przypomnienie hasła do panelu admina";
    $mail['body']       = "Twoje hasło do panelu to: " . $haslo;
    $mail['sender']     = "system@twojastrona.pl";
    $mail['recipient']  = $emailOdbiorcy;

    $header  = "From: System przypominania hasła <" . $mail['sender'] . ">\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
    $header .= "X-Mailer: PRapWWW mail 1.2\n";
    $header .= "X-Priority: 1\n"; // Wyższy priorytet dla hasła
    $header .= "Return-Path: <" . $mail['sender'] . ">\n";

    mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

    echo '[haslo_wyslane]';
}

?>