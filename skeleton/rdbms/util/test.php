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

    // $l= &Logger::getInstance();
    // $cat= &$l->getCategory();
    // $cat->addAppender(new FileAppender('php://stderr'));

    $adapter= &new SybaseDBAdapter(new SPSybase(array(
        'host'    => 'gurke',
        'user'    => 'puretec',
        'pass'    => 'gkhei43'
    )));
    try(); {
        $gen= &DBXmlGenerator::createFromTable(
            DBTable::getByName($adapter, 'techauftrag')
        );
    } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit;
    }
    echo $gen->getSource();
?>
