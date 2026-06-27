#!/bin/bash
# pre-commit — chạy validation trước khi commit
# Cài đặt: cp scripts/pre-commit.sh .git/hooks/pre-commit && chmod +x .git/hooks/pre-commit

PHP="D:/xampp/php/php.exe"
PROJECT="D:/xampp/htdocs/DA_TTTN"
ERRORS=0

echo "🔍 Pre-commit validation..."
echo ""

# 1. PHP Syntax check
echo "📋 PHP Syntax..."
for f in $(git diff --cached --name-only --diff-filter=ACM | grep '\.php$'); do
    if [ -f "$f" ]; then
        "$PHP" -l "$f" 2>&1 | grep -v "No syntax" && ERRORS=1
    fi
done

# 2. PHP-CS-Fixer (only on changed PHP files)
if git diff --cached --name-only --diff-filter=ACM | grep -q '\.php$'; then
    echo "🎨 PHP-CS-Fixer..."
    "$PHP" "$PROJECT/vendor/bin/php-cs-fixer" fix --config="$PROJECT/.php-cs-fixer.php" --dry-run --quiet 2>&1 || {
        echo "⚠️  Code style issues found. Run: php vendor/bin/php-cs-fixer fix"
        ERRORS=1
    }
fi

# 3. PHPStan
echo "🔬 PHPStan..."
"$PHP" "$PROJECT/vendor/bin/phpstan" analyse -c "$PROJECT/phpstan.neon" --memory-limit=256M --quiet 2>&1 || {
    ERRORS=1
}

# 4. PHPUnit
echo "🧪 PHPUnit..."
"$PHP" "$PROJECT/vendor/bin/phpunit" -c "$PROJECT/tests/phpunit.xml" --quiet 2>&1 || {
    ERRORS=1
}

echo ""
if [ $ERRORS -eq 0 ]; then
    echo "✅ All checks passed!"
    exit 0
else
    echo "❌ Validation failed. Please fix errors before committing."
    exit 1
fi
