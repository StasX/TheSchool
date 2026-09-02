FROM redis:alpine

RUN RUN apk upgrade --no-cache

HEALTHCHECK --interval=10s --timeout=5s --retries=5 --start-period=15s \
    CMD redis-cli ping || exit 1

USER redis
