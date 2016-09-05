<?php
/**
 * Build Apache configuration file for CCHS gift registry.
 */
$TOP_DIR = dirname(__DIR__);
$REALM = 'CCHS Gift Registry';

// construct output file name
//
$path = pathinfo(__FILE__);
$output = $path['dirname'].DIRECTORY_SEPARATOR.$path['filename'];
ob_start();
?>
#---------------------------------------------------------------
# gift registry {{{
#
AliasMatch \
	"^/gifts/(admin|donate)/?$" \
	"<?php echo $TOP_DIR; ?>/gift-registry/$1/"
AliasMatch \
	"^/gifts/(admin|donate)/api/(.*)$" \
	"<?php echo $TOP_DIR; ?>/gift-registry/api.php/$2"
AliasMatch \
	"^/gifts/(admin|donate)/(css|fonts|js)/(.*)$" \
	"<?php echo $TOP_DIR; ?>/gift-registry/$2/$3"
Alias /gifts/ "<?php echo $TOP_DIR; ?>/gift-registry/"

<Location /gifts/admin>
	AuthType Digest
	AuthName "<?php echo $REALM; ?>"
	AuthDigestDomain "/gifts/admin/"
	AuthDigestProvider file
	AuthUserFile "<?php echo $TOP_DIR; ?>/etc/users.htdigest"
	Require valid-user
</Location>

<Directory <?php echo $TOP_DIR; ?>/gift-registry>
	Options FollowSymLinks
	AllowOverride All
	Order Allow,Deny
	Allow from all
	<IfModule mod_authz_core.c>
	Require all granted
	</IfModule>
</Directory>
# }}}
#---------------------------------------------------------------
# vim: set ts=2 noexpandtab syntax=apache fdm=marker: ('zR' to unfold all)
<?php

file_put_contents($output, ob_get_clean());
echo "Apache configuration generated: $output", PHP_EOL;

// vim: set ts=2 noexpandtab syntax=php fdm=marker: ('zR' to unfold all)
