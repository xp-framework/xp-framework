# Virtualhost for xpdoc
#
# Usage:
# Include this in your httpd.conf
#
# $Id$

<VirtualHost xpdoc.your.name.here>
    DocumentRoot /path/to/where/xpdoc/is/
    AddType application/x-httpd-php .html
    RewriteEngine on
    RewriteRule /source/(.*) /showsource.php?f=$1
</VirtualHost>

