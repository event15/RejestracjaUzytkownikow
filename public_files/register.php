<?php
// dołączenie kodu współużytkowanego
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// rozpoczęcie lub kontynuacja sesji, by udostępnić
// test CAPTCHA przechowywany w zmiennej $_SESSION
session_start();
header('Cache-control: private');

// przygotowanie formularza HTML do rejestracji
ob_start();
?>
    <form method="post"
          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <table>
            <tr>
                <td><label for="username">Nazwa użytkownika</label></td>
                <td><input type="text" name="username" id="username"
                           value="<?php if (isset($_POST['username']))
                               echo htmlspecialchars($_POST['username']); ?>"/></td>
            </tr><tr>
                <td><label for="password1">Hasło</label></td>
                <td><input type="password" name="password1" id="password1"
                           value=""/></td>
            </tr><tr>
                <td><label for="password2">Powtórzenie hasła</label></td>
                <td><input type="password" name="password2" id="password2"
                           value=""/></td>
            </tr><tr>
                <td><label for="email">Adres email</label></td>
                <td><input type="text" name="email" id="email"
                           value="<?php if (isset($_POST['email']))
                               echo htmlspecialchars($_POST['email']); ?>"/></td>
            </tr><tr>
                <td><label for="captcha">Weryfikacja</label></td>
                <td>Wpisz tekst widoczny na obrazku<br/ >
                    <img src="img/captcha.php?nocache=<?php echo time(); ?>" alt=""/><br />
                    <input type="text" name="captcha" id="captcha"/></td>
            </tr><tr>
                <td> </td>
                <td><input type="submit" value="Zarejestruj"/></td>
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
    // weryfikacja hasła
    $password1 = (isset($_POST['password1'])) ? $_POST['password1'] : '';
    $password2 = (isset($_POST['password2'])) ? $_POST['password2'] : '';
    $password = ($password1 && $password1 == $password2) ?
        sha1($password1) : '';

    // weryfikacja tekstu CAPTCHA
    $captcha = (isset($_POST['captcha']) &&
        strtoupper($_POST['captcha']) == $_SESSION['captcha']);

    // jeśli wszystkie dane prawidłowe - dodanie rekordu
    if ($password &&
        $captcha &&
        User::validateUsername($_POST['username']) &&
        User::validateEmailAddr($_POST['email']))
    {
        // sprawdzenie, czy użytkownik już istnieje
        $user = User::getByUsername($_POST['username']);
        if ($user->userId)
        {
            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Przepraszamy, ' .
                'takie konto już istnieje.</strong></p> <p>Prosimy podać ' .
                'inną nazwę użytkownika.</p>';
            $GLOBALS['TEMPLATE']['content'] .= $form;
        }
        else
        {
            // utworzenie nieaktywnego rekordu użytkownika
            $u = new User();
            $u->username = $_POST['username'];
            $u->password = $password;
            $u->emailAddr = $_POST['email'];
            $token = $u->setInactive();

            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Dziękujemy za ' .
                'zarejestrowanie się.</strong></p> <p>Należy pamiętać o ' .
                'zweryfikowaniu konta i kliknąć łącze <a href="verify.php?uid=' .
                $u->userId . '&token=' . $token . '">verify.php?uid=' .
                $u->userId . '&token=' . $token . '</a></p>';
        }
    }
    // dane nieprawidłowe
    else
    {
        $GLOBALS['TEMPLATE']['content'] .= '<p><strong>Podano nieprawidłowe ' .
            'dane.</strong></p> <p>Prosimy prawidłowo wypełnić ' .
            'wszystkie pola, abyśmy mogli zarejestrować konto użytkownika.</p>';
        $GLOBALS['TEMPLATE']['content'] .= $form;
    }
}

// wyświetlenie strony
include '../templates/template.php';