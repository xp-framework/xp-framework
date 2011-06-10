SHELL = /bin/sh
GENERATOR = ~/devel/xp/ports/technologies/opt/jay/generate.sh
PHONY = generate

generate: JsonParser.class.php

JsonParser.class.php: grammar/json.jay
	@echo "===> Generating parser"
	@$(GENERATOR) grammar/json.jay php5 Json > JsonParser.class.php

clean: JsonParser.class.php
	@echo "===> Remove JsonParser.class.php"
	@rm JsonParser.class.php

help:
	@echo "Generates and removes the JsonParser.class.php"
	@echo ""
	@echo "Without argument - generates the file"
	@echo "clean            - removes the file"
	@echo "help             - prints this menu"

.PHONY: $(PHONY)
