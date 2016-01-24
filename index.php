<?php

function replace_fdf($subject, $items) {
    foreach ($items as $k => $v) {
        $subject = str_replace(
            $k,
            chr(0xFE).chr(0xFF).mb_convert_encoding($v,'UTF-16BE', 'UTF-8'),
            $subject
        );
    }
    return $subject;
}

header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));

$missing = [];
foreach ([
    'puolue' => 'Puolueen nimi (ilman ry:tä, esim. Piraattipuolue)',
    'syntynyt' => 'Syntymäaika ISO-8601-muodossa (esim. 1983-03-28)',
    'etunimet' => 'Etunimet (esim. Ville Petteri)',
    'sukunimi' => 'Sukunimi (esim. Virtanen)',
    'selvennys' => 'Nimenselvennys (esim. Ville Virtanen)',
    'kotikunta' => 'Henkilön kotikunta',
    'paikka' => 'Allekirjoituspaikka (oletuksena sama kuin kotikunta)'
] as $key => $help) {
    if (array_key_exists($key, $_GET)) continue;
    $missing[$key] = $help;
}

if (count($missing) !== 0) {
    http_response_code(400);
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <title>Puolueen kannattajakorttien muodostaja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Puolueen kannattajakorttien muodostaja</h1>
    <p>Tällä palvelulla voi muodostaa puolueen kannattajakortteja koneellisesti. Seuraavat GET-muuttujat puuttuvat pyynnöstä: </p>
    <table>
        <tr><th>Muuttuja</th><th>Selite</th></tr>
<?php
    foreach ($missing as $key => $help) {
        echo '        <tr><td>'.$key.'</td><td>'.$help."</td></tr>\n";
    }
?>
    </table>
    <address>
        Palvelun on toteuttanut <a href="http://zouppen.iki.fi">Joel
        Lehtonen</a> Piraattipuolueen kannattajakorttien keräilyä varten.
        Palvelua saa käyttää koneellisesti muilta sivuilta ja myös muiden
        puolueiksi pyrkivien yhdistysten toimesta. Älkää sikailko, bannia
        tulee. <a href="https://github.com/zouppen/kannattaja">Lähdekoodit</a>
        löytyvät GitHubista.
    </address>
</body>
</html>
<?php
    exit(0);
}

$now = time();

// Fill form with data array
$tmpfile = tempnam(sys_get_temp_dir(), '');
file_put_contents($tmpfile, replace_fdf(file_get_contents(__DIR__.'/template.fdf'), [    
    '$PUOLUE' => strtoupper($_GET['puolue']).' RY',
    '$YB' => substr($_GET['syntynyt'], 0, 4),
    '$MB' => substr($_GET['syntynyt'], 5, 2),
    '$DB' => substr($_GET['syntynyt'], 8, 2),
    '$ETUNIMI' => $_GET['etunimet'],
    '$SUKUNIMI' => $_GET['sukunimi'],
    '$KOKONIMI' => $_GET['selvennys'],
    '$KOTIKUNTA' => $_GET['kotikunta'],
    '$PAIKKA' => $_GET['paikka'],
    '$YN' => date('y',$now),
    '$MN' => date('m',$now),
    '$DN' => date('d',$now),
]));

header('Content-type: application/pdf');
passthru('pdftk '.__DIR__.'/original.pdf fill_form '.$tmpfile.' output -');
unlink($tmpfile);
