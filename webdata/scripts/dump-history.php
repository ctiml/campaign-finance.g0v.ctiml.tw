<?php

include(__DIR__ . '/../init.inc.php');

Pix_Table::$_save_memory = true;
Pix_Table::addStaticResultSetHelper('Pix_Array_Volume');

$output = fopen('php://output', 'w');
fputcsv($output, array('page', 'row', 'col', 'ans', 'user_id', 'time'));
foreach (CellHistory::search(1)->order('id')->volumemode(100000) as $ch) {
    fputcsv($output, array(
        $ch->page,
        $ch->x,
        $ch->y,
        $ch->ans,
        crc32($ch->client_ip . strval(getenv(IP_CLOAK_SECRET))),
        $ch->created,
    ));
}
