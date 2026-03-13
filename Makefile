VERSION=$(shell grep '"version":' composer.json | sed -E 's/.*"version"[: ]*"(.*)".*/\1/')

.PHONY: push_to_github
push_to_github:
	@echo "Pushing version $(VERSION) to GitHub..."
	git add .
	git commit -m "$(VERSION)"
	git tag $(VERSION)
	git push origin main --tags
