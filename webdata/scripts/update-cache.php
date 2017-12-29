<?php

include(__DIR__ . '/../init.inc.php');
while (true) {
    $round = Cell::search(1)->min('count')->count;
    KeyValue::set('cache_fill_round', $round);
    KeyValue::set('cache_count', count(Cell::search(1)));
    KeyValue::set('cache_count_todo', count(Cell::search(array('count' => $round))));
    sleep(5);
}
