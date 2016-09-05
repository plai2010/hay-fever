.PHONY: all clean

TARGETS=\
	../gift-registry/js/gift-registry.prod.js \
	../etc/httpd.conf \

all: $(TARGETS)

clean:
	rm -f $(TARGETS)

%.conf: %.conf.php
	@echo Generating configuration $@ ...
	@php "$<"

%.prod.js: %.js
	@echo Compiling Javascript $< ...
	@java -jar ../lib/google-closure-compiler.jar --js "$<" --js_output_file "$@"
