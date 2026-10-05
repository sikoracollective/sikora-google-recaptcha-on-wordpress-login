#!/usr/bin/env bash
set -euo pipefail

PLUGIN_SLUG="sikora-google-recaptcha-on-wordpress-login"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DIST_ZIP="${ROOT_DIR}/${PLUGIN_SLUG}.zip"
TEMP_DIR="$(mktemp -d "${TMPDIR:-/tmp}/${PLUGIN_SLUG}-build.XXXXXX")"
STAGE_DIR="${TEMP_DIR}/${PLUGIN_SLUG}"

cleanup() {
	rm -rf "${TEMP_DIR}"
}
trap cleanup EXIT

mkdir -p "${STAGE_DIR}"

# files/directories included in the installable plugin zip
INCLUDE_PATHS=(
	"sikora-google-recaptcha-on-wordpress-login.php"
	"includes"
	"uninstall.php"
	"readme.txt"
)

echo "Building ${PLUGIN_SLUG}.zip"
echo "Files included in the zip:"

file_count=0

for path in "${INCLUDE_PATHS[@]}"; do
	src="${ROOT_DIR}/${path}"
	if [[ ! -e "${src}" ]]; then
		echo "error: missing required path: ${path}" >&2
		exit 1
	fi

	cp -R "${src}" "${STAGE_DIR}/"

	if [[ -d "${src}" ]]; then
		# list each file under directories so the build output is explicit
		while IFS= read -r -d '' file; do
			rel="${file#${STAGE_DIR}/}"
			echo "  ${rel}"
			file_count=$((file_count + 1))
		done < <(find "${STAGE_DIR}/${path}" -type f -print0 | sort -z)
	else
		echo "  ${path}"
		file_count=$((file_count + 1))
	fi
done

rm -f "${DIST_ZIP}"

(
	cd "${TEMP_DIR}"
	zip -r "${DIST_ZIP}" "${PLUGIN_SLUG}" >/dev/null
)

echo "Added ${file_count} files to the zip."
echo "Created: ${PLUGIN_SLUG}.zip"
# temp dir removed by trap on exit
