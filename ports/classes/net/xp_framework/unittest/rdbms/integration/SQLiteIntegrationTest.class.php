<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * SQLite integration test
   *
   * @ext       sqlite
   */
  class SQLiteIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * Retrieve dsn
     *
     * @return  string
     */
    public function _dsn() {
      return 'sqlite';
    }
    
    /**
     * Create autoincrement table
     *
     * @param   string name
     */
    protected function createAutoIncrementTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk integer primary key, username varchar(30))', $name);
    }

    /**
     * Ignored
     *
     */
    #[@test, @ignore('SQLite does not use credentials')]
    public function connectFailedThrowsException() {
      // Intentionally empty
    }
    
    /**
     * Ignored
     *
     */
    #[@test, @ignore('Somehow AI does not work')]
    public function identity() {
      // Intentionally empty
    }

    /**
     * SQLite is typeless - overwritten to use cast()
     *
     */
    #[@test]
    public function simpleSelect() {
      $this->assertEquals(
        array(array('foo' => 1)), 
        $this->db()->select('cast(1, "int") as foo')
      );
    }
    
    /**
     * SQLite is typeless - overwritten to use cast()
     *
     */
    #[@test]
    public function simpleQuery() {
      $q= $this->db()->query('select cast(1, "int") as foo');
      $this->assertSubclass($q, 'rdbms.ResultSet');
      $this->assertEquals(1, $q->next('foo'));
    }

    /**
     * SQLite is typeless - overwritten to use cast()
     *
     */
    #[@test]
    public function selectInteger() {
      $this->assertEquals(1, $this->db()->query('select cast(1, "int") as value')->next('value'));
    }

    /**
     * SQLite is typeless - overwritten to use cast()
     *
     */
    #[@test]
    public function selectFloat() {
      $this->assertEquals(0.5, $this->db()->query('select cast(0.5, "float") as value')->next('value'));
      $this->assertEquals(1.0, $this->db()->query('select cast(1.0, "float") as value')->next('value'));
    }

    /**
     * Test selecting date values returns util.Date objects
     *
     */
    #[@test]
    public function selectDate() {
      $cmp= new Date('2009-08-14 12:45:00');
      $result= $this->db()->query('select cast(datetime(%s), "date") as value', $cmp)->next('value');
      
      $this->assertSubclass($result, 'util.Date');
      $this->assertEquals($cmp->toString('Y-m-d'), $result->toString('Y-m-d'));
    }
  }
?>
