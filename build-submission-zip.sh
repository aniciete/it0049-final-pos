#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PARENT_DIR="$(dirname "$DIR")"
FOLDER_NAME="$(basename "$DIR")"
OUTPUT_ZIP="$DIR/it0049-final-pos-submission.zip"

echo "==> Building clean Canvas submission ZIP (excluding node_modules, vendor, .git, secrets)..."
cd "$DIR"
if [ -d "$DIR/node_modules" ] && [ -f "$DIR/package.json" ]; then
    npm run build > /dev/null
fi

rm -f "$OUTPUT_ZIP"

cd "$PARENT_DIR"
zip -r "$OUTPUT_ZIP" "$FOLDER_NAME" \
  -x "$FOLDER_NAME/node_modules/*" \
     "$FOLDER_NAME/vendor/*" \
     "$FOLDER_NAME/.git/*" \
     "$FOLDER_NAME/.env" \
     "$FOLDER_NAME/.env.production" \
     "$FOLDER_NAME/env.production" \
     "$FOLDER_NAME/.deploy.env" \
     "$FOLDER_NAME/.extract_token" \
     "$FOLDER_NAME/*.zip" \
     "$FOLDER_NAME/.phpunit.result.cache" \
     "$FOLDER_NAME/public/storage" \
     "*.DS_Store" > /dev/null

echo "==> Clean submission ZIP ready: $OUTPUT_ZIP"
ls -lh "$OUTPUT_ZIP"
