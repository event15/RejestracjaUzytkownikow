<?php
// dołączenie kodu współużytkowanego
include '../lib/common.php';

// rozpoczęcie lub dołączenie do sesji
session_start();
header('Cache-control: private');

// zwrócenie błędu 401, jeśli użytkownik się nie uwierzytelnił
if (!isset($_SESSION['access']) || $_SESSION['access'] != TRUE)
{
    header('HTTP/1.0 401 Authorization Error');
    ob_start();
    ?>
    <script type="text/javascript">
        window.seconds = 10;
        window.onload = function()
        {
            if (window.seconds != 0)
            {
                document.getElementById('secondsDisplay').innerHTML = '' +
                    window.seconds + ' sekund' + ((window.seconds > 4) ? '' : 'y');
                window.seconds--;
                setTimeout(window.onload, 1000);
            }
            else
            {
                window.location = 'login.php';
            }
        }
    </script>
    <?php
    $GLOBALS['TEMPLATE']['extra_head'] = ob_get_contents();
    ob_clean();

    ?>
    <p>Wywołany zasób wymaga uwierzytelnienia się. Nie wpisano
        odpowiednich danych uwierzytelniających lub podane dane
        uwierzytelniające nie uprawniają do uzyskania dostępu do zasobu.</p>

    <p><strong>Za <span id="secondsDisplay">10 sekund</span> nastąpi
            przekierowanie do strony logowania.</strong></p>

    <p>Jeżeli przekierowanie nie nastąpi automatycznie , należy kliknąć następujące łącze:
        <a href="login.php">Logowanie</a></p>
    <?php
    $GLOBALS['TEMPLATE']['content'] = ob_get_clean();

    include '../templates/template.php';
    exit();
}
?>