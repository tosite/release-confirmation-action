FROM php:cli-alpine

COPY entrypoint.sh /entrypoint.sh
COPY actions /actions
ENTRYPOINT ["/entrypoint.sh"]
