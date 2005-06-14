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
      $conn = NULL,
      $dsn  = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string dsn
     */
    function __construct($name, $dsn) {
      $this->dsn= $dsn;
      parent::__construct($name);
    }
      
    /**
     * Setup function
     *
     * @access  public
     * @throws  rdbms.DriverNotSupportedException
     */
    function setUp() {
      try(); {
        $this->conn= &DriverManager::getConnection($this->dsn);
      } if (catch('DriverNotSupportedException', $e)) {
        throw (new PrerequisitesNotMetError(
          PREREQUISITE_INITFAILED,
          $e,
          $dsn
        ));
      }
    }
    
    /**
     * Tear down function
     *
     * @access  public
     */
    function tearDown() {
      $this->conn->close();
    }

    /**
     * Test percent token
     *
     * @access  public
     */
    #[@test]
    function testPercentToken() {
      $this->assertEquals(
        $this->conn->prepare('select * from test where name like "%%.de"', 1),
        'select * from test where name like "%.de"'
      );
    }

    /**
     * Test unknown token
     *
     * @access  public
     */
    #[@test]
    function testUnknownToken() {
      $this->assertEquals(
        $this->conn->prepare('select * from test where name like "%X"', 1),
        'select * from test where name like "%X"'
      );
    }
    
    /**
     * Test integer token
     *
     * @access  public
     */
    #[@test]
    function testIntegerToken() {
      $this->assertEquals(
        $this->conn->prepare('select %d as intval', 1),
        'select 1 as intval'
      );
    }

    /**
     * Test float token
     *
     * @access  public
     */
    #[@test]
    function testFloatToken() {
      $this->assertEquals(
        $this->conn->prepare('select %f as floatval', 6.1),
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
      
      $this->assertEquals(
        $this->conn->prepare('select %s as strval', '"Hello", Tom\'s friend said'),
        $expect[substr($this->dsn, 0, strpos($this->dsn, '://'))]
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
      
      $this->assertEquals(
        $this->conn->prepare('select %s as strval', 'Hello \\ '),
        $expect[substr($this->dsn, 0, strpos($this->dsn, '://'))]
      );
    }
    
    /**
     * Test array of integer token
     *
     * @access  public
     */
    #[@test]
    function testIntegerArrayToken() {
      $this->assertEquals(
        $this->conn->prepare('select * from news where news_id in (%d)', array()),
        'select * from news where news_id in ()'
      );
      $this->assertEquals(
        $this->conn->prepare('select * from news where news_id in (%d)', array(1, 2, 3)),
        'select * from news where news_id in (1, 2, 3)'
      );
    }
    
    /**
     * Test leading token
     *
     * @access  public
     */
    #[@test]
    function testLeadingToken() {
      $this->assertEquals(
        $this->conn->prepare('%c', 'select 1'),
        'select 1'
      );
    } 
  }
?>
