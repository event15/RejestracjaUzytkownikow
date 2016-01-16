<?php
// zwrócenie ciągu losowych znaków o określonej długości
function random_text($count, $rm_similar = false)
{
    // utworzenie listy znaków
    $chars = array_flip(array_merge(range(0, 9), range('A', 'Z')));

    // usunięcie podobnie wyglądających znaków, aby uniknąć pomyłek
    if ($rm_similar)
    {
        unset($chars[0], $chars[1], $chars[2], $chars[5], $chars[8],
            $chars['B'], $chars['I'], $chars['O'], $chars['Q'],
            $chars['S'], $chars['U'], $chars['V'], $chars['Z']);
    }

    // wygenerowanie ciągu losowych znaków
    for ($i = 0, $text = ''; $i < $count; $i++)
    {
        $text .= array_rand($chars);
    }

    return $text;
}
?>