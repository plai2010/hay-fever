California Crosspoint Gift Registry
Copyright (c) 2016 Patrick Lai

Requirements
- Apache 2.4 (2.2 may be okay)
  - auth_digest
- PHP 5.6
- MySQL
- 'make' and 'java'

Installation
- Run 'make' in the 'build' directory. This generates/updates two files:
  - etc/httpd.conf
  - gift-registry/js/gift-registry.prod.js
- Incorporate 'etc/httpd.conf' into Apache configuration, e.g. with the
  'Include' directive.
- Create MySQL database, e.g.
    create database 'cchs_db' character set utf8 collate utf8_general_ci;
    create user 'cchs'@'localhost' identified by 'some-secret';
    grant all privileges on cchs_db.* to cchs@localhost;
- Run the three MySQL scripts in order against the database created above:
  - etc/schema.sql
  - etc/base-data.sql
  - gift-registry/donate/etc/hayward.sql
- Update database parameters in 'etc/local-settings.php'.
- The 'etc/base-data.sq' creates 'admin'. Additional ones may be added to
  the CCHS_USER table.
- Create password for admin users. Access control is through HTTP digest
  authentication ("CCHS Gift Registry" realm); use Apache 'htdigest'
  command to update 'etc/users.digest'. For example,
    htdigest etc/users.htdigest "CCHS Gift Registry" admin

Links
- Donation page: http://host/gifts/donate
- Admin page: https://host/gifts/admin

# vim: set ts=2 expandtab fdm=marker: ('zR' to unfold all)
