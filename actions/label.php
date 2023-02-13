<?php

require_once 'client.php';
$isDebug = (int)getenv('DEBUG_MODE') === 1;

echo "==== add label. ====\n";

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
$cli->setRepo($params['repo']);
$cli->setNumber($params['number']);
if ($isDebug) {
    var_dump([
        'params'       => $params,
        'GITHUB_TOKEN' => getenv('GITHUB_TOKEN'),
    ]);
    $cli->setDebugMode();
}

echo "==== post api.  ====\n";
$cli->addLabels($params['merged_label'], $params['merged_color']);
echo "==== finish.    ====\n";
