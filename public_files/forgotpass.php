<?php
// dołączenie kodu współużytkowanego
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// formularz HTML z żądaniem hasła
ob_start();
?>
    <form action="<?php echo htmlspecialchars($_SEVER['PHP_SELF']); ?>"
          method="post">
        <p>Podaj nazwę użytkownika. Nowe hasło zostanie wysłane
            na podany adres poczty email.</p>
        <table>
            <tr>
                <td><label for="username">Nazwa użytkownika</label></td>
                <td><input type="text" name="username" id="username"
                           value="<?php if (isset($_POST['username']))
                               echo htmlspecialchars($_POST['username']); ?>"/></td>
            </tr><tr>
                <td> </td>
                <td><input type="submit" value="Zatwierdź"/></td>
                <td><input type="hidden" name="submitted" value="1"/></td>
            </tr><tr>
        </table>
    </form>
<?php
$form = ob_get_clean();

// wyświetlenie formularza, jeśli strona jest przeglądana po raz pierwszy
if (!isset($_POST['submitted']))
{
    $GLOBALS['TEMPLATE']['content'] = $form;
}
// w przeciwnym razie - przetworzenie danych wejściowych
else
{
    // sprawdzenie poprawności nazwy użytkownika
    if (User::validateUsername($_POST['username']))
    {
        $user = User::getByUsername($_POST['username']);
        if (!$user->userId)
        {
            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Przepraszamy, ' .
                'podane konto nie istnieje.</strong></p> <p>Prosimy podać ' .
                'inną nazwę użytkownika.</p>';
            $GLOBALS['TEMPLATE']['content'] .= $form;
        }
        else
        {
            // wygenerowanie nowego hasła
            $password = random_text(8);

            // wysłanie nowego hasła na adres pocztowy
            $message = 'Nowe hasło to: ' . $password;
            mail($user->emailAddr, 'New password', $message);

            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Nowe hasło ' .
                'wysłano na podany adres pocztowy.</strong></p>';

            // zapisanie nowego hasła
            $user->password = $password;
            $user->save();
        }
    }
    // dane błędne
    else
    {
        $GLOBALS['TEMPLATE']['content'] .= '<p><strong>Nie podano ' .
            'prawidłowej nazwy użytkownika.</strong></p> <p>Prosimy ' .
            'spróbować ponownie.</p>';
        $GLOBALS['TEMPLATE']['content'] .= $form;
    }
}

// wyświetlenie strony
include '../templates/template.php';