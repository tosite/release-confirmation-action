<?php

require_once 'client.php';
$isDebug = (int)getenv('DEBUG_MODE') === 1;

echo "==== start.        ====\n";

$keys = [
    'endpoint',
    'action',
    'base_url',
    'repo',
    'number',
    'merged_label',
    'released_label',
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

echo "==== add label.    ====\n";
$cli->addLabels($params['merged_label']);
echo "==== finish.       ====\n";
