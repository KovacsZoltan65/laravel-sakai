#!/bin/bash
echo "♻️  Visszaállítás indítása..."

if [ -d ".backup_npm" ]; then
  cp .backup_npm/package.json .
  cp .backup_npm/package-lock.json .
  rm -rf node_modules
  cp -r .backup_npm/node_modules .
  echo "✅ Visszaállítva az eredeti állapot."
else
  echo "⚠️  Nincs mentés. Előbb futtasd le a backup.sh-t!"
fi