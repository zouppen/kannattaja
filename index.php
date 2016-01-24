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

function filenamize($input) {
    return preg_replace('/[^a-z0-9-_]/','', str_replace(' ', '_', strtolower(iconv("UTF-8", "ASCII//TRANSLIT", $input))));
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
//header('Content-Disposition: attachment; filename="kannattajakortti-'.filenamize($_GET['kokonimi']).'.pdf"');
passthru('pdftk '.__DIR__.'/original.pdf fill_form '.$tmpfile.' output -');
unlink($tmpfile);
