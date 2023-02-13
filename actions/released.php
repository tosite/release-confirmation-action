<?php

require_once 'client.php';

echo "==== start.        ====\n";
include 'parse_params.php';

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

echo "==== remove label. ====\n";
$cli->removeLabel($params['merged_label']);

echo "==== add label.    ====\n";
$cli = new GithubClient($params['base_url']);
$cli->setRepo($params['repo']);
$cli->setNumber($params['number']);
if ($isDebug) {
    $cli->setDebugMode();
}
$cli->addLabels($params['released_label']);
echo "==== finish.       ====\n";
