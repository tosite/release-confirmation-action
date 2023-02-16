<?php

require_once 'GithubClient.php';
require_once 'SlackClient.php';

echo "==> start notify action.\n";
$params = [];
$isDebug = false;
include 'parse_params.php';

$repos = explode(',', getenv('TARGET_REPOSITORIES'));
$cli = new GithubClient($params['base_url']);
$releasedPulls = [];
$unreleasedPulls = [];
$isShowReleasedPulls = (int)getenv('SHOW_RELEASED_PULLS') === 1;

if ($isDebug) {
    echo "[DEBUG]show repos:\n";
    var_dump($repos);
    $cli->setDebugMode();
}

echo "==> fetch pull requests.\n";
foreach ($repos as $repo) {
    echo "==> repo: `{$repo}`.\n";
    $cli->setRepo($params['repo']);
    $cli->setNumber($params['number']);
    $labels = $isShowReleasedPulls ? "{$params['merged_label']},{$params['released_label']}" : $params['merged_label'];
    $queryParams = [
        'state'     => 'closed',
        'labels'    => $labels,
        'sort'      => 'updated',
        'direction' => 'desc',
        'per_page'  => 100,
    ];
    if ($isDebug) {
        echo "[DEBUG]show params:\n";
        var_dump([
            'params'              => $params,
            'request_params'      => $queryParams,
            'show_released_pulls' => $isShowReleasedPulls,
            'GITHUB_TOKEN'        => getenv('GITHUB_TOKEN'),
        ]);
    }
    $cli->fetchPulls($queryParams);
    $unreleasedPulls[$repo] = $cli->filteringUnreleasedPulls($params['merged_label']);
    $releasedPulls[$repo] = $cli->filteringReleasedPulls($params['released_label'], getenv('RELEASED_DAYS'));
}

echo "==> post to Slack.\n";
$slack = new SlackClient();
if ($isDebug) {
    $slack->debugMode();
}
$slack->setSubject(getenv('SUBJECT'))
    ->setMention(getenv('MENTION'))
    ->setUnreleasedParams(getenv('UNRELEASED_PULLS_SUBJECT'), $unreleasedPulls);
if ($isShowReleasedPulls) {
    $slack->setReleasedParams(getenv('RELEASED_PULLS_SUBJECT'), $releasedPulls);
}
$slack->send();
echo "==> finish notify action.\n";
