<?php
/* Test application
 *
 * Example:
 * php -q util/test.php | sabcmd util/xp.php.xsl
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
      printf("Usage: %s [table_name]\n", basename($_SERVER['argv'][0]));
      exit();
    }

    // $l= &Logger::getInstance();
    // $cat= &$l->getCategory();
    // $cat->addAppender(new FileAppender('php://stderr'));

    $adapter= &new SybaseDBAdapter(new SPSybase(array(
        'host'    => 'schlupa',
        'user'    => 'hotlinetool',
        'pass'    => 'serverputt'
    )));
    try(); {
        $gen= &DBXmlGenerator::createFromTable(
            DBTable::getByName($adapter, $_SERVER['argv'][1])
        );
    } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit;
    }
    echo $gen->getSource();
?>
