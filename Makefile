.PHONY: apigen composer test docs mkdocs

COMPOSER_ARGS=update --no-interaction --prefer-source
PHPUNIT_ARGS=--process-isolation

composer:
	@command -v composer >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		composer $(COMPOSER_ARGS); \
	elif test -r composer.phar; then \
		php composer.phar $(COMPOSER_ARGS); \
	else \
		$(error Cannot find composer); \
	fi

test: composer
	@command -v phpunit >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		phpunit $(PHPUNIT_ARGS); \
	elif test -r phpunit.phar; then \
		php phpunit.phar $(PHPUNIT_ARGS); \
	else \
		$(error Cannot find phpunit); \
	fi

apigen:
	@command -v apigen >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		apigen generate; \
	elif test -r apigen.phar; then \
		php apigen.phar generate; \
	else \
		$(error Cannot find agigen); \
	fi

mkdocs:
	@command -v mkdocs >/dev/null 2>&1; \
	if test $$? -eq 0; then \
		mkdocs build --clean; \
	else \
		$(error Cannot find mkdocs); \
	fi

release/%: release-log/%
	@echo "Please run:"
	@echo "    " git add RELEASE-$(*)
	@echo "    " git commit -m \"Add $(*) release notes\"
	@echo "    " git tag -a -m \"Release MongoDB library $(*)\" $(*)
	@echo "    " git push --tags
	@echo "    " make release-docs

docs: mkdocs apigen

release-docs: docs
	mkdocs gh-deploy

release-log/%:
	@git log --pretty=format:"%ad  %an  <%ae>%n%x09* %s%n" --date short --no-merges --since="$$(git show -s --format=%ad `git rev-list --tags --max-count=1`)" > RELEASE-$(*)
