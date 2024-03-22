<?php

function EA($obj, $ex = false): void
{
    echo '<pre>';
    print_r($obj);
    echo '</pre>';

    if ($ex) {
        die;
    }
}