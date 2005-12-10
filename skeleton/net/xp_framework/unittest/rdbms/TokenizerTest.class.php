<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.DriverManager', 
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test rdbms tokenizer
   *
   * @purpose  Unit Test
   */
  class TokenizerTest extends TestCase {
    var
      $conn = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      parent::__construct($name);
      $this->conn['sybase']= &DriverManager::getConnection('sybase://localhost:1999/');
      $this->conn['mysql']= &DriverManager::getConnection('mysql://localhost/');
      $this->conn['pgsql']= &DriverManager::getConnection('pgsql://localhost/');
    }
      
    /**
     * Test percent token
     *
     * @access  public
     */
    #[@test]
    function testPercentToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $value->prepare('select * from test where name like "%%.de"', 1),
        'select * from test where name like "%.de"',
        $key
      );
    }

    /**
     * Test unknown token
     *
     * @access  public
     */
    #[@test]
    function testUnknownToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $value->prepare('select * from test where name like "%X"', 1),
        'select * from test where name like "%X"',
        $key
      );
    }
    
    /**
     * Test integer token
     *
     * @access  public
     */
    #[@test]
    function testIntegerToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $value->prepare('select %d as intval', 1),
        'select 1 as intval',
        $key
      );
    }

    /**
     * Test float token
     *
     * @access  public
     */
    #[@test]
    function testFloatToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $value->prepare('select %f as floatval', 6.1),
        'select 6.1 as floatval'
      );
    }

    /**
     * Test string token
     *
     * @access  public
     */
    #[@test]
    function testStringToken() {
      static $expect= array(
        'sybase' => 'select """Hello"", Tom\'s friend said" as strval',
        'mysql'  => 'select "\"Hello\", Tom\'s friend said" as strval',
        // TBD: Other built-in rdbms engines
      );
      
      foreach ($expect as $key => $value) $this->assertEquals(
        $this->conn[$key]->prepare('select %s as strval', '"Hello", Tom\'s friend said'),
        $value,
        $key
      );
    }

    /**
     * Test backslash escaping
     *
     * @access  public
     */
    #[@test]
    function testBackslash() {
      static $expect= array(
        'sybase' => 'select "Hello \\ " as strval',     // one backslash
        'mysql'  => 'select "Hello \\\\ " as strval',   // two backslashes
        // TBD: Other built-in rdbms engines
      );
      
      foreach ($expect as $key => $value) $this->assertEquals(
        $this->conn[$key]->prepare('select %s as strval', 'Hello \\ '),
        $value,
        $key
      );
    }
    
    /**
     * Test array of integer token
     *
     * @access  public
     */
    #[@test]
    function testIntegerArrayToken() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          $value->prepare('select * from news where news_id in (%d)', array()),
          'select * from news where news_id in ()',
          $key
        );
        $this->assertEquals(
          $value->prepare('select * from news where news_id in (%d)', array(1, 2, 3)),
          'select * from news where news_id in (1, 2, 3)',
          $key
        );
      }
    }
    
    /**
     * Test leading token
     *
     * @access  public
     */
    #[@test]
    function testLeadingToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $value->prepare('%c', 'select 1'),
        'select 1',
        $key
      );
    } 
  }
?>
