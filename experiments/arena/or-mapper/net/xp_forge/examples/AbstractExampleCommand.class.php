<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.cmd.Command',
    'net.xp_forge.ds.test.Person',
    'net.xp_forge.ds.test.Account'
  );
  
  $package= 'net.xp_forge.examples';

  /**
   * Base class for demos
   *
   * @purpose  Base class
   */
  abstract class net·xp_forge·examples·AbstractExampleCommand extends Command {
  
    /**
     * Connect to the database
     *
     * @param   rdbms.DBConnection
     */
    #[@inject(type= 'rdbms.DBConnection', name= 'test-ds')]
    public function connect($conn) {
      $conn->connect();
    }
  }
?>
