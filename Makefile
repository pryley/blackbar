VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

analyse: ## Run phpstan analyser
	./vendor/bin/phpstan analyse --memory-limit 1G

build: ## Build all assets, blocks, and languages
	npx gulp
	npx rollup -c

bump: ## Bump to the next minor version
	npx gulp bump

check: ## Check WP compatibility for declared version
	./vendor/bin/wp-since check

compat: ## Run PHP CodeSniffer to check PHP 7.4+ Compatibility
	XDEBUG_MODE=off ./vendor/bin/phpcs --standard=phpcs.xml

help: ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

release: ## Release a new version of Blackbar
	sh ./release.sh

update: ## Update Composer and NPM
	composer update
	npm-check -u

zip: ## Create a zip archive of Blackbar
	git archive -o ./blackbar-v$(VERSION).zip --prefix=blackbar/ HEAD
	open .

.PHONY: analyse build bump check compat help release update zip
