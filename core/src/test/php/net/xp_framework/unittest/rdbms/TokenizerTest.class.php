<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('rdbms.DBConnection', 'unittest.TestCase');

  /**
   * Test rdbms tokenizer
   *
   * @see   xp://rdbms.StatementFormatter
   */
  abstract class TokenizerTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected abstract function fixture();

    /**
     * Sets up a Database Object for the test
     *
     */
    public function setUp() {
      $this->fixture= $this->fixture();
    }

    /**
     * Test double-quoted string
     *
     */
    #[@test]
    public function doubleQuotedString() {
      $this->assertEquals(
        'select \'Uber\' + \' \' + \'Coder\' as realname',
        $this->fixture->prepare('select "Uber" + " " + "Coder" as realname')
      );
    }

    /**
     * Test single-quoted string
     *
     */
    #[@test]
    public function singleQuotedString() {
      $this->assertEquals(
        'select \'Uber\' + \' \' + \'Coder\' as realname',
        $this->fixture->prepare("select 'Uber' + ' ' + 'Coder' as realname")
      );
    }

    /**
     * Test double-quoted string with escaped double quotes inside
     *
     */
    #[@test]
    public function doubleQuotedStringWithEscapes() {
      $this->assertEquals(
        'select \'Quote signs: " \'\' ` \'\'\' as test',
        $this->fixture->prepare('select "Quote signs: "" \' ` \'" as test')
      );
    }

    /**
     * Test single-quoted string with escaped single quotes inside
     *
     */
    #[@test]
    public function singleQuotedStringWithEscapes() {
      $this->assertEquals(
        'select \'Quote signs: " \'\' ` \'\'\' as test',
        $this->fixture->prepare("select 'Quote signs: \" '' ` ''' as test")
      );
    }
      
    /**
     * Test escaped percent token inside a string (backwards compat!)
     *
     */
    #[@test]
    public function escapedPercentTokenInString() {
      $this->assertEquals(
        'select * from test where name like \'%.de\'',
        $this->fixture->prepare('select * from test where name like "%%.de"')
      );
    }

    /**
     * Test double-escaped percent token inside a string (backwards compat!)
     *
     */
    #[@test]
    public function doubleEscapedPercentTokenInString() {
      $this->assertEquals(
        'select * from test where url like \'http://%%20\'',
        $this->fixture->prepare('select * from test where url like "http://%%%%20"')
      );
    }

    /**
     * Test double-escaped percent token inside a value
     *
     */
    #[@test]
    public function escapedPercentTokenInValue() {
      $this->assertEquals(
        'select * from test where url like \'http://%%20\'',
        $this->fixture->prepare('select * from test where url like %s', 'http://%%20')
      );
    }

    /**
     * Test percent token
     *
     */
    #[@test]
    public function percentTokenInString() {
      $this->assertEquals(
        'select * from test where name like \'%.de\'',
        $this->fixture->prepare('select * from test where name like "%.de"')
      );
    }

    /**
     * Test unknown token inside a string
     *
     */
    #[@test]
    public function unknownTokenInString() {
      $this->assertEquals(
        'select * from test where name like \'%X\'',
        $this->fixture->prepare('select * from test where name like "%X"')
      );
    }

    /**
     * Test unknown token
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function unknownToken() {
      $this->fixture->prepare('select * from test where name like %X');
    }

    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function unclosedDoubleQuotedString() {
      $this->fixture->prepare('select * from test where name = "unclosed');
    }

    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function unclosedDoubleQuotedStringEndingWithEscape() {
      $this->fixture->prepare('select * from test where name = "unclosed""');
    }
    
    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function unclosedSingleQuotedString() {
      $this->fixture->prepare("select * from test where name = 'unclosed");
    }

    /**
     * Test an unclosed string leads to a rdbms.SQLStateException
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function unclosedSingleQuotedStringEndingWithEscape() {
      $this->fixture->prepare("select * from test where name = 'unclosed''");
    }
    
    /**
     * Test number token
     *
     */
    #[@test]
    public function numberTokenWithPrimitive() {
      $this->assertEquals(
        'select 1 as intval',
        $this->fixture->prepare('select %d as intval', 1)
      );
    }

    /**
     * Test float token
     *
     */
    #[@test]
    public function floatTokenWithPrimitive() {
      $this->assertEquals(
        'select 6.1 as floatval',
        $this->fixture->prepare('select %f as floatval', 6.1)
      );
    }

    /**
     * Test string token
     *
     */
    #[@test]
    public function stringToken() {
      $this->assertEquals(
        'select \'"Hello", Tom\'\'s friend said\' as strval',
        $this->fixture->prepare('select %s as strval', '"Hello", Tom\'s friend said')
      );
    }

    /**
     * Test string token
     *
     */
    #[@test]
    public function stringTypeToken() {
      $this->assertEquals(
        'select \'"Hello", Tom\'\'s friend said\' as strval',
        $this->fixture->prepare('select %s as strval', new String('"Hello", Tom\'s friend said'))
      );
    }

    /**
     * Test label token
     *
     */
    #[@test]
    public function labelToken() {
      $this->assertEquals(
        'select * from \'order\'',
        $this->fixture->prepare('select * from %l', 'order')
      );
    }

    /**
     * Test date token
     *
     */
    #[@test]
    public function dateToken() {
      $t= new Date('1977-12-14');
      $this->assertEquals(
        "select * from news where date= '1977-12-14 00:00:00'",
        $this->fixture->prepare('select * from news where date= %s', $t)
      );
    }

    /**
     * Test timestamp token
     *
     */
    #[@test]
    public function timeStampToken() {
      $t= create(new Date('1977-12-14'))->getTime();
      $this->assertEquals(
        "select * from news where created= '1977-12-14 00:00:00'",
        $this->fixture->prepare('select * from news where created= %u', $t)
      );
    }

    /**
     * Test backslash escaping
     *
     */
    #[@test]
    public function backslash() {
      $this->assertEquals(
        'select \'Hello \\ \' as strval',
        $this->fixture->prepare('select %s as strval', 'Hello \\ ')
      );
    }
    
    /**
     * Test array of integer token
     *
     */
    #[@test]
    public function integerArrayToken() {
      $this->assertEquals(
        'select * from news where news_id in ()',
        $this->fixture->prepare('select * from news where news_id in (%d)', array())
      );
      $this->assertEquals(
        'select * from news where news_id in (1, 2, 3)',
        $this->fixture->prepare('select * from news where news_id in (%d)', array(1, 2, 3))
      );
    }

    /**
     * Test array of date token
     *
     */
    #[@test]
    public function dateArrayToken() {
      $d1= new Date('1977-12-14');
      $d2= new Date('1977-12-15');
      $this->assertEquals(
        "select * from news where created in ('1977-12-14 00:00:00', '1977-12-15 00:00:00')",
        $this->fixture->prepare('select * from news where created in (%s)', array($d1, $d2))
      );
    }
    
    /**
     * Test leading token
     *
     */
    #[@test]
    public function leadingToken() {
      $this->assertEquals(
        'select 1',
        $this->fixture->prepare('%c', 'select 1')
      );
    }
    
    /**
     * Test random argument access
     *
     */
    #[@test]
    public function randomAccess() {
      $this->assertEquals(
        'select column from table',
        $this->fixture->prepare('select %2$c from %1$c', 'table', 'column')
      );
    }
    
    /**
     * Test passing null values
     *
     */
    #[@test]
    public function passNullValues() {
      $this->assertEquals(
        'select NULL from NULL',
        $this->fixture->prepare('select %2$c from %1$c', NULL, NULL)
      );
    }
    
    /**
     * Test accessing non-passed values (eg. values with a higher
     * ordinal than available).
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function accessNonexistant() {
      $this->fixture->prepare('%2$c', NULL);
    }

    /**
     * Test percent char within a string
     *
     */
    #[@test]
    public function percentSignInPrepareString() {
      $this->assertEquals(
        'insert into table values (\'value\', \'str%&ing\', \'value\')',
        $this->fixture->prepare('insert into table values (%s, "str%&ing", %s)', 'value', 'value')
      );
    }

    /**
     * Tests percent char in values
     *
     */
    #[@test]
    public function percentSignInValues() {
      $this->assertEquals(
        "select '%20'",
        $this->fixture->prepare('select %s', '%20')
      );
    } 
    
    /**
     * Test huge numbers in %d token
     *
     * @see     bug://1
     */
    #[@test]
    public function testHugeIntegerNumber() {
      $this->assertEquals(
        'NULL',
        $this->fixture->prepare('%d', 'Helo 123 Moto')
      );
      $this->assertEquals(
        '0',
        $this->fixture->prepare('%d', '0')
      );
      $this->assertEquals(
        '999999999999999999999999999',
        $this->fixture->prepare('%d', '999999999999999999999999999')
      );
      $this->assertEquals(
        '-999999999999999999999999999',
        $this->fixture->prepare('%d', '-999999999999999999999999999')
      );
    }
    
    /**
     * Test huge numbers in %f token
     *
     * @see     bug://1
     */
    #[@test]
    public function testHugeFloatNumber() {
      $this->assertEquals(
        'NULL',
        $this->fixture->prepare('%d', 'Helo 123 Moto')
      );
      $this->assertEquals(
        '0.0',
        $this->fixture->prepare('%d', '0.0')
      );
      $this->assertEquals(
        '0.00000000000000234E03',
        $this->fixture->prepare('%d', '0.00000000000000234E03')
      );
      $this->assertEquals(
        '1232342354362.00000000000000234e-14',
        $this->fixture->prepare('%d', '1232342354362.00000000000000234e-14')
      );
    }

    /**
     * Tests empty string in %d token
     *
     */
    #[@test]
    public function emptyStringAsNumber() {
      $this->assertEquals('NULL', $this->fixture->prepare('%d', ''));
    }

    /**
     * Tests dash ("-") in %d token
     *
     */
    #[@test]
    public function dashAsNumber() {
      $this->assertEquals('NULL', $this->fixture->prepare('%d', '-'));
    }

    /**
     * Tests dot (".") in %d token
     *
     */
    #[@test]
    public function dotAsNumber() {
      $this->assertEquals('NULL', $this->fixture->prepare('%d', '.'));
    }
 
    /**
     * Tests plus ("+") in %d token
     *
     */
    #[@test]
    public function plusAsNumber() {
      $this->assertEquals('NULL', $this->fixture->prepare('%d', '+'));
    } 

    /**
     * Tests TRUE as number
     *
     */
    #[@test]
    public function trueAsNumber() {
      $this->assertEquals('1', $this->fixture->prepare('%d', TRUE));
    } 

    /**
     * Tests FALSE as number
     *
     */
    #[@test]
    public function falseAsNumber() {
      $this->assertEquals('0', $this->fixture->prepare('%d', FALSE));
    } 
  }
?>
