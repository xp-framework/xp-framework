# Virtualhost for xpdoc
#
# Usage:
# Include this in your httpd.conf
#
# $Id$

# Minimal sample
<VirtualHost xpdoc.your.name.here>
    DocumentRoot /path/to/where/xpdoc/is/
    AddType application/x-httpd-php .html
    RewriteEngine on
    RewriteRule /source/(.*) /showsource.php?f=$1
</VirtualHost>

# Extended sample
<VirtualHost xp.php3.de>
        SSLEngine       off
        ServerName      xp.php3.de
        ServerAdmin     xp@php3.de
        DocumentRoot    /home/httpd/xp.php3.de/doc_root/

        ErrorDocument   404 /error/index.php
        ErrorDocument   401 /error/index.php
        ErrorDocument   403 /error/index.phP

        ErrorLog      /var/log/httpd/xp.php3.de/error_log
        CustomLog     /var/log/httpd/xp.php3.de/access_log combined

        DirectoryIndex  index.php index.php3 index.html

        # Enable php-parsing of .html-files
        AddType application/x-httpd-php .html

        # Include path
        php_value include_path ".:/usr/local/lib/xp"

        RewriteEngine On

        # Nice "show-source-path"
        RewriteCond %{REQUEST_URI} ^/source/.*$
        RewriteRule ^/source/(.*)$ /showsource.php?f=$1

        # Nice "search-path"
        RewriteCond %{REQUEST_URI} ^/search/.*$
        RewriteRule ^/search/(.+)$ /search.php?keyword=$1

        <Directory "/home/httpd/xp.php3.de/doc_root/">
                Options +Indexes
                AllowOverride All
        </Directory>
</VirtualHost>

