#!/usr/bin/env bash
# Builds sikorix-image-upload-manager.zip with readme.txt and PHP files only.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
SLUG="sikorix-image-upload-manager"
BUILD_DIR="${ROOT}/build/${SLUG}"
ZIP_PATH="${ROOT}/${SLUG}.zip"

rm -rf "${ROOT}/build"
mkdir -p "${BUILD_DIR}/includes"

cp "${ROOT}/readme.txt" "${BUILD_DIR}/"
cp "${ROOT}/sikorix-image-upload-manager.php" "${BUILD_DIR}/"
cp "${ROOT}/uninstall.php" "${BUILD_DIR}/"
cp "${ROOT}/includes/"*.php "${BUILD_DIR}/includes/"

FILE_COUNT="$(find "${BUILD_DIR}" -type f | wc -l | tr -d '[:space:]')"
echo "Including ${FILE_COUNT} files:"
(
	cd "${BUILD_DIR}"
	find . -type f | sed 's|^\./||' | sort | sed 's|^|  |'
)

rm -f "${ZIP_PATH}"
(
	cd "${ROOT}/build"
	zip -qr "${ZIP_PATH}" "${SLUG}"
)

rm -rf "${ROOT}/build"

echo "Installable WordPress plugin: ${SLUG}.zip"
