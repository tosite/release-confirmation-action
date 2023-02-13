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
$queryParams = [
    'state'    => 'closed',
    'labels'   => $params['merged_label'],
    'per_page' => 100,
];

if ($isDebug) {
    var_dump([
        'params'         => $params,
        'request_params' => $queryParams,
        'GITHUB_TOKEN'   => getenv('GITHUB_TOKEN'),
    ]);
    $cli->setDebugMode();
}

echo "==== fetch pulls. ====\n";
$res = $cli->fetchUnreleasedPulls($queryParams, $params['merged_label'], $params['released_label']);
var_dump($res);
echo "==== finish.       ====\n";
