<?php
$isDebug = (int)getenv('DEBUG_MODE') === 1;
$keys = [
    'endpoint',
    'action',
    'base_url',
    'repo',
    'number',
];
var_dump(['keys' => $keys, 'values' => $argv]);
$params = array_combine($keys, $argv);
$params['merged_label'] = getenv('MERGED_LABEL');
$params['released_label'] = getenv('RELEASED_LABEL');
$params['slack_subject'] = getenv('SUBJECT');
$params['mention'] = getenv('MENTION');
