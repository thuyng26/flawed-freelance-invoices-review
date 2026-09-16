#!/bin/sh

if [ "${DEPLOY_CHECK_FAIL:-0}" = "1" ]; then
    echo "deployment check failed: forced failure for verification" >&2
    exit 1
fi

echo "deployment check passed"
