<?php

function EA($obj, $ex = true): void
{
    echo '<pre>';
    print_r($obj);
    echo '</pre>';

    if ($ex) {
        die;
    }
}