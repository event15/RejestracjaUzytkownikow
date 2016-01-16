<?php
// dołączenie kodu współużytkowanego
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// sprawdzenie, czy otrzymano identyfikator użytkownika i znacznik
if (!isset($_GET['uid']) || !isset($_GET['token']))
{
    $GLOBALS['TEMPLATE']['content'] = '<p><strong>Otrzymane informacje ' .
        'są niepełne.</strong></p> <p>Prosimy spróbować ponownie.</p>';
    include '../templates/template.php';
    exit();
}

// weryfikacja identyfikatora użytkownika
if (!$user = User::getById($_GET['uid']))
{
    $GLOBALS['TEMPLATE']['content'] = '<p><strong>Podane konto nie istnieje.</strong>' .
        '</p> <p>Prosimy spróbować ponownie.</p>';
}
// upewnienie się, że konto jest nieaktywne
else
{
    if ($user->isActive)
    {
        $GLOBALS['TEMPLATE']['content'] = '<p><strong>Konto ' .
            'zostało już zweryfikowane.</strong></p>';
    }
    // uaktywnienie konta
    else
    {
        if ($user->setActive($_GET['token']))
        {
            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Dziękujemy ' .
                'za zweryfikowanie konta.</strong></p> <p>Można się ' .
                'teraz <a href="login.php">zalogować</a>.</p>';
        }
        else
        {
            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Podano ' .
                'nieprawidłowe dane.</strong></p> <p>Prosimy spróbować ponownie.</p>';
        }
    }
}

// wyświetlenie strony
include '../templates/template.php';