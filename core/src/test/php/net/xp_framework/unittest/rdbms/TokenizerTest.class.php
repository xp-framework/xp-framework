<?php namespace net\xp_framework\unittest\rdbms;
 
use rdbms\DBConnection;

/**
 * Test rdbms tokenizer
 *
 * @see   xp://rdbms.StatementFormatter
 */
abstract class TokenizerTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected abstract function fixture();

  /**
   * Sets up a Database Object for the test
   */
  public function setUp() {
    $this->fixture= $this->fixture();
  }

  #[@test]
  public function doubleQuotedString() {
    $this->assertEquals(
      'select \'Uber\' + \' \' + \'Coder\' as realname',
      $this->fixture->prepare('select "Uber" + " " + "Coder" as realname')
    );
  }

  #[@test]
  public function singleQuotedString() {
    $this->assertEquals(
      'select \'Uber\' + \' \' + \'Coder\' as realname',
      $this->fixture->prepare("select 'Uber' + ' ' + 'Coder' as realname")
    );
  }

  #[@test]
  public function doubleQuotedStringWithEscapes() {
    $this->assertEquals(
      'select \'Quote signs: " \'\' ` \'\'\' as test',
      $this->fixture->prepare('select "Quote signs: "" \' ` \'" as test')
    );
  }

  #[@test]
  public function singleQuotedStringWithEscapes() {
    $this->assertEquals(
      'select \'Quote signs: " \'\' ` \'\'\' as test',
      $this->fixture->prepare("select 'Quote signs: \" '' ` ''' as test")
    );
  }
    
  #[@test]
  public function escapedPercentTokenInString() {
    $this->assertEquals(
      'select * from test where name like \'%.de\'',
      $this->fixture->prepare('select * from test where name like "%%.de"')
    );
  }

  #[@test]
  public function doubleEscapedPercentTokenInString() {
    $this->assertEquals(
      'select * from test where url like \'http://%%20\'',
      $this->fixture->prepare('select * from test where url like "http://%%%%20"')
    );
  }

  #[@test]
  public function escapedPercentTokenInValue() {
    $this->assertEquals(
      'select * from test where url like \'http://%%20\'',
      $this->fixture->prepare('select * from test where url like %s', 'http://%%20')
    );
  }

  #[@test]
  public function percentTokenInString() {
    $this->assertEquals(
      'select * from test where name like \'%.de\'',
      $this->fixture->prepare('select * from test where name like "%.de"')
    );
  }

  #[@test]
  public function unknownTokenInString() {
    $this->assertEquals(
      'select * from test where name like \'%X\'',
      $this->fixture->prepare('select * from test where name like "%X"')
    );
  }

  #[@test, @expect('rdbms.SQLStateException')]
  public function unknownToken() {
    $this->fixture->prepare('select * from test where name like %X');
  }

  #[@test, @expect('rdbms.SQLStateException')]
  public function unclosedDoubleQuotedString() {
    $this->fixture->prepare('select * from test where name = "unclosed');
  }

  #[@test, @expect('rdbms.SQLStateException')]
  public function unclosedDoubleQuotedStringEndingWithEscape() {
    $this->fixture->prepare('select * from test where name = "unclosed""');
  }
  
  #[@test, @expect('rdbms.SQLStateException')]
  public function unclosedSingleQuotedString() {
    $this->fixture->prepare("select * from test where name = 'unclosed");
  }

  #[@test, @expect('rdbms.SQLStateException')]
  public function unclosedSingleQuotedStringEndingWithEscape() {
    $this->fixture->prepare("select * from test where name = 'unclosed''");
  }
  
  #[@test]
  public function numberTokenWithPrimitive() {
    $this->assertEquals(
      'select 1 as intval',
      $this->fixture->prepare('select %d as intval', 1)
    );
  }

  #[@test]
  public function floatTokenWithPrimitive() {
    $this->assertEquals(
      'select 6.1 as floatval',
      $this->fixture->prepare('select %f as floatval', 6.1)
    );
  }

  #[@test]
  public function stringToken() {
    $this->assertEquals(
      'select \'"Hello", Tom\'\'s friend said\' as strval',
      $this->fixture->prepare('select %s as strval', '"Hello", Tom\'s friend said')
    );
  }

  #[@test]
  public function stringTypeToken() {
    $this->assertEquals(
      'select \'"Hello", Tom\'\'s friend said\' as strval',
      $this->fixture->prepare('select %s as strval', new \lang\types\String('"Hello", Tom\'s friend said'))
    );
  }

  #[@test]
  public function labelToken() {
    $this->assertEquals(
      'select * from \'order\'',
      $this->fixture->prepare('select * from %l', 'order')
    );
  }

  #[@test]
  public function dateToken() {
    $t= new \util\Date('1977-12-14');
    $this->assertEquals(
      "select * from news where date= '1977-12-14 00:00:00'",
      $this->fixture->prepare('select * from news where date= %s', $t)
    );
  }

  #[@test]
  public function timeStampToken() {
    $t= create(new \util\Date('1977-12-14'))->getTime();
    $this->assertEquals(
      "select * from news where created= '1977-12-14 00:00:00'",
      $this->fixture->prepare('select * from news where created= %u', $t)
    );
  }

  #[@test]
  public function backslash() {
    $this->assertEquals(
      'select \'Hello \\ \' as strval',
      $this->fixture->prepare('select %s as strval', 'Hello \\ ')
    );
  }
  
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

  #[@test]
  public function dateArrayToken() {
    $d1= new \util\Date('1977-12-14');
    $d2= new \util\Date('1977-12-15');
    $this->assertEquals(
      "select * from news where created in ('1977-12-14 00:00:00', '1977-12-15 00:00:00')",
      $this->fixture->prepare('select * from news where created in (%s)', array($d1, $d2))
    );
  }
  
  #[@test]
  public function leadingToken() {
    $this->assertEquals(
      'select 1',
      $this->fixture->prepare('%c', 'select 1')
    );
  }
  
  #[@test]
  public function randomAccess() {
    $this->assertEquals(
      'select column from table',
      $this->fixture->prepare('select %2$c from %1$c', 'table', 'column')
    );
  }
  
  #[@test]
  public function passNullValues() {
    $this->assertEquals(
      'select NULL from NULL',
      $this->fixture->prepare('select %2$c from %1$c', null, null)
    );
  }
  
  #[@test, @expect('rdbms.SQLStateException')]
  public function accessNonexistant() {
    $this->fixture->prepare('%2$c', null);
  }

  #[@test]
  public function percentSignInPrepareString() {
    $this->assertEquals(
      'insert into table values (\'value\', \'str%&ing\', \'value\')',
      $this->fixture->prepare('insert into table values (%s, "str%&ing", %s)', 'value', 'value')
    );
  }

  #[@test]
  public function percentSignInValues() {
    $this->assertEquals(
      "select '%20'",
      $this->fixture->prepare('select %s', '%20')
    );
  } 
  
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

  #[@test]
  public function emptyStringAsNumber() {
    $this->assertEquals('NULL', $this->fixture->prepare('%d', ''));
  }

  #[@test]
  public function dashAsNumber() {
    $this->assertEquals('NULL', $this->fixture->prepare('%d', '-'));
  }

  #[@test]
  public function dotAsNumber() {
    $this->assertEquals('NULL', $this->fixture->prepare('%d', '.'));
  }
 
  #[@test]
  public function plusAsNumber() {
    $this->assertEquals('NULL', $this->fixture->prepare('%d', '+'));
  } 

  #[@test]
  public function trueAsNumber() {
    $this->assertEquals('1', $this->fixture->prepare('%d', true));
  } 

  #[@test]
  public function falseAsNumber() {
    $this->assertEquals('0', $this->fixture->prepare('%d', false));
  } 
}
