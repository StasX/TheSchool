FROM redis:alpine

RUN apk update \
    && apk upgrade \
    && rm -rf /var/cache/apk/*

HEALTHCHECK --interval=10s --timeout=5s --retries=5 --start-period=15s \
    CMD redis-cli ping || exit 1
