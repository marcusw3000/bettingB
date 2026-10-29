#!/usr/bin/env sh
set -eu

# The project public entry point and generated SPA assets live at repository
# root, while Laravel itself lives in ./casino.
exec php -S "0.0.0.0:${PORT:-8080}" -t . router.local.php
