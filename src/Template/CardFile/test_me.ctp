<?php

use App\Lib\Wildcard;

$a = array(1,2,3,4,5,6,7,8,9,10,11,12);

$arbitrary = [ 3, 7, 5, 6, 2, 1, 11, 12, 10, 8, 4, 9];
$arbitrary_order = array_flip($arbitrary);

usort($a, function ($a, $b) use ($arbitrary_order){
    if ($arbitrary_order[$a] == $arbitrary_order[$b]) { return 0; }
    return ($arbitrary_order[$a] < $arbitrary_order[$b]) ? -1 : 1;
});


foreach ($a as $key => $value) {
    echo "<p>$key: $value</p>";
}
