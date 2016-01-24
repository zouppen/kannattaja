<?php

function replace_fdf($subject, $items) {
    foreach ($items as $k => $v) {
        $subject = str_replace(
            $k,
            chr(0xFE).chr(0xFF).mb_convert_encoding(preg_replace('/[()$]/','', $v),'UTF-16BE', 'UTF-8'),
            $subject
        );
    }
    return $subject;
}

$now = time();
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('tomorrow', $now)));
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: *');

$missing = [];
foreach ([
    'puolue' => 'Puolueen nimi (ilman ry:tä, esim. Piraattipuolue)',
    'syntynyt' => 'Syntymäaika ISO-8601-muodossa (esim. 1983-03-28)',
    'etunimet' => 'Etunimet (esim. Ville Petteri)',
    'sukunimi' => 'Sukunimi (esim. Virtanen)',
    'selvennys' => 'Nimenselvennys (esim. Ville Virtanen)',
    'kotikunta' => 'Henkilön kotikunta',
    'paikka' => 'Allekirjoituspaikka (ei pakollinen, oletuksena sama kuin kotikunta)'
] as $key => $help) {
    if (array_key_exists($key, $_GET)) continue;
    $missing[$key] = $help;
}

// FIXME Not very proud of this, should use proper argument parser.
if (count($missing) !== 0 && !(count($missing) === 1 && array_key_exists('paikka', $missing))) {
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
    <p>
        Tällä palvelulla voi muodostaa haluamasi puolueen kannattajakortteja
        koneellisesti. Rajapinta sijaitsee tässä osoitteessa eli
        <tt>https://kannatus.liittovaltio.fi/</tt>. Käytä UTF-8 -koodausta
        nimissä. Katso tarvittaessa
        <a href="?puolue=Piraattipuolue&syntynyt=1983-03-28&etunimet=Ville%20Petteri&sukunimi=Virtanen&selvennys=Ville%20Virtanen&kotikunta=Lepp%C3%A4virta">esimerkki</a>.
        Tarvitset vielä seuraavat GET-muuttujat mukaan pyyntöön:
    </p>
    <table>
        <tr><th>Muuttuja</th><th>Selite</th></tr>
<?php
    foreach ($missing as $key => $help) {
        echo '        <tr><td>'.$key.'</td><td>'.$help."</td></tr>\n";
    }
?>
    </table>
    <address>
        Palvelun on toteuttanut <a href="https://zouppen.iki.fi">Joel
        Lehtonen</a> Piraattipuolueen kannattajakorttien keräilyä varten.
        Palvelua saa käyttää koneellisesti muilta sivuilta ja minkä tahansa
        puolueeksi pyrkivän yhdistyksen toimesta. Älkää sikailko, bannia
        tulee. <a href="https://github.com/zouppen/kannattaja">Lähdekoodit</a>
        löytyvät GitHubista.
    </address>
</body>
</html>
<?php
    exit(0);
}

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
    '$PAIKKA' => $_GET[array_key_exists('paikka', $_GET) ? 'paikka' : 'kotikunta'],
    '$YN' => date('y',$now),
    '$MN' => date('m',$now),
    '$DN' => date('d',$now),
]));

header('Content-type: application/pdf');
passthru('pdftk '.__DIR__.'/original.pdf fill_form '.$tmpfile.' output -');
unlink($tmpfile);
