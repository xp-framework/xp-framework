<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.rdbms.ConnectionManagerTest',
    'util.Properties'
  );

  /**
   * Tests for configured connection managers
   *
   * @see   xp://rdbms.ConnectionManager#configure
   * @see   xp://net.xp_framework.unittest.rdbms.ConnectionManagerTest
   */
  class ConfiguredConnectionManagerTest extends ConnectionManagerTest {

    /**
     * Returns an instance with a given number of DSNs
     *
     * @param   [:string] dsns
     * @return  rdbms.ConnectionManager
     */
    protected function instanceWith($dsns) {
      $properties= '';
      foreach ($dsns as $name => $dsn) {
        $properties.= '['.$name."]\ndsn=\"".$dsn."\"\n";
      }
      $cm= ConnectionManager::getInstance();
      $cm->configure(Properties::fromString($properties));
      return $cm;
    }
  }
?>
