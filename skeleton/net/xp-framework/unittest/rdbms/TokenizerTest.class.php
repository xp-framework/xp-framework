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
     * @access  publuc
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
     * Test integer token
     *
     * @access  public
     */
    function testIntegerToken() {
      $this->assertEquals(
        'select 1 as intval',
        $this->conn->prepare('select %d as intval', 1)
      );
    }

    /**
     * Test float token
     *
     * @access  public
     */
    function testFloatToken() {
      $this->assertEquals(
        'select 6.1 as floatval',
        $this->conn->prepare('select %f as floatval', 6.1)
      );
    }

    /**
     * Test string token
     *
     * @access  public
     */
    function testStringToken() {
      static $expect= array(
        'sybase' => 'select """Hello"", Tom\'s friend said" as strval',
        // TBD: Other built-in rdbms engines
      );
      
      $this->assertEquals(
        $expect[substr($this->dsn, 0, strpos($this->dsn, '://'))],
        $this->conn->prepare('select %s as strval', '"Hello", Tom\'s friend said')
      );
    }
    
    /**
     * Test array of integer token
     *
     * @access  public
     */
    function testIntegerArrayToken() {
      $this->assertEquals(
        'select * from news where news_id in ()',
        $this->conn->prepare('select * from news where news_id in (%d)', array())
      );
      $this->assertEquals(
        'select * from news where news_id in (1, 2, 3)',
        $this->conn->prepare('select * from news where news_id in (%d)', array(1, 2, 3))
      );
    }
  }
?>
