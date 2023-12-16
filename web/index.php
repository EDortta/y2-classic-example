<?php
require_once 'vendor/autoload.php';

function createSessionToken($section=['lgn'])
{
    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->exp = time() + 3600;
    $aJWT->iss = 'login';
    $aJWT->uot = true;
    $aJWT->addAllowedSection($section);
    $jwtToken = $aJWT->createToken();
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
$uri = \YeAPF\WebApp::getURI('login');
$first = \YeAPF\WebApp::splitURI()[0];
switch ($first) {
    case 'xdbg':
        xdebug_info();
        exit;
        break;
    case 'login':
        if (\YeAPF\WebApp::clientExpectJSON()) {
            $request = \YeAPF\WebApp::getRequest();
            if ($request['username']=='admin' && $request['password']=='admin') {
                destroySessionToken();
                $context['sessionToken'] = createSessionToken(['adm']);
                setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');
            } else {
                http_response_code(401);
                $context['error'] = 'Invalid username or password';
            }
        } else {
            $context['sessionToken'] = createSessionToken();
            setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');
        }        
        break;
    case 'logout':
        destroySessionToken();
        break;
}

// echo '<pre>';
// echo "uri = '$uri'\n";
// print_r($context);
// print_r($uri_path);
// print_r(\YeAPF\WebApp::splitURI());
// print_r(\YeAPF\WebApp::getRequest());
// echo '</pre>';

// die ();
\YeAPF\WebApp::go($context, 'x' . date('YmdHi'));
