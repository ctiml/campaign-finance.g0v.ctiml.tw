<?php

include(__DIR__ . '/../init.inc.php');
while (true) {
    $round = Cell::search(1)->min('count')->count;
    $values = array(
        'cache_fill_round' => $round,
        'cache_count' => count(Cell::search(1)),
        'cache_count_todo' => count(Cell::search(arraY('count' => $round))),
    );
    foreach ($values as $k => $v) {
        KeyValue::set($k, $v);
    }
    error_log(json_encode($values));
    sleep(5);
}
