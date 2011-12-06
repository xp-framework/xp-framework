<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.tds';
  
  uses('rdbms.DSN');

  /**
   * Supplies hostname and port to connect to from a given "virtual"
   * name by looking up the details, e.g. in a file.
   *
   *
   * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.dc38421_1500/html/ntconfig/X12040.htm
   * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.dc35823_1500/html/uconfig/X28613.htm
   * @see   http://www.freetds.org/userguide/freetdsconf.htm
   */
  interface rdbms·tds·ConnectionLookup {
    
    /**
     * Look up DSN
     *
     * @param   rdbms.DSN dsn
     */
    public function lookup($dsn);
  }
?>
