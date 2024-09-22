<?php
/*
 * Точка входа в приложение
 */

use App\Http\Request;
use App\Http\SuccessfulResponse;
use App\Http\ErrorResponse;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

$request = new Request($_GET, $_SERVER);

//$parameter = $request->query('param');
//$header = $request->header('Some-Header');
$path = $request->path();

//$response = new SuccessfulResponse([
//    'message' => 'Successfull response'
//]);

$response = new ErrorResponse('Error response');

$response->send();