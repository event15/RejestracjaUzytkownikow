<?php
// dołączenie kodu współużytkowanego
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// dołączenie pliku 401.php - użytkownik może oglądać stronę tylko po zalogowaniu się
include '401.php';

// wygenerowanie formularza informacji o użytkowniku
$user = User::getById($_SESSION['userId']);
ob_start();
?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
          method="post">
        <table>
            <tr>
                <td><label>Nazwa użytkownika</label></td>
                <td><input type="text" name="username"  disabled="disabled"
                           readonly="readonly" value="<?php echo $user->username; ?>"/></td>
            </tr><tr>
                <td><label for="email">Adres email</label></td>
                <td><input type="text" name="email" id="email"
                           value="<?php echo (isset($_POST['email']))? htmlspecialchars(
                               $_POST['email']) : $user->emailAddr; ?>"/></td>
            </tr><tr>
                <td><label for="password">Nowe hasło</label></td>
                <td><input type="password" name="password1" id="password1"/></td>
            </tr><tr>
                <td><label for="password2">Powtórzenie hasła</label></td>
                <td><input type="password" name="password2" id="password2"/></td>
            </tr><tr>
                <td> </td>
                <td><input type="submit" value="Zapisz"/></td>
                <td><input type="hidden" name="submitted" value="1"/></td>
            </tr><tr>
        </table>
    </form>
<?php
$form = ob_get_clean();

// wyświetlenie formularza, jeśli strona jest wyświetlana po raz pierwszy
if (!isset($_POST['submitted']))
{
    $GLOBALS['TEMPLATE']['content'] = $form;
}
// w przeciwnym razie przetworzenie danych wejściowych
else
{
    // sprawdzenie poprawności hasła
    $password1 = (isset($_POST['password1']) && $_POST['password1']) ?
        sha1($_POST['password1']) : $user->password;
    $password2 = (isset($_POST['password2']) && $_POST['password2']) ?
        sha1($_POST['password2']) : $user->password;
    $password = ($password1 == $password2) ? $password1 : '';

    // uaktualnienie rekordu, jeżeli dane wejściowe są poprawne
    if (User::validateEmailAddr($_POST['email']) && $password)
    {
        $user->emailAddr = $_POST['email'];
        $user->password = $password;
        $user->save();

        $GLOBALS['TEMPLATE']['content'] = '<p><strong>Informacje ' .
            'w bazie danych zostały uaktualnione.</strong></p>';
    }
    // dane nieprawidłowe
    else
    {
        $GLOBALS['TEMPLATE']['content'] .= '<p><strong>Podano nieprawidłowe ' .
            'dane.</strong></p>';
        $GLOBALS['TEMPLATE']['content'] .= $form;
    }
}
// wyświetlenie strony
include '../templates/template.php';
?>