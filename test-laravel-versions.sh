#!/bin/bash

# Script to test SPID Laravel package against multiple Laravel versions
# This version is compatible with bash 3.2 (macOS default)
# Usage: ./test-laravel-versions-legacy.sh [version]
# Example: ./test-laravel-versions-legacy.sh 12  (test only Laravel 12)
#          ./test-laravel-versions-legacy.sh     (test all versions)

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to get Laravel version constraint
get_laravel_version() {
    case $1 in
        9) echo "^9.52.4" ;;
        10) echo "^10.0" ;;
        11) echo "^11.0" ;;
        12) echo "^12.0" ;;
        *) echo "" ;;
    esac
}

# Function to get Testbench version
get_testbench_version() {
    case $1 in
        9) echo "^7.22.1" ;;
        10) echo "^8.0" ;;
        11) echo "^9.0" ;;
        12) echo "^10.0" ;;
        *) echo "" ;;
    esac
}

# Function to get PHPUnit version
get_phpunit_version() {
    case $1 in
        9) echo "^9.5.10" ;;
        10) echo "^10.0" ;;
        11) echo "^10.0" ;;
        12) echo "^11.0" ;;
        *) echo "" ;;
    esac
}

# Function to get Carbon version
get_carbon_version() {
    case $1 in
        9) echo "^2.66" ;;
        10) echo "^2.66" ;;
        11) echo "^3.0" ;;
        12) echo "^3.0" ;;
        *) echo "" ;;
    esac
}

# Function to print colored messages
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

# Function to backup composer files
backup_composer() {
    print_info "Backing up composer.json and composer.lock..."
    cp composer.json composer.json.backup
    if [ -f composer.lock ]; then
        cp composer.lock composer.lock.backup
    fi
}

# Function to restore composer files
restore_composer() {
    print_info "Restoring composer.json and composer.lock..."
    mv composer.json.backup composer.json
    if [ -f composer.lock.backup ]; then
        mv composer.lock.backup composer.lock
    fi
}

# Function to check PHP version
check_php_version() {
    local required_version=$1
    local current_version=$(php -r 'echo PHP_VERSION;')

    echo "Current PHP version: $current_version"
    echo "Required PHP version: >= $required_version"
}

# Function to test a specific Laravel version
test_laravel_version() {
    local version=$1
    local illuminate_version=$(get_laravel_version $version)
    local testbench_version=$(get_testbench_version $version)
    local phpunit_version=$(get_phpunit_version $version)
    local carbon_version=$(get_carbon_version $version)

    if [ -z "$illuminate_version" ]; then
        print_error "Invalid Laravel version: $version"
        return 1
    fi

    print_header "Testing Laravel $version"

    echo "Installing dependencies for Laravel $version..."
    echo "  - illuminate/config: $illuminate_version"
    echo "  - illuminate/support: $illuminate_version"
    echo "  - orchestra/testbench: $testbench_version"
    echo "  - phpunit/phpunit: $phpunit_version"
    echo "  - nesbot/carbon: $carbon_version"
    echo ""

    # Update composer.json with specific versions
    # First, update production dependencies (require)
    print_info "Updating production dependencies..."
    composer require --no-update \
        "illuminate/config:$illuminate_version" \
        "illuminate/support:$illuminate_version" \
        "nesbot/carbon:$carbon_version"

    # Then, update development dependencies (require-dev)
    print_info "Updating development dependencies..."
    composer require --dev --no-update \
        "orchestra/testbench:$testbench_version" \
        "phpunit/phpunit:$phpunit_version"

    # Update dependencies
    print_info "Running composer update (this may take a while)..."
    if ! composer update --prefer-dist --no-interaction --no-plugins 2>&1 | tee /tmp/composer-update.log | grep -E "(Installing|Upgrading|Package operations|Nothing to modify)"; then
        echo ""
        echo "Full composer output:"
        cat /tmp/composer-update.log
        print_error "Failed to update dependencies for Laravel $version"
        return 1
    fi

    print_success "Dependencies installed successfully"

    # Run tests
    print_info "Running PHPUnit tests..."
    echo ""

    # select phpunit config based on requested phpunit major
    phpunit_major=$(echo "$phpunit_version" | sed -E 's/^\^?([0-9]+).*/\1/')

    if XDEBUG_MODE=coverage vendor/bin/phpunit -c "phpunit-$phpunit_major.xml"; then
        print_success "All tests passed for Laravel $version!"
        return 0
    else
        print_error "Tests failed for Laravel $version"
        return 1
    fi
}

# Main script
main() {
    local test_version=$1
    local failed_versions=""
    local successful_versions=""

    print_header "SPID Laravel - Multi-version Testing"

    check_php_version "8.2"
    echo ""

    # Backup composer files
    backup_composer

    # Determine which versions to test
    local versions_to_test
    if [ -n "$test_version" ]; then
        local laravel_version=$(get_laravel_version $test_version)
        if [ -z "$laravel_version" ]; then
            print_error "Invalid Laravel version: $test_version"
            print_info "Available versions: 9, 10, 11, 12"
            restore_composer
            exit 1
        fi
        versions_to_test="$test_version"
    else
        versions_to_test="9 10 11 12"
    fi

    # Test each version
    for version in $versions_to_test; do
        echo ""
        if test_laravel_version "$version"; then
            successful_versions="$successful_versions $version"
        else
            failed_versions="$failed_versions $version"
        fi
        echo ""
        echo "---"
        echo ""
    done

    # Restore original composer files
    restore_composer
    print_success "Composer files restored"
    echo ""

    # Print summary
    print_header "TEST SUMMARY"

    if [ -n "$successful_versions" ]; then
        print_success "Successful versions:$successful_versions"
    fi

    if [ -n "$failed_versions" ]; then
        print_error "Failed versions:$failed_versions"
        exit 1
    else
        print_success "All tests passed! ✨"
        exit 0
    fi
}

# Run main function
main "$@"
