<?php

require_once 'GithubClient.php';

echo "==> start released action.\n";
$params = [];
$isDebug = false;
include 'parse_params.php';

$cli = new GithubClient($params['base_url']);
$cli->setRepo($params['repo']);
$cli->setNumber($params['number']);
if ($isDebug) {
    $cli->setDebugMode();
}

echo "==> remove `{$params['merged_label']}` label.\n";
$cli->removeLabel($params['merged_label']);

echo "==> add {$params['released_label']} label.\n";
$cli->addLabels($params['released_label']);
echo "==> finish released action.\n";
