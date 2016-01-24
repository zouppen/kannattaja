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

// UTF-8 support for uppercase
mb_internal_encoding("UTF-8");

$now = time();
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('tomorrow', $now)));
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: *');

foreach ([
    'party',
    'fname',
    'lname',
    'clarification',
    'bday',
    'city',
    'location',
] as $key) {
    if (array_key_exists($key, $_GET)) continue;
    http_response_code(400);
    header('Content-type: text/plain');
    print("Missing field: ".$key."\n");
    exit(0);
}

// Fill form with data array
$tmpfile = tempnam(sys_get_temp_dir(), '');
file_put_contents($tmpfile, replace_fdf(file_get_contents(__DIR__.'/template.fdf'), [    
    '$PUOLUE' => strtoupper($_GET['party']).' RY',
    '$YB' => substr($_GET['bday'], 0, 4),
    '$MB' => substr($_GET['bday'], 5, 2),
    '$DB' => substr($_GET['bday'], 8, 2),
    '$ETUNIMI' => mb_strtoupper($_GET['fname']),
    '$SUKUNIMI' => mb_strtoupper($_GET['lname']),
    '$KOKONIMI' => mb_strtoupper($_GET['clarification']),
    '$KOTIKUNTA' => mb_strtoupper($_GET['city']),
    '$PAIKKA' => mb_strtoupper($_GET['location']),
    '$YN' => date('y',$now),
    '$MN' => date('m',$now),
    '$DN' => date('d',$now),
]));

header('Content-type: application/pdf');
passthru('pdftk '.__DIR__.'/original.pdf fill_form '.$tmpfile.' output - flatten');
unlink($tmpfile);
