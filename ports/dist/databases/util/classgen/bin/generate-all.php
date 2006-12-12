<?php
/* Test application
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'rdbms.util.DBXmlGenerator', 
    'rdbms.DBTable',
    'rdbms.DSN',
    'rdbms.DriverManager',
    'util.log.Logger',
    'util.log.FileAppender',
    'util.cmd.ParamString',
    'io.File',
    'io.FileUtil'
  );
  
  // Scheme => Adapter mapping
  $adapters= array(
    'mysql'   => 'rdbms.mysql.MySQLDBAdapter',
    'sybase'  => 'rdbms.sybase.SybaseDBAdapter',
    'pgsql'   => 'rdbms.pgsql.PostgreSQLDBAdapter'
  );

  // {{{ main
  $param= &new ParamString();
  if ($param->count < 3 || $param->exists('help', '?')) {
    Console::writeLine(<<<__
Generates O/R XML for a specified database table
--------------------------------------------------------------------------------

Usage: php generate-all.php <DSN> <package> [<options>]

  * DSN:
    scheme://user:password@host/database

  * package:
    Fully qualified package name, e.g. "de.thekid.db.forum"

  * Options:
    --connection, -C: Define connection name, defaults to <host> from DSN
    --prefix, -p    : Prefix classes with <prefix> (default: none)

--------------------------------------------------------------------------------
__
    );
    exit(1);
  }

  // Parse DSN
  $dsn= parse_url($param->value(1));
  list(, $database)= explode('/', $dsn['path'], 2);
  if (!isset($adapters[$dsn['scheme']])) {
    Console::writeLine('Unsupported scheme "', $dsn['scheme'], '"');
    exit(1);
  }
  $package= $param->value(2);
  $prefix= $param->value('prefix', 'p', '');
  
  // Get connection
  $dm= &DriverManager::getInstance();
  $dbo= &$dm->getConnection(sprintf(
    '%s://%s:%s@%s/%s',
    $dsn['scheme'],
    $dsn['user'],
    $dsn['pass'],
    $dsn['host'],
    $database
  ));

  try(); {
    $dbo->connect();
  } if (catch('SQLException', $e)) {
    $e->printStackTrace();
    exit;
  }

  // Create adapter instance
  $class= &XPClass::forName($adapters[$dsn['scheme']]);
  $adapter= &$class->newInstance($dbo);
  
  $tables= DBTable::getByDatabase($adapter, $database);
  foreach ($tables as $t) {

    // Generate XML
    try(); {
      $gen= &DBXmlGenerator::createFromTable(
        $t, 
        $param->value('connection', 'C', $dsn['host']), 
        $database
      ); 
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      exit;
    }

    $filename= $prefix.ucfirst($t->name);
    with ($node= &$gen->doc->root->children[0]); {  // Table node
      $node->setAttribute('dbtype', $dsn['scheme']);
      $node->setAttribute('class', $filename);
      $node->setAttribute('package', $package);
    }

    $f= &new File($filename.'.xml');
    $written= FileUtil::setContents($f, $gen->getSource());
    Console::writeLinef(
      '===> Output written to %s (%.2f kB)', 
      $f->getURI(),
      $written / 1024
    );
  }
?>
