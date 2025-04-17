#!/bin/bash
echo "🔒 Készül a biztonsági mentés..."

mkdir -p .backup_npm
cp package.json .backup_npm/
cp package-lock.json .backup_npm/
cp -r node_modules .backup_npm/

echo "✅ Mentés kész (.backup_npm mappába)"
