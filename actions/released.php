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
// Clone object
$cli2 = clone $cli;
if ($isDebug) {
    var_dump([
        'params'       => $params,
        'GITHUB_TOKEN' => getenv('GITHUB_TOKEN'),
    ]);
    $cli->setDebugMode();
    $cli2->setDebugMode();
}
echo "==== remove label. ====\n";
$cli->removeLabel($params['merged_label']);

echo "==== add label.    ====\n";
$cli2->addLabels($params['released_label']);

echo "==== finish.       ====\n";
