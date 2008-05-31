<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.DriverManager',
    'unittest.TestCase'
  );

  /**
   * Test rdbms tokenizer
   *
   * @see       xp://rdbms.StatementFormatter
   * @purpose   Unit Test
   */
  class TokenizerTest extends TestCase {
    public
      $conn = array();
      
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      parent::__construct($name);
      $this->conn['sybase']= DriverManager::getConnection('sybase://localhost:1999/');
      $this->conn['mysql']= DriverManager::getConnection('mysql://localhost/');
      $this->conn['pgsql']= DriverManager::getConnection('pgsql://localhost/');
    }
      
    /**
     * Test percent token
     *
     */
    #[@test]
    public function testPercentToken() {
      static $expect= array(
        'sybase'  => 'select * from test where name like \'%.de\'',
        'mysql'   => 'select * from test where name like \'%.de\'',
        'pgsql'   => 'select * from test where name like \'%.de\'',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select * from test where name like "%.de"',
        $value->prepare('select * from test where name like "%%.de"', 1),
        $key
      );
    }

    /**
     * Test unknown token
     *
     */
    #[@test]
    public function testUnknownToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select * from test where name like "%X"',
        $value->prepare('select * from test where name like "%X"', 1),
        $key
      );
    }
    
    /**
     * Test integer token
     *
     */
    #[@test]
    public function testIntegerToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select 1 as intval',
        $value->prepare('select %d as intval', 1),
        $key
      );
    }

    /**
     * Test float token
     *
     */
    #[@test]
    public function testFloatToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select 6.1 as floatval',
        $value->prepare('select %f as floatval', 6.1),
        $key
      );
    }

    /**
     * Test string token
     *
     */
    #[@test]
    public function testStringToken() {
      static $expect= array(
        'sybase'  => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        'mysql'   => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        'pgsql'   => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        // Add other built-in rdbms engines when added to the test!
      );
      
      foreach ($expect as $key => $value) $this->assertEquals(
        $value,
        $this->conn[$key]->prepare('select %s as strval', '"Hello", Tom\'s friend said'),
        $key
      );
    }

    /**
     * Test backslash escaping
     *
     */
    #[@test]
    public function testBackslash() {
      static $expect= array(
        'sybase'  => 'select \'Hello \\ \' as strval',    // one backslash
        'mysql'   => 'select \'Hello \\\\ \' as strval',  // two backslashes
        'pgsql'   => 'select \'Hello \\ \' as strval',    // one backslash
        // TBD: Other built-in rdbms engines
      );
      
      foreach ($expect as $key => $value) $this->assertEquals(
        $value,
        $this->conn[$key]->prepare('select %s as strval', 'Hello \\ '),
        $key
      );
    }
    
    /**
     * Test array of integer token
     *
     */
    #[@test]
    public function testIntegerArrayToken() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'select * from news where news_id in ()',
          $value->prepare('select * from news where news_id in (%d)', array()),
          $key
        );
        $this->assertEquals(
          'select * from news where news_id in (1, 2, 3)',
          $value->prepare('select * from news where news_id in (%d)', array(1, 2, 3)),
          $key
        );
      }
    }

    /**
     * Test array of date token
     *
     */
    #[@test]
    public function testDateArrayToken() {
      static $expect= array(
        'sybase'  => "'1977-12-14 12:00AM', '1977-12-15 12:00AM'",
        'mysql'   => "'1977-12-14 00:00:00', '1977-12-15 00:00:00'",
        'pgsql'   => "'1977-12-14 00:00:00', '1977-12-15 00:00:00'",
        // Add other built-in rdbms engines when added to the test!
      );

      $d1= new Date('1977-12-14');
      $d2= new Date('1977-12-15');
      
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'select * from news where news_id in ('.$expect[$key].')',
          $value->prepare('select * from news where news_id in (%s)', array($d1, $d2)),
          $key
        );
      }
    }
    
    /**
     * Test leading token
     *
     */
    #[@test]
    public function testLeadingToken() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select 1',
        $value->prepare('%c', 'select 1'),
        $key
      );
    }
    
    /**
     * Test random argument access
     *
     */
    #[@test]
    public function testRandomAccess() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select column from table',
        $value->prepare('select %2$c from %1$c', 'table', 'column'),
        $key
      );
    }
    
    /**
     * Test passing null values
     *
     */
    #[@test]
    public function testPassNullValues() {
      foreach ($this->conn as $key => $value) $this->assertEquals(
        'select NULL from NULL',
        $value->prepare('select %2$c from %1$c', NULL, NULL),
        $key
      );
    }
    
    /**
     * Test accessing non-passed values (eg. values with a higher
     * ordinal than available).
     *
     */
    #[@test]
    public function testAccessNonexistant() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare('%2$c', NULL);
          $this->fail('Expected exception not caught');
        } catch (SQLStateException $expected) {
          // OK
        }
      }
    }

    /**
     * Test percent char within a string
     *
     */
    #[@test]
    public function percentSignInPrepareString() {
      static $expect= array(
        'sybase'  => 'insert into table values (\'value\', "str%&ing", \'value\')',
        'mysql'   => 'insert into table values (\'value\', "str%&ing", \'value\')',
        'pgsql'   => 'insert into table values (\'value\', "str%&ing", \'value\')'
      );
      
      foreach ($expect as $key => $value) $this->assertEquals(
        $value,
        $this->conn[$key]->prepare('insert into table values (%s, "str%&ing", %s)', 'value', 'value'),
        $key
      );
    }

    /**
     * Tests percent char in values
     *
     */
    #[@test]
    public function percentSignInValues() {
      static $expect= array(
        'sybase'  => "select '%20'",
        'mysql'   => "select '%20'",
        'pgsql'   => "select '%20'"
      );

      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          $expect[$key],
          $value->prepare('select %s', '%20'),
          $key
        );
      }
    } 
    
    /**
     * Test huge numbers in %d token
     *
     * @see     bug://1
     */
    #[@test]
    public function testHugeIntegerNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'NULL',
          $value->prepare('%d', 'Helo 123 Moto'),
          $key
        );
        $this->assertEquals(
          '0',
          $value->prepare('%d', '0'),
          $key
        );
        $this->assertEquals(
          '999999999999999999999999999',
          $value->prepare('%d', '999999999999999999999999999'),
          $key
        );
        $this->assertEquals(
          '-999999999999999999999999999',
          $value->prepare('%d', '-999999999999999999999999999'),
          $key
        );
      }
    }
    
    /**
     * Test huge numbers in %f token
     *
     * @see     bug://1
     */
    #[@test]
    public function testHugeFloatNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'NULL',
          $value->prepare('%d', 'Helo 123 Moto'),
          $key
        );
        $this->assertEquals(
          '0.0',
          $value->prepare('%d', '0.0'),
          $key
        );
        $this->assertEquals(
          '0.00000000000000234E03',
          $value->prepare('%d', '0.00000000000000234E03'),
          $key
        );
        $this->assertEquals(
          '1232342354362.00000000000000234e-14',
          $value->prepare('%d', '1232342354362.00000000000000234e-14'),
          $key
        );
      }
    }

    /**
     * Tests empty string in %d token
     *
     */
    #[@test]
    public function testEmptyStringAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'NULL',
          $value->prepare('%d', ''),
          $key
        );
      }
    }

    /**
     * Tests dash ("-") in %d token
     *
     */
    #[@test]
    public function testDashAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'NULL',
          $value->prepare('%d', '-'),
          $key
        );
      }
    }

    /**
     * Tests dot (".") in %d token
     *
     */
    #[@test]
    public function testDotAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'NULL',
          $value->prepare('%d', '.'),
          $key
        );
      }
    }
 
    /**
     * Tests plus ("+") in %d token
     *
     */
    #[@test]
    public function testPlusAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals(
          'NULL',
          $value->prepare('%d', '+'),
          $key
        );
      }
    } 
  }
?>
