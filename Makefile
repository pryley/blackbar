VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

.PHONY: analyse
analyse: ## Run phpstan analyser
	./vendor/bin/phpstan analyse --memory-limit 1G

.PHONY: build
build: ## Build all assets and languages
	npx gulp
	npx rollup -c

.PHONY: bump
bump: ## Bump to the next minor version
	npx gulp bump

.PHONY: help
help: ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

.PHONY: release
release: ## Release a new version of Site Reviews
	sh ./release.sh

.PHONY: update
update: ## Update Composer and NPM
	valet composer update
	npm-check -u

.PHONY: zip
zip: ## Create a zip archive of Site Reviews
	git archive -o ./blackbar-v$(VERSION).zip --prefix=blackbar/ HEAD
	open .
