<?php
require_once 'vendor/autoload.php';

function createSessionToken()
{
    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->exp = time() + 3600;
    $aJWT->iss = 'login';
    $aJWT->uot = true;
    $aJWT->addAllowedSection(['lgn']);
    $jwtToken = $aJWT->createToken();

    \_log("jwtToken: $jwtToken");
    return $jwtToken;
}

function destroySessionToken()
{
    $sessionToken = $_COOKIE['sessionToken'];

    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->sendToBin($sessionToken);
}

$context = [];

\YeAPF\WebApp::setUselessURILevel(0);
$uri = \YeAPF\WebApp::getURI();
if (strlen($uri)==0) {
    \YeAPF\WebApp::setURI('login');
}
preg_match('/[a-zA-Z_]{1,}[a-z0-9A-Z_]*/', $uri, $output_array);
if ($output_array) {
    $first = $output_array[0];
    switch($first) {
        case 'xdbg':
            xdebug_info();
            exit;
            break;
        case 'login':
            $context['sessionToken'] = createSessionToken();
            break;

        case 'logout':
            destroySessionToken();
            break;
    }
}

\YeAPF\WebApp::go($context, 'x'.date("YmdHi"));
