PS1="[$(basename $(pwd))] $PS1"
CDPATH="$CDPATH:$(pwd)/gift-registry"

PATH="$(pwd)/bin:$PATH"

# TODO: extract database config from settings script into env
CCHS_DB_USER=cchs
CCHS_DB_PASS=cchs
CCHS_DB_NAME=cchs
CCHS_DB_HOST=localhost
eval $(php <<-'EOF'
	<?php
	$settings = @require getcwd().'/etc/local-settings.php';
	if (!empty($settings['dbuser'])) {
		echo "CCHS_DB_USER='{$settings['dbuser']}';\n";
		echo "CCHS_DB_PASS='{$settings['dbpass']}';\n";
		echo "CCHS_DB_NAME='{$settings['dbname']}';\n";
		echo "CCHS_DB_HOST='{$settings['dbhost']}';\n";
	}
EOF
)

dbclient()
{
	mysql \
		-u"$CCHS_DB_USER" \
		-p"$CCHS_DB_PASS" \
		-D"$CCHS_DB_NAME" \
		-h"$CCHS_DB_HOST" \
		-A "$@"
}

dbbackup()
{
	dbdump \
	| gzip -c \
	> "dump-$CCHS_DB_NAME.sql.gz"
}

dbdump()
{
	mysqldump \
		--skip-extended-insert \
		-u"$CCHS_DB_USER" \
		-p"$CCHS_DB_PASS" \
		-h"$CCHS_DB_HOST" \
		"$CCHS_DB_NAME"
}

#------------------------------------------------------------
# vim: set ts=4 noexpandtab fdm=marker syntax=sh:
