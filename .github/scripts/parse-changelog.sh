#!/usr/bin/env bash
set -euo pipefail

CHANGELOG_FILE="${CHANGELOG_FILE:-CHANGELOG.md}"
OUTPUT_FILE="${OUTPUT_FILE:-release_notes.md}"

error() {
    echo "Error: $1" >&2
    exit 1
}

normalize_version() {
    local version="${1:-}"

    version="${version#v}"

    if [[ ! "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
        error "Invalid version '$1'. Expected X.Y.Z or vX.Y.Z"
    fi

    printf '%s\n' "$version"
}

check_changelog_exists() {
    if [[ ! -f "$CHANGELOG_FILE" ]]; then
        error "$CHANGELOG_FILE not found"
    fi
}

get_unreleased_content() {
    awk '
        /^## \[Unreleased\]/ { capture = 1; next }
        /^## \[/ && capture { exit }
        /^\[/ && capture { exit }
        capture { print }
    ' "$CHANGELOG_FILE"
}

write_release_notes() {
    local content="$1"

    printf '%s\n' "$content" | awk '
        {
            lines[++count] = $0
        }
        END {
            start = 1
            while (start <= count && lines[start] ~ /^[[:space:]]*$/) {
                start++
            }

            finish = count
            while (finish >= start && lines[finish] ~ /^[[:space:]]*$/) {
                finish--
            }

            for (i = start; i <= finish; i++) {
                print lines[i]
            }
        }
    ' > "$OUTPUT_FILE"
}

validate() {
    check_changelog_exists

    local unreleased_content
    unreleased_content="$(get_unreleased_content)"

    local trimmed_content
    trimmed_content="$(printf '%s\n' "$unreleased_content" | grep -v '^[[:space:]]*$' | grep -v '^###' || true)"

    if [[ -z "$trimmed_content" ]]; then
        error "No changes found in [Unreleased]. Add changelog entries before releasing."
    fi
}

update_unreleased_link() {
    local version="$1"
    local repo_url="$2"

    perl -0pi -e 's{^\[Unreleased\]: .*?/compare/v[0-9.]+\.\.\.HEAD$}{[Unreleased]: '"$repo_url"'/compare/v'"$version"'...HEAD}m' "$CHANGELOG_FILE"
}

insert_version_link() {
    local version="$1"
    local repo_url="$2"
    local previous_version="$3"

    if [[ -n "$previous_version" ]]; then
        perl -0pi -e 's{^\[\Q'"$previous_version"'\E\]: .*$}{['"$version"']: '"$repo_url"'/compare/v'"$previous_version"'...v'"$version"'\n$&}m' "$CHANGELOG_FILE"
    else
        printf '\n[%s]: %s/releases/tag/v%s\n' "$version" "$repo_url" "$version" >> "$CHANGELOG_FILE"
    fi
}

release() {
    check_changelog_exists

    local version
    version="$(normalize_version "${1:-}")"

    validate

    local unreleased_content
    unreleased_content="$(get_unreleased_content)"
    write_release_notes "$unreleased_content"

    local release_date
    release_date="$(date +%Y-%m-%d)"

    local tmp_file
    tmp_file="$(mktemp "${CHANGELOG_FILE}.release.XXXXXX")"

    awk -v version="$version" -v release_date="$release_date" '
        BEGIN { in_unreleased = 0; content = "" }
        /^## \[Unreleased\]/ {
            print $0
            print ""
            in_unreleased = 1
            next
        }
        /^## \[/ && in_unreleased {
            print "## [" version "] - " release_date
            print content
            in_unreleased = 0
        }
        /^\[/ && in_unreleased {
            print "## [" version "] - " release_date
            print content
            in_unreleased = 0
        }
        in_unreleased {
            content = content $0 "\n"
            next
        }
        { print }
        END {
            if (in_unreleased && content != "") {
                print "## [" version "] - " release_date
                print content
            }
        }
    ' "$CHANGELOG_FILE" > "$tmp_file"

    mv "$tmp_file" "$CHANGELOG_FILE"

    local repo_url
    repo_url="$(git config --get remote.origin.url 2>/dev/null || true)"

    if [[ -n "$repo_url" ]]; then
        repo_url="$(printf '%s\n' "$repo_url" | sed 's/\.git$//' | sed 's#git@github.com:#https://github.com/#')"
    else
        repo_url="https://github.com/OWNER/REPO"
    fi

    update_unreleased_link "$version" "$repo_url"

    local previous_version
    previous_version="$(grep -Eo '^## \[[0-9]+\.[0-9]+\.[0-9]+\]' "$CHANGELOG_FILE" | sed -n '2p' | sed 's/^## \[\(.*\)\]$/\1/' || true)"

    if ! grep -q "^\[$version\]:" "$CHANGELOG_FILE"; then
        insert_version_link "$version" "$repo_url" "$previous_version"
    fi
}

usage() {
    cat <<'EOF'
Usage: parse-changelog.sh <command> [version]

Commands:
  validate           Check changelog has unreleased content
  release <version>  Promote [Unreleased] to the given version and write release notes
EOF
}

case "${1:-}" in
    validate)
        validate
        ;;
    release)
        release "${2:-}"
        ;;
    -h|--help|help)
        usage
        ;;
    *)
        usage
        exit 1
        ;;
esac
