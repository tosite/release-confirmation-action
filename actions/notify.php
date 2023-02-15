<?php

require_once 'GithubClient.php';
require_once 'SlackClient.php';

echo "==== start.        ====\n";
$params = [];
$isDebug = false;
include 'parse_params.php';

$cli = new GithubClient($params['base_url']);
$cli->setRepo($params['repo']);
$cli->setNumber($params['number']);
$isShowReleasedPulls = (int)getenv('SHOW_RELEASED_PULLS') === 1;

echo "==== fetch pulls.  ====\n";
$labels = $isShowReleasedPulls ? "{$params['merged_label']},{$params['released_label']}" : $params['merged_label'];
$queryParams = [
    'state'     => 'closed',
    'labels'    => $labels,
    'sort'      => 'updated',
    'direction' => 'desc',
    'per_page'  => 100,
];
if ($isDebug) {
    var_dump([
        'params'              => $params,
        'request_params'      => $queryParams,
        'show_released_pulls' => $isShowReleasedPulls,
        'GITHUB_TOKEN'        => getenv('GITHUB_TOKEN'),
    ]);
    $cli->setDebugMode();
}
$cli->fetchPulls($queryParams);
$unreleasedPulls = $cli->filteringUnreleasedPulls($params['merged_label']);

echo "==== slack post.   ====\n";
$slack = new SlackClient();
if ($isDebug) {
    $slack->debugMode();
}
$slack->setSubject(getenv('SUBJECT'))
    ->setMention(getenv('MENTION'))
    ->setChannel(getenv('CHANNEL'))
    ->setUnreleasedParams(getenv('UNRELEASED_PULLS_SUBJECT'), $unreleasedPulls);
if ($isShowReleasedPulls) {
    $releasedPulls = $cli->filteringReleasedPulls($params['released_label'], getenv('RELEASED_DAYS'));
    $slack->setReleasedParams(getenv('RELEASED_PULLS_SUBJECT'), $releasedPulls);
}
$slack->send();
echo "==== finish.       ====\n";
