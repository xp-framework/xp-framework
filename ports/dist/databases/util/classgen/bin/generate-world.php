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
    'io.FileUtil',
    'io.Folder',
    'util.Properties'
  );
  
  // Scheme => Adapter mapping
  $adapters= array(
    'mysql'   => 'rdbms.mysql.MySQLDBAdapter',
    'sybase'  => 'rdbms.sybase.SybaseDBAdapter',
    'pgsql'   => 'rdbms.pgsql.PostgreSQLDBAdapter'
  );

  // {{{ main
  $param= &new ParamString();

  // Read Config file
  $ini=new Properties($param->value('config', 'c', 'config.ini'));
  if ($param->exists('help', 'h')) {
    Console::writeLine(<<<__
Generates O/R XML for multiple databases
--------------------------------------------------------------------------------

Usage: php generate-world.php <options>

  Options:
    --config  -c: specify a config file (default: config.ini)
    --help    -h: I guess you already figured that one out (or you wouldn't be
                  reading this).
    
The list of Databases has to be specified in the config file.

The syntax of the configuration:

--snip--
[configuration]
outputdir="/home/rene/spam"

[database1]
dsn="scheme://user:passwordl@host/DATABASE"
prefix="methadon"
prefix.include="foo|bar|stfu|crap"
connection="methadon"
package="de.schlund.db.methadon"

[database2]
dsn=""
prefix=""
prefix.include=""
connection=""
package=""
--snip--
--------------------------------------------------------------------------------    
__
    );
    exit(1);
  };
  if (!$ini->exists()) {
    Console::writeLine('No config file found. Use --help for more details');
  };
  $outputdir=$ini->readString('configuration', 'outputdir', './');
  if (substr($outputdir, -1, 1) != '/') {
    $outputdir= $outputdir.'/';
  };
  while(TRUE) {
    $section=$ini->getNextSection();
    if (empty($section)) {
    Console::writeLine('==> No more sections found in config.ini');
    Console::writeLine('==> Generation complete');
    exit(-1);
    };
    if ($section != 'configuration') {
    $dsntemp    = $ini->readString($section, 'dsn');
    $prefix     = $ini->readString($section, 'prefix');
    $incprefix  = $ini->readArray ($section, 'prefix.include');
    $connection = $ini->readString($section, 'connection');
    $package    = $ini->readString($section, 'package');
    generateTables($dsntemp, 
                   $prefix, 
                   $incprefix, 
                   $connection, 
                   $package, 
                   $outputdir, 
                   $adapters
                   );
    Console::writeLine('Done here ==> Jumping to the next database');
    };
  };

      /**
     * generates .xml documents from tables 
     *
     * @access public
     * @param  string $dsn, $prefix, $connection, $package, $outputdir, 
     *         array $incprefix,
     */
  function generateTables($dsntemp, $prefix, $incprefix, $connection, $package, $outputdir, $adapters) {
    // Parse DSN
    $dsn= parse_url($dsntemp);
    list(, $database)= explode('/', $dsn['path'], 2);
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

    // Create new Folder Object and new Folder(s) if necessary
    $fold = new Folder ($outputdir.strtolower($database).'/');
    if (!$fold->exists()) {
      $fold->create();
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
          $connection,          
          $database
        ); 
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit;
      }

      // Determine if filename needs prefix
      if (in_array($t->name, $incprefix)) {
        $filename= $prefix.ucfirst($t->name);
      } else {
        $filename= ucfirst($t->name);
      };
      with ($node= &$gen->doc->root->children[0]); {  // Table node
        $node->setAttribute('dbtype', $dsn['scheme']);
        $node->setAttribute('class', $filename);
        $node->setAttribute('package', $package);
      }
      $f= &new File($fold->getURI().$filename.'.xml');
      $written= FileUtil::setContents($f, $gen->getSource());
      Console::writeLinef(
        '===> Output written to %s (%.2f kB)', 
        $f->getURI(),
        $written / 1024
      );
    }
  }
?>
