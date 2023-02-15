<?php

require_once 'client.php';
require_once 'slack.php';

echo "==== start.        ====\n";
$params = [];
$isDebug = false;
include 'parse_params.php';

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

echo "==== fetch pulls.  ====\n";
$unreleasedPulls = $cli->fetchUnreleasedPulls($queryParams, $params['merged_label'], $params['released_label']);

echo "==== slack post.   ====\n";
$slack = new SlackClient();
if ($isDebug) {
    $slack->debugMode();
}
$slack->setSubject(getenv('SUBJECT'))
    ->setMention(getenv('MENTION'))
    ->setChannel(getenv('CHANNEL'))
    ->setUnreleasedParams('UNRELEASED_PULLS_SUBJECT', $unreleasedPulls);
if (getenv('SHOW_RELEASED_PULLS')) {
    $slack->setReleasedParams(getenv('RELEASED_PULLS_SUBJECT'), []);
}
$slack->send();
echo "==== finish.       ====\n";
