<?php
/* Test application
 *
 * Example:
 * php -q util/test.php sybase://user:pass@host/database/table | sabcmd util/xp.php.xsl
 *
 * $Id$
 */
    require('lang.base.php');
    uses(
        'rdbms.util.DBXmlGenerator', 
        'rdbms.DBTable',
        'rdbms.sybase.SPSybase',
        'rdbms.sybase.SybaseDBAdapter',
        'util.log.Logger',
        'util.log.FileAppender'
    );

    if (empty($_SERVER['argv'][1])) {
        printf(
            "Usage: %s [dsn]\n".
	    "       Example: php -q util/test.php sybase://user:pass@host/database/table\n", 
	    basename($_SERVER['argv'][0])
	);
        exit();
    }

    $dsn= parse_url($_SERVER['argv'][1]);
    list(, $database, $table)= explode('/', $dsn['path'], 3);

    // $l= &Logger::getInstance();
    // $cat= &$l->getCategory();
    // $cat->addAppender(new FileAppender('php://stderr'));

    $db= new SPSybase(array(
      'host'    => $dsn['host'],
      'user'    => $dsn['user'],
      'pass'    => $dsn['pass']
    ));
    try(); {
        $db->connect();
	$db->select_db($database);
    } if (catch('Exception', $e)) {
        $e->printStackTrace();
	exit;
    }	
    
    try(); {
        $gen= &DBXmlGenerator::createFromTable(DBTable::getByName(new SybaseDBAdapter($db), $table)); 
    } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit;
    }
    echo $gen->getSource();
?>
