<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.sybase.SybaseConnection',
    'rdbms.mysql.MySQLConnection',
    'rdbms.pgsql.PostgreSQLConnection',
    'unittest.TestCase'
  );

  /**
   * Test rdbms tokenizer
   *
   * @see       xp://rdbms.StatementFormatter
   * @purpose   Unit Test
   */
  class TokenizerTest extends TestCase {
    protected $conn= array();
      
    /**
     * Sets up a Database Object for the test
     *
     */
    public function setUp() {
      $this->conn['sybase']= new SybaseConnection(new DSN('sybase://localhost:1999/'));
      $this->conn['mysql']= new MySQLConnection(new DSN('mysql://localhost/'));
      $this->conn['pgsql']= new PostgreSQLConnection(new DSN('pgsql://localhost/'));
    }

    /**
     * Test double-quoted string
     *
     */
    #[@test]
    public function doubleQuotedString() {
      static $expect= array(
        'sybase'  => 'select \'Uber\' + \' \' + \'Coder\' as realname',
        'mysql'   => 'select \'Uber\' + \' \' + \'Coder\' as realname',
        'pgsql'   => 'select \'Uber\' + \' \' + \'Coder\' as realname',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select "Uber" + " " + "Coder" as realname'),
        $key
      );
    }

    /**
     * Test single-quoted string
     *
     */
    #[@test]
    public function singleQuotedString() {
      static $expect= array(
        'sybase'  => 'select \'Uber\' + \' \' + \'Coder\' as realname',
        'mysql'   => 'select \'Uber\' + \' \' + \'Coder\' as realname',
        'pgsql'   => 'select \'Uber\' + \' \' + \'Coder\' as realname',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare("select 'Uber' + ' ' + 'Coder' as realname"),
        $key
      );
    }

    /**
     * Test double-quoted string with escaped double quotes inside
     *
     */
    #[@test]
    public function doubleQuotedStringWithEscapes() {
      static $expect= array(
        'sybase'  => 'select \'Quote signs: " \'\' ` \'\'\' as test',
        'mysql'   => 'select \'Quote signs: " \'\' ` \'\'\' as test',
        'pgsql'   => 'select \'Quote signs: " \'\' ` \'\'\' as test',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select "Quote signs: "" \' ` \'" as test'),
        $key
      );
    }

    /**
     * Test single-quoted string with escaped single quotes inside
     *
     */
    #[@test]
    public function singleQuotedStringWithEscapes() {
      static $expect= array(
        'sybase'  => 'select \'Quote signs: " \'\' ` \'\'\' as test',
        'mysql'   => 'select \'Quote signs: " \'\' ` \'\'\' as test',
        'pgsql'   => 'select \'Quote signs: " \'\' ` \'\'\' as test',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare("select 'Quote signs: \" '' ` ''' as test"),
        $key
      );
    }
      
    /**
     * Test escaped percent token inside a string (backwards compat!)
     *
     */
    #[@test]
    public function escapedPercentTokenInString() {
      static $expect= array(
        'sybase'  => 'select * from test where name like \'%.de\'',
        'mysql'   => 'select * from test where name like \'%.de\'',
        'pgsql'   => 'select * from test where name like \'%.de\'',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from test where name like "%%.de"'),
        $key
      );
    }

    /**
     * Test double-escaped percent token inside a string (backwards compat!)
     *
     */
    #[@test]
    public function doubleEscapedPercentTokenInString() {
      static $expect= array(
        'sybase'  => 'select * from test where url like \'http://%%20\'',
        'mysql'   => 'select * from test where url like \'http://%%20\'',
        'pgsql'   => 'select * from test where url like \'http://%%20\'',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from test where url like "http://%%%%20"'),
        $key
      );
    }

    /**
     * Test double-escaped percent token inside a value
     *
     */
    #[@test]
    public function escapedPercentTokenInValue() {
      static $expect= array(
        'sybase'  => 'select * from test where url like \'http://%%20\'',
        'mysql'   => 'select * from test where url like \'http://%%20\'',
        'pgsql'   => 'select * from test where url like \'http://%%20\'',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from test where url like %s', 'http://%%20'),
        $key
      );
    }

    /**
     * Test percent token
     *
     */
    #[@test]
    public function percentTokenInString() {
      static $expect= array(
        'sybase'  => 'select * from test where name like \'%.de\'',
        'mysql'   => 'select * from test where name like \'%.de\'',
        'pgsql'   => 'select * from test where name like \'%.de\'',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from test where name like "%.de"'),
        $key
      );
    }

    /**
     * Test unknown token inside a string
     *
     */
    #[@test]
    public function unknownTokenInString() {
      static $expect= array(
        'sybase'  => 'select * from test where name like \'%X\'',
        'mysql'   => 'select * from test where name like \'%X\'',
        'pgsql'   => 'select * from test where name like \'%X\'',
        // Add other built-in rdbms engines when added to the test!
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from test where name like "%X"'),
        $key
      );
    }

    /**
     * Test unknown token
     *
     */
    #[@test]
    public function unknownToken() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare('select * from test where name like %X');
          $this->fail('Unknown token exception expected', NULL, 'rdbms.SQLStateException');
        } catch (SQLStateException $expected) { }
      }
    }

    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test]
    public function unclosedDoubleQuotedString() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare('select * from test where name = "unclosed');
          $this->fail('Unknown token exception expected', NULL, 'rdbms.SQLStateException');
        } catch (SQLStateException $expected) { }
      }
    }

    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test]
    public function unclosedDoubleQuotedStringEndingWithEscape() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare('select * from test where name = "unclosed""');
          $this->fail('Unknown token exception expected', NULL, 'rdbms.SQLStateException');
        } catch (SQLStateException $expected) { }
      }
    }
    
    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test]
    public function unclosedSingleQuotedString() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare("select * from test where name = 'unclosed");
          $this->fail('Unknown token exception expected', NULL, 'rdbms.SQLStateException');
        } catch (SQLStateException $expected) { }
      }
    }

    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test]
    public function unclosedSingleQuotedStringEndingWithEscape() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare("select * from test where name = 'unclosed''");
          $this->fail('Unknown token exception expected', NULL, 'rdbms.SQLStateException');
        } catch (SQLStateException $expected) { }
      }
    }
    
    /**
     * Test number token
     *
     */
    #[@test]
    public function numberTokenWithPrimitive() {
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
    public function floatTokenWithPrimitive() {
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
    public function stringToken() {
      static $expect= array(
        'sybase'  => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        'mysql'   => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        'pgsql'   => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        // Add other built-in rdbms engines when added to the test!
      );
      
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select %s as strval', '"Hello", Tom\'s friend said'),
        $key
      );
    }

    /**
     * Test string token
     *
     */
    #[@test]
    public function stringTypeToken() {
      static $expect= array(
        'sybase'  => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        'mysql'   => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        'pgsql'   => 'select \'"Hello", Tom\'\'s friend said\' as strval',
        // Add other built-in rdbms engines when added to the test!
      );
      
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select %s as strval', new String('"Hello", Tom\'s friend said')),
        $key
      );
    }

    /**
     * Test label token
     *
     */
    #[@test]
    public function labelToken() {
      static $expect= array(
        'sybase'  => 'select * from \'order\'',
        'mysql'   => 'select * from `order`',
        'pgsql'   => 'select * from "order"',
        // TBD: Other built-in rdbms engines
      );

      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from %l', 'order'),
        $key
      );
    }

    /**
     * Test date token
     *
     */
    #[@test]
    public function dateToken() {
      static $expect= array(
        'sybase'  => "select * from news where date= '1977-12-14 12:00AM'",
        'mysql'   => "select * from news where date= '1977-12-14 00:00:00'",
        'pgsql'   => "select * from news where date= '1977-12-14 00:00:00'",
        // Add other built-in rdbms engines when added to the test!
      );

      $t= new Date('1977-12-14');
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from news where date= %s', $t),
        $key
      );
    }

    /**
     * Test timestamp token
     *
     */
    #[@test]
    public function timeStampToken() {
      static $expect= array(
        'sybase'  => "select * from news where created= '1977-12-14 12:00AM'",
        'mysql'   => "select * from news where created= '1977-12-14 00:00:00'",
        'pgsql'   => "select * from news where created= '1977-12-14 00:00:00'",
        // Add other built-in rdbms engines when added to the test!
      );

      $t= create(new Date('1977-12-14'))->getTime();
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select * from news where created= %u', $t),
        $key
      );
    }

    /**
     * Test backslash escaping
     *
     */
    #[@test]
    public function backslash() {
      static $expect= array(
        'sybase'  => 'select \'Hello \\ \' as strval',    // one backslash
        'mysql'   => 'select \'Hello \\\\ \' as strval',  // two backslashes
        'pgsql'   => 'select \'Hello \\ \' as strval',    // one backslash
        // TBD: Other built-in rdbms engines
      );
      
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('select %s as strval', 'Hello \\ '),
        $key
      );
    }
    
    /**
     * Test array of integer token
     *
     */
    #[@test]
    public function integerArrayToken() {
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
    public function dateArrayToken() {
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
          'select * from news where created in ('.$expect[$key].')',
          $value->prepare('select * from news where created in (%s)', array($d1, $d2)),
          $key
        );
      }
    }
    
    /**
     * Test leading token
     *
     */
    #[@test]
    public function leadingToken() {
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
    public function randomAccess() {
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
    public function passNullValues() {
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
    public function accessNonexistant() {
      foreach ($this->conn as $key => $value) {
        try {
          $value->prepare('%2$c', NULL);
          $this->fail('Expected exception not caught', 'rdbms.SQLStateException', NULL);
        } catch (SQLStateException $expected) { }
      }
    }

    /**
     * Test percent char within a string
     *
     */
    #[@test]
    public function percentSignInPrepareString() {
      static $expect= array(
        'sybase'  => 'insert into table values (\'value\', \'str%&ing\', \'value\')',
        'mysql'   => 'insert into table values (\'value\', \'str%&ing\', \'value\')',
        'pgsql'   => 'insert into table values (\'value\', \'str%&ing\', \'value\')'
      );
      
      foreach ($this->conn as $key => $value) $this->assertEquals(
        $expect[$key],
        $value->prepare('insert into table values (%s, "str%&ing", %s)', 'value', 'value'),
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
    public function emptyStringAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals('NULL', $value->prepare('%d', ''), $key);
      }
    }

    /**
     * Tests dash ("-") in %d token
     *
     */
    #[@test]
    public function dashAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals('NULL', $value->prepare('%d', '-'), $key);
      }
    }

    /**
     * Tests dot (".") in %d token
     *
     */
    #[@test]
    public function dotAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals('NULL', $value->prepare('%d', '.'), $key);
      }
    }
 
    /**
     * Tests plus ("+") in %d token
     *
     */
    #[@test]
    public function plusAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals('NULL', $value->prepare('%d', '+'), $key);
      }
    } 

    /**
     * Tests TRUE as number
     *
     */
    #[@test]
    public function trueAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals('1', $value->prepare('%d', TRUE), $key);
      }
    } 

    /**
     * Tests FALSE as number
     *
     */
    #[@test]
    public function falseAsNumber() {
      foreach ($this->conn as $key => $value) {
        $this->assertEquals('0', $value->prepare('%d', FALSE), $key);
      }
    } 
  }
?>
