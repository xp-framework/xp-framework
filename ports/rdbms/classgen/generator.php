<?php
/* Test application
 *
 * Example:
 * php generator.php sybase://user:pass@host/database/table | sabcmd util/xp.php.xsl
 *
 * $Id$
 */
  require('lang.base.php');
  uses(
    'rdbms.util.DBXmlGenerator', 
    'rdbms.DBTable',
    'rdbms.sybase.SybaseDBAdapter',
    'rdbms.mysql.MySQLDBAdapter',
    'rdbms.DSN',
    'rdbms.DriverManager',
    'util.log.Logger',
    'util.log.FileAppender',
    'util.cmd.ParamString'
  );

  $param= &new ParamString();
  try(); {
    $dsnString= $param->value (1);
  } if (catch ('Exception', $e)) {
    printf(
      "Usage: %s [dsn]\n".
          "       Example: php generator.php sybase://user:pass@host/database/table\n", 
          basename($_SERVER['argv'][0])
	);
    exit();
  }

  $dsn= parse_url($dsnString);
  list(, $database, $table)= explode('/', $dsn['path'], 3);

  $drvManager= &DriverManager::getInstance();
  $dbo= &$drvManager->getConnection ($dsnString);

  switch ($dsn['scheme']) {
    case 'sybase':
      $adapter= &new SybaseDBAdapter($dbo);
      break;
      
    case 'mysql':
      $adapter= &new MySQLDBAdapter($dbo);
      break;
    
    default:
      printf("Unsupported scheme '%s'\n", $dsn['scheme']);
      exit();
  }
  
  // HACK
  $adapter->conn->dsn->parts['path']= NULL;
  
  try(); {
    $adapter->conn->connect();
    $adapter->conn->selectdb($database);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }	
  
  try(); {
    $gen= &DBXmlGenerator::createFromTable(DBTable::getByName($adapter, $table)); 
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  echo $gen->getSource();
?>
