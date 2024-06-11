<?php
require_once 'vendor/autoload.php';

function createSessionToken($section = ['lgn'])
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

if (file_exists('api.php'))
    require_once 'api.php';

$first = \YeAPF\WebApp::splitURI()[0];
switch ($first) {
    case 'xdbg':
        xdebug_info();
        exit;
        break;
    case 'login':
        /*
         * This is the easiest and shortest way to
         * deal with a route and a request with the same name.
         * If the client is expecting JSON, then we will
         * treat it as a request. If not, then it will
         * be treated as a route.
         */
        if (\YeAPF\WebApp::clientExpectJSON()) {
            $request = \YeAPF\WebApp::getRequest();

            /*
             * Your access control file in this sample is in '.secure/password.json'
             * You can change it to whatever you want.
             * In this sample, we expect to have a JSON with this structure:
             * [
             *    {
             *        "username": "user1",
             *        "password": "Qw3rTy!@#$"
             *    },
             *    {
             *        "username": "user2",
             *        "password": "Zx9Cv8Bn6M"
             *    },
             *    {
             *        "username": "user3",
             *        "password": "Lk2Jh4Fg5D"
             *    }
             * ]
             */
            if (file_exists('.secure/password.json')) {
                $passwordFile = json_decode(file_get_contents('.secure/password.json'), true);
            } else {
                $passwordFile = [];
            }

            $userLogged=false;

            if (isset($request['username']) && isset($request['password']) && isset($passwordFile[$request['username']])) {

                if ($passwordFile[$request['username']]['password'] == $request['password']) {
                    destroySessionToken();
                    $context['sessionToken'] = createSessionToken(['adm']);
                    setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');
                    $userLogged = true;
                }
            }
            $context['__json'] = json_encode([ 
                'userLogged' => $userLogged,
                'sessionToken' => $context['sessionToken']??null                
            ]);
            if (!$userLogged) {
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
// print_r(\YeAPF\WebApp::splitURI());
// print_r(\YeAPF\WebApp::getRequest());
// echo '</pre>';
// die ();

\YeAPF\WebApp::go($context, 'x' . date('YmdHi'));
