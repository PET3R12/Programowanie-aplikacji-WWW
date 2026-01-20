<?php
/**
 * Moduł obsługi kontaktu i przypominania hasła.
 * Zawiera funkcje do generowania formularza i wysyłania e-maili.
 * Wersja: v1.8
 */

/**
 * Wyświetla formularz kontaktowy HTML.
 * @return string HTML formularza
 */
function PokazKontakt()
{
    $wynik = '
    <div class="kontakt-formularz">
        <h2>Formularz kontaktowy</h2>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
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

/**
 * Wysyła wiadomość e-mail z formularza kontaktowego.
 * @param string $odbiorca Adres e-mail administratora
 */
function WyslijMailKontakt($odbiorca)
{
    // Sprawdzenie czy pola nie są puste
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt(); // Ponowne wyświetlenie formularza
    } else {
        $mail['subject']    = htmlspecialchars($_POST['temat']);
        $mail['body']       = htmlspecialchars($_POST['tresc']);
        $mail['sender']     = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $mail['recipient']  = $odbiorca;

        // Nagłówki maila
        $header  = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: <" . $mail['sender'] . ">\n";
        $header .= "X-Mailer: PRapWWW mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <" . $mail['sender'] . ">\n";

        // Wysłanie maila
        mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

        echo '[wiadomosc_wyslana]';
    }
}

/**
 * Funkcja przypominająca hasło admina.
 * @param string $emailOdbiorcy Adres e-mail na który wysłać hasło
 */
function PrzypomnijHaslo($emailOdbiorcy)
{
    $haslo = "twoje_tajne_haslo_admina_123"; // W przyszłości pobierać z bazy!
    
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