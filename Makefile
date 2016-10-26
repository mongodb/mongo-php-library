.PHONY: apigen composer test

COMPOSER_ARGS=update --no-interaction --prefer-source
PHPUNIT_ARGS=--process-isolation

composer:
	@command -v composer >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		composer $(COMPOSER_ARGS); \
	elif test -r composer.phar; then \
		php composer.phar $(COMPOSER_ARGS); \
	else \
		echo >&2 "Cannot find composer; aborting."; \
		false; \
	fi

test: composer
	@command -v phpunit >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		phpunit $(PHPUNIT_ARGS); \
	elif test -r phpunit.phar; then \
		php phpunit.phar $(PHPUNIT_ARGS); \
	else \
		echo >&2 "Cannot find phpunit; aborting."; \
		false; \
	fi

release/%: release-log/%
	@echo "Please run:"
	@echo "    " git add RELEASE-$(*)
	@echo "    " git commit -m \"Add $(*) release notes\"
	@echo "    " git tag -a -m \"Release MongoDB library $(*)\" $(*)
	@echo "    " git push REMOTE `git rev-parse --abbrev-ref HEAD`
	@echo "    " git push REMOTE --tags

release-log/%:
	@git log --pretty=format:"%ad  %an  <%ae>%n%x09* %s%n" --date short --no-merges --since="$$(git show -s --format=%ad `git rev-list --tags --max-count=1`)" > RELEASE-$(*)
