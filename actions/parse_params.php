<?php
$isDebug = (int)getenv('DEBUG_MODE') === 1;
$params['merged_label'] = getenv('MERGED_LABEL');
$params['released_label'] = getenv('RELEASED_LABEL');
$params['slack_subject'] = getenv('SUBJECT');
$params['mention'] = getenv('MENTION');
$params['base_url'] = getenv('GITHUB_BASE_URL');
$params['repo'] = getenv('GITHUB_REPO');
$params['number'] = getenv('GITHUB_PULL_NUMBER');

if ($isDebug) {
    echo "[DEBUG]show params:\n";
    var_dump($params);
}
