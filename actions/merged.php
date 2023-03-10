<?php

require_once 'GithubClient.php';

echo "==> start merged action.\n";
$params = [];
$isDebug = false;
include 'parse_params.php';

$cli = new GithubClient($params['base_url']);
$cli->setRepo($params['repo']);
$cli->setNumber($params['number']);
if ($isDebug) {
    $cli->setDebugMode();
}

echo "==> add `{$params['merged_label']}` label.\n";
$cli->addLabels($params['merged_label']);
echo "==> finish merged action.\n";
