<?php

require_once 'client.php';
$isDebug = (int)getenv('DEBUG_MODE') === 1;

echo "==== add label. ====";

$keys = [
    'endpoint',
    'action',
    'base_url',
    'repo',
    'number',
    'merged_label',
    'released_label',
    'merged_color',
    'released_color',
];
$params = array_combine($keys, $argv);
$cli = new GithubClient($params['base_url']);
if ($isDebug) {
    var_dump([
        'params'       => $params,
        'GITHUB_TOKEN' => getenv('GITHUB_TOKEN'),
    ]);
    $cli->setDebugMode();
}

echo "==== post api.  ====";
$cli->addLabels($params['merged_label'], $params['merged_color']);
echo "==== finish.    ====";
