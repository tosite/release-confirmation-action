#!/bin/sh -l

echo ${GITHUB_TOKEN}
php "/actions/$1.php" $@
