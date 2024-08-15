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
        $ret = 200;

        $filesList = glob('files/*.json');
        $filesInfo = [];

        foreach ($filesList as $file) {
            $content = json_decode(file_get_contents($file), true);
            $filesInfo[] = $content['file'];
        }
        $bulletin->setContentType('application/json');
        $bulletin->setJsonString(json_encode($filesInfo));

        return $ret;
    }
);

function replace_extension($filename, $new_extension)
{
    $info = pathinfo($filename);
    return $info['filename'] . '.' . $new_extension;
}

/**
 * This example shows how to get info about a file given a filename
 * that is picked from the list of files.
 *
 * Pay attention that in the URL is being defined a filename as an STRING
 * That means that any character outside the [STRING] definition are not
 * allowed increasing the security of your API entrypoint.
 *
 * Any parameter defined as this, need to be present at the function that
 * implement the route in the chosed method.
 */
\YeAPF\WebApp::setRouteHandler(
    preg_quote('/api/files/get/info/{{filename::STRING}}', '/'),
    'GET',
    function (\YeAPF\IBulletin &$bulletin, $uri, &$context, $filename) {
        $filename = replace_extension($filename, 'json');

        if (file_exists("files/$filename")) {
            $ret = 200;
            $content = json_decode(file_get_contents("files/$filename"), true);
            $bulletin->setContentType('application/json');
            $bulletin->setJsonString(json_encode($content));
        } else {
            $ret = 404;
        }

        return $ret;
    }
);

/**
 * This example shows how to download a file given a filename
 *
 * Pay attention that bulletin->setBinaryFile already checks if the file exists
 * and if it is readable. Further, it also checks if the file is a file and set
 * the correct content type and return length.
 */
\YeAPF\WebApp::setRouteHandler(
    preg_quote('/api/files/get/{{filename::STRING}}', '/'),
    'GET',
    function (\YeAPF\IBulletin &$bulletin, $uri, &$context, $filename) {
        if (file_exists("files/$filename")) {
            $ret = 200;
            $bulletin->setBinaryFile("files/$filename");
        } else {
            $ret = 404;
        }

        return $ret;
    }
);
