#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$DIR/.deploy.env" ]; then
    set -a
    source "$DIR/.deploy.env"
    set +a
fi

python3 "$DIR/scripts/deploy_infinityfree.py"
