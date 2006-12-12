<?php
/* Test application
 *
 * Example:
 * php generator.php sybase://user:pass@host/database/table | sabcmd util/xp.php.xsl
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

Usage: php generator.php <DSN> <FQCN> [<options>]

  * DSN:
    scheme://user:password@host/database/table

  * FQCN:
    Fully qualified class name, e.g. "de.thekid.db.forum.Entry"

  * Options:
    --connection, -C: Define connection name, defaults to <host> from DSN
    --output, -O    : Output to file (without value: [classname].xml)

--------------------------------------------------------------------------------
__
    );
    exit(1);
  }

  // Parse DSN
  $dsn= parse_url($param->value(1));
  list(, $database, $table)= explode('/', $dsn['path'], 3);
  if (!isset($adapters[$dsn['scheme']])) {
    Console::writeLine('Unsupported scheme "', $dsn['scheme'], '"');
    exit(1);
  }
  
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

  // Generate XML
  try(); {
    $gen= &DBXmlGenerator::createFromTable(
      DBTable::getByName($adapter, $table), 
      $param->value('connection', 'C', $dsn['host']), 
      $database
    ); 
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  $fqcn= $param->value(2);
  with ($node= &$gen->doc->root->children[0]); {  // Table node
    $node->setAttribute('dbtype', $dsn['scheme']);
    $node->setAttribute('class', substr($fqcn, strrpos($fqcn, '.')+ 1));
    $node->setAttribute('package', substr($fqcn, 0, strrpos($fqcn, '.')));
  }
  
  // Decides whether output is written into a file or stdout
  if (!$param->exists('output', 'O')) {
    Console::write($gen->getSource());
  } else {
    $f= &new File($param->value('output', 'O', substr($fqcn, strrpos($fqcn, '.')+ 1).'.xml'));
    $written= FileUtil::setContents($f, $gen->getSource());
    Console::writeLinef(
      '===> Output written to %s (%.2f kB)', 
      $f->getURI(),
      $written / 1024
    );
  } 
?>
