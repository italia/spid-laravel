#!/bin/bash

# Script to test SPID Laravel package against multiple Laravel & PHP versions using Docker
# Matrix matches CircleCI configuration exactly.
#
# Usage:
#   ./test-laravel-versions-docker.sh                   (run full matrix)
#   ./test-laravel-versions-docker.sh 12                (test Laravel 12 on all valid PHP versions)
#   ./test-laravel-versions-docker.sh 12 8.3            (test Laravel 12 specifically on PHP 8.3)

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
BLUE='\033[0;34m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

# Matrix definition
ALL_PHP_VERSIONS="8.2 8.3 8.4 8.5"
ALL_LARAVEL_VERSIONS="9 10 11 12 13"

# Check if a PHP + Laravel combination is valid according to CircleCI matrix
is_valid_combination() {
    local php=$1
    local laravel=$2

    case $laravel in
        9)
            [ "$php" = "8.2" ] && return 0
            ;;
        10)
            [ "$php" = "8.2" ] || [ "$php" = "8.3" ] && return 0
            ;;
        11)
            [ "$php" = "8.2" ] || [ "$php" = "8.3" ] || [ "$php" = "8.4" ] && return 0
            ;;
        12)
            [ "$php" = "8.2" ] || [ "$php" = "8.3" ] || [ "$php" = "8.4" ] || [ "$php" = "8.5" ] && return 0
            ;;
        13)
            [ "$php" = "8.3" ] || [ "$php" = "8.4" ] || [ "$php" = "8.5" ] && return 0
            ;;
    esac

    return 1
}

# Get phpunit XML config file per Laravel version
get_phpunit_config() {
    case $1 in
        9) echo "phpunit-9.xml" ;;
        10) echo "phpunit-10.xml" ;;
        11) echo "phpunit-10.xml" ;;
        12) echo "phpunit-11.xml" ;;
        13) echo "phpunit-12.xml" ;;
        *) echo "phpunit.xml" ;;
    esac
}

# Output formatting functions
print_header() {
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}================================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${CYAN}ℹ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Backup & Restore composer files
backup_composer() {
    print_info "Backing up composer.json and composer.lock..."
    cp composer.json composer.json.backup
    if [ -f composer.lock ]; then
        cp composer.lock composer.lock.backup
    fi
}

restore_composer() {
    if [ -f composer.json.backup ]; then
        print_info "Restoring original composer.json and composer.lock..."
        mv composer.json.backup composer.json
        if [ -f composer.lock.backup ]; then
            mv composer.lock.backup composer.lock
        fi
    fi
}

# Ensure composer files are restored on exit or interruption
trap restore_composer EXIT INT TERM

# Check Docker status
check_docker() {
    if ! command -v docker >/dev/null 2>&1; then
        print_error "Docker not installed or not in PATH."
        exit 1
    fi

    if ! docker info >/dev/null 2>&1; then
        print_error "The Docker daemon is not running."
        exit 1
    fi
}

# Run job inside Docker container
run_docker_job() {
    local laravel_version=$1
    local php_version=$2
    local phpunit_config=$(get_phpunit_config $laravel_version)

    print_header "Testing PHP $php_version - Laravel $laravel_version (Docker: cimg/php:$php_version)"

    # Restore clean composer.json state before altering dependencies
    restore_composer
    backup_composer

    # Generate container setup script matching CircleCI commands
    local container_cmd="set -e
    
    echo '==> Ensuring coverage driver (Xdebug) is installed...'
    curl -sSL -o pie https://github.com/php/pie/releases/latest/download/pie.phar
    chmod +x pie
    sudo mv pie /usr/local/bin/pie
    sudo pie install xdebug/xdebug

    echo '==> Updating Composer dependencies for Laravel $laravel_version...'
    case $laravel_version in
        9)
            composer require --no-update 'illuminate/config:^9.52.4' 'illuminate/support:^9.52.4' 'nesbot/carbon:^2.66'
            composer require --dev --no-update 'orchestra/testbench:^7.22.1' 'phpunit/phpunit:^9.5.10'
            ;;
        10)
            composer require --no-update 'illuminate/config:^10.0' 'illuminate/support:^10.0' 'nesbot/carbon:^2.66'
            composer require --dev --no-update 'orchestra/testbench:^8.0' 'phpunit/phpunit:^10.0'
            ;;
        11)
            composer require --no-update 'illuminate/config:^11.0' 'illuminate/support:^11.0' 'nesbot/carbon:^3.0'
            composer require --dev --no-update 'orchestra/testbench:^9.0' 'phpunit/phpunit:^10.0'
            ;;
        12)
            composer require --no-update 'illuminate/config:^12.0' 'illuminate/support:^12.0' 'nesbot/carbon:^3.0'
            composer require --dev --no-update 'orchestra/testbench:^10.0' 'phpunit/phpunit:^11.0'
            ;;
        13)
            composer require --no-update 'illuminate/config:^13.0' 'illuminate/support:^13.0' 'nesbot/carbon:^3.0'
            composer require --dev --no-update 'orchestra/testbench:^11.0' 'phpunit/phpunit:^12.0'
            ;;
    esac

    echo '==> Running composer update...'
    COMPOSER_EXIT_ON_PATCH_FAILURE=1 composer update --prefer-dist --no-interaction

    echo '==> Validating SPID IdP certificates...'
    composer spid:idps:check

    echo '==> Validating package structure (pds-skeleton)...'
    vendor/bin/pds-skeleton validate
"

        container_cmd="$container_cmd
    echo '==> Running php-cs-fixer...'
    PHP_CS_FIXER_FUTURE_MODE=1 vendor/bin/php-cs-fixer fix --diff --dry-run --verbose
"

    # Add security audit and PHPUnit execution
    container_cmd="$container_cmd
    echo '==> Checking known security issues in dependencies...'
    composer audit

    echo '==> Running PHPUnit tests...'
    XDEBUG_MODE=coverage vendor/bin/phpunit -c $phpunit_config
"

    # Run Docker container
    if docker run --rm \
        -v "$(pwd)":/app \
        -w /app \
        "cimg/php:${php_version}" \
        bash -c "$container_cmd"; then
        print_success "PHP $php_version - Laravel $laravel_version PASSED"
        return 0
    else
        print_error "PHP $php_version - Laravel $laravel_version FAILED"
        return 1
    fi
}

# Main function
main() {
    local req_laravel=$1
    local req_php=$2

    local successful_jobs=""
    local failed_jobs=""

    check_docker

    print_header "SPID Laravel - Docker Matrix Testing (CircleCI Replica)"

    # Determine which combinations to run
    for l_ver in $ALL_LARAVEL_VERSIONS; do
        if [ -n "$req_laravel" ] && [ "$req_laravel" != "$l_ver" ]; then
            continue
        fi

        for p_ver in $ALL_PHP_VERSIONS; do
            if [ -n "$req_php" ] && [ "$req_php" != "$p_ver" ]; then
                continue
            fi

            if is_valid_combination "$p_ver" "$l_ver"; then
                if run_docker_job "$l_ver" "$p_ver"; then
                    successful_jobs="$successful_jobs\n  ✓ PHP $p_ver - Laravel $l_ver"
                else
                    failed_jobs="$failed_jobs\n  ✗ PHP $p_ver - Laravel $l_ver"
                fi
                echo ""
            fi
        done
    done

    # Print summary
    print_header "MATRIX TEST SUMMARY"

    if [ -n "$successful_jobs" ]; then
        echo -e "${GREEN}Passed Matrix Jobs:${NC}$successful_jobs"
        echo ""
    fi

    if [ -n "$failed_jobs" ]; then
        echo -e "${RED}Failed Matrix Jobs:${NC}$failed_jobs"
        echo ""
        exit 1
    else
        print_success "All executed matrix jobs passed successfully! ✨"
        exit 0
    fi
}

main "$@"
