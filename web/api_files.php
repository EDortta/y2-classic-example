<?php

/**
 * You can test this with curl, for example
 * curl -X GET http://localhost:8183/api/files/list
 * 
 * Insomnia can be used to test it too.
 */

\YeAPF\WebApp::setRouteHandler(
    preg_quote('/api/files/list', '/'),
    'GET',
    function (\YeAPF\IBulletin &$bulletin, $uri, &$context) {
        $ret=200;

        $filesList = glob('files/*.json');
        $filesInfo = [];

        foreach ($filesList as $file) {
            $content = json_decode(file_get_contents($file), true);
            $filesInfo[] = $content;
        }
        $bulletin -> setContentType('application/json');
        $bulletin -> setJsonString(json_encode($filesInfo));

        return $ret;
    }
);
