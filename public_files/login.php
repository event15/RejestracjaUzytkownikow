<?php
// dołączenie kodu współużytkowanego
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// rozpoczęcie lub dołączenie do sesji
session_start();
header('Cache-control: private');

// logowanie, jeśli ustawiono zmienną login
if (isset($_GET['login']))
{
    if (isset($_POST['username']) && isset($_POST['password']))
    {
        // odczytanie rekordu użytkownika
        $user = (User::validateUsername($_POST['username'])) ?
            User::getByUsername($_POST['username']) : new User();

        if ($user->userId && $user->password == sha1($_POST['password']))
        {
            // zapisanie wartości w sesji, aby móc śledzić użytkownika
            // i przekierować go do strony głównej
            $_SESSION['access'] = TRUE;
            $_SESSION['userId'] = $user->userId;
            $_SESSION['username'] = $user->username;
            header('Location: main.php');
        }
        else
        {
            // nieprawidłowy użytkownik i (lub) hasło
            $_SESSION['access'] = FALSE;
            $_SESSION['username'] = null;
            header('Location: 401.php');
        }
    }
    // brak danych uwierzytelniających
    else
    {
        $_SESSION['access'] = FALSE;
        $_SESSION['username'] = null;
        header('Location: 401.php');
    }
    exit();
}

// wylogowanie, jeśli ustawiono zmienną logout
// (wyczyszczenie danych sesji prowadzi do wylogowania użytkownika)
else if (isset($_GET['logout']))
{
    if (isset($_COOKIE[session_name()]))
    {
        setcookie(session_name(), '', time() - 42000, '/');
    }

    $_SESSION = array();
    session_unset();
    session_destroy();
}

// wygenerowanie formularza logowania
ob_start();
?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?login"
          method="post">
        <table>
            <tr>
                <td><label for="username">Nazwa użytkownika</label></td>
                <td><input type="text" name="username" id="username"/></td>
            </tr><tr>
                <td><label for="password">Hasło</label></td>
                <td><input type="password" name="password" id="password"/></td>
            </tr><tr>
                <td> </td>
                <td><input type="submit" value="Zaloguj"/></td>
            </tr>
        </table>
    </form>
<?php
$GLOBALS['TEMPLATE']['content'] = ob_get_clean();

// wyświetlenie strony
include '../templates/template.php';