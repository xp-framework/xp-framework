<?php namespace net\xp_framework\unittest\core\types;

use unittest\TestCase;
use lang\types\String;

/**
 * TestCase
 *
 * @see   xp://lang.types.String
 */
class StringTest extends TestCase {

  #[@test]
  public function stringIsEqualToItself() {
    $a= new String('');
    $this->assertTrue($a->equals($a));
  }

  #[@test]
  public function stringIsEqualSameString() {
    $this->assertTrue(create(new String('ABC'))->equals(new String('ABC')));
  }

  #[@test]
  public function stringIsNotEqualToDifferentString() {
    $this->assertFalse(create(new String('ABC'))->equals(new String('CBA')));
  }

  #[@test, @expect('lang.FormatException')]
  public function incompleteMultiByteCharacter() {
    new String('ä', 'utf-8');
  }

  #[@test, @expect('lang.FormatException')]
  public function illegalCharacter() {
    new String('ä', 'US-ASCII');
  }

  #[@test]
  public function usAsciiString() {
    $str= new String('Hello');
    $this->assertEquals(new \lang\types\Bytes('Hello'), $str->getBytes());
    $this->assertEquals(5, $str->length());
  }

  #[@test]
  public function integerString() {
    $str= new String(1);
    $this->assertEquals(new \lang\types\Bytes('1'), $str->getBytes());
    $this->assertEquals(1, $str->length());
  }

  #[@test]
  public function characterString() {
    $str= new String(new \lang\types\Character('Ä'));
    $this->assertEquals(new \lang\types\Bytes("\304"), $str->getBytes('iso-8859-1'));
    $this->assertEquals(1, $str->length());
  }

  #[@test]
  public function doubleString() {
    $str= new String(1.1);
    $this->assertEquals(new \lang\types\Bytes('1.1'), $str->getBytes());
    $this->assertEquals(3, $str->length());
  }

  #[@test]
  public function trueString() {
    $str= new String(TRUE);
    $this->assertEquals(new \lang\types\Bytes('1'), $str->getBytes());
    $this->assertEquals(1, $str->length());
  }

  #[@test]
  public function falseString() {
    $str= new String(FALSE);
    $this->assertEquals(new \lang\types\Bytes(''), $str->getBytes());
    $this->assertEquals(0, $str->length());
  }

  #[@test]
  public function nullString() {
    $str= new String(NULL);
    $this->assertEquals(new \lang\types\Bytes(''), $str->getBytes());
    $this->assertEquals(0, $str->length());
  }

  #[@test]
  public function umlautString() {
    $str= new String('Hällo');
    $this->assertEquals(new \lang\types\Bytes('HÃ¤llo'), $str->getBytes('utf-8'));
    $this->assertEquals(5, $str->length());
  }

  #[@test]
  public function utf8String() {
    $this->assertEquals(
      new String('HÃ¤llo', 'utf-8'),
      new String('Hällo', 'iso-8859-1')
    );
  }

  #[@test, @ignore('Does not work with all iconv implementations')]
  public function transliteration() {
    $this->assertEquals(
      'Trenciansky kraj', 
      create(new String('TrenÄiansky kraj', 'utf-8'))->toString()
    );
  }

  #[@test]
  public function indexOf() {
    $str= new String('Hällo');
    $this->assertEquals(0, $str->indexOf('H'));
    $this->assertEquals(1, $str->indexOf('ä'));
    $this->assertEquals(1, $str->indexOf(new String('ä')));
    $this->assertEquals(-1, $str->indexOf(''));
    $this->assertEquals(-1, $str->indexOf('4'));
  }

  #[@test]
  public function lastIndexOf() {
    $str= new String('HälloH');
    $this->assertEquals($str->length()- 1, $str->lastIndexOf('H'));
    $this->assertEquals(1, $str->lastIndexOf('ä'));
    $this->assertEquals(1, $str->lastIndexOf(new String('ä')));
    $this->assertEquals(-1, $str->lastIndexOf(''));
    $this->assertEquals(-1, $str->lastIndexOf('4'));
  }

  #[@test]
  public function contains() {
    $str= new String('Hällo');
    $this->assertTrue($str->contains('H'));
    $this->assertTrue($str->contains('ä'));
    $this->assertTrue($str->contains('o'));
    $this->assertFalse($str->contains(''));
    $this->assertFalse($str->contains('4'));
  }

  #[@test]
  public function substring() {
    $str= new String('Hällo');
    $this->assertEquals(new String('ällo'), $str->substring(1));
    $this->assertEquals(new String('ll'), $str->substring(2, -1));
    $this->assertEquals(new String('o'), $str->substring(-1, 1));
  }

  #[@test]
  public function startsWith() {
    $str= new String('www.müller.com');
    $this->assertTrue($str->startsWith('www.'));
    $this->assertFalse($str->startsWith('ww.'));
    $this->assertFalse($str->startsWith('müller'));
  }

  #[@test]
  public function endsWith() {
    $str= new String('www.müller.com');
    $this->assertTrue($str->endsWith('.com'));
    $this->assertTrue($str->endsWith('üller.com'));
    $this->assertFalse($str->endsWith('.co'));
    $this->assertFalse($str->endsWith('müller'));
  }

  #[@test]
  public function concat() {
    $this->assertEquals(new String('www.müller.com'), create(new String('www'))
      ->concat(new \lang\types\Character('.'))
      ->concat('müller')
      ->concat('.com')
    );
  }
  
  #[@test]
  public function hashesOfSameStringEqual() {
    $this->assertEquals(
      create(new String(''))->hashCode(),
      create(new String(''))->hashCode()
    );
  }

  #[@test]
  public function hashesOfDifferentStringsNotEqual() {
    $this->assertNotEquals(
      create(new String('A'))->hashCode(),
      create(new String('B'))->hashCode()
    );
  }
  
  #[@test]
  public function charAt() {
    $this->assertEquals(new \lang\types\Character('ü'), create(new String('www.müller.com'))->charAt(5));
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function charAtNegative() {
    create(new String('ABC'))->charAt(-1);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function charAtAfterEnd() {
    create(new String('ABC'))->charAt(4);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function charAtEnd() {
    create(new String('ABC'))->charAt(3);
  }

  #[@test]
  public function replace() {
    $str= new String('www.müller.com');
    $this->assertEquals(new String('müller'), $str->replace('www.')->replace('.com'));
    $this->assertEquals(new String('muller'), $str->replace('ü', 'u'));
  }

  #[@test]
  public function offsetSet() {
    $str= new String('www.müller.com');
    $str[5]= 'u';
    $this->assertEquals(new String('www.muller.com'), $str);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetSetNegative() {
    $str= new String('www.müller.com');
    $str[-1]= 'u';
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetSetAfterEnd() {
    $str= new String('www.müller.com');
    $str[$str->length()]= 'u';
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function offsetSetIncorrectLength() {
    $str= new String('www.müller.com');
    $str[5]= 'ue';
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function offsetAdd() {
    $str= new String('www.müller.com');
    $str[]= '.';
  }

  #[@test]
  public function offsetGet() {
    $str= new String('www.müller.com');
    $this->assertEquals(new \lang\types\Character('ü'), $str[5]);
  }

  #[@test]
  public function offsetExists() {
    $str= new String('www.müller.com');
    $this->assertTrue(isset($str[0]), 0);
    $this->assertTrue(isset($str[5]), 5);
    $this->assertFalse(isset($str[-1]), -1);
    $this->assertFalse(isset($str[1024]), 1024);
  }

  #[@test]
  public function offsetUnsetAtBeginning() {
    $str= new String('www.müller.com');
    unset($str[0]);
    $this->assertEquals(new String('ww.müller.com'), $str);
  }

  #[@test]
  public function offsetUnsetAtEnd() {
    $str= new String('www.müller.com');
    unset($str[$str->length()- 1]);
    $this->assertEquals(new String('www.müller.co'), $str);
  }

  #[@test]
  public function offsetUnsetInBetween() {
    $str= new String('www.müller.com');
    unset($str[5]);
    $this->assertEquals(new String('www.mller.com'), $str);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetUnsetNegative() {
    $str= new String('www.müller.com');
    unset($str[-1]);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetUnsetAfterEnd() {
    $str= new String('www.müller.com');
    unset($str[1024]);
  }

  #[@test]
  public function worksWithEchoStatement() {
    ob_start();
    echo new String('www.müller.com');
    $this->assertEquals('www.müller.com', ob_get_clean());
  }

  #[@test]
  public function stringCast() {
    $this->assertEquals('www.müller.com', (string)new String('www.müller.com'));
  }

  #[@test]
  public function usedInStringFunction() {
    $this->assertEquals(
      'ftp.müller.com', 
      str_replace('www', 'ftp', new String('www.müller.com')
    ));
  }

  #[@test, @expect('lang.FormatException')]
  public function getUmlautsAsAsciiBytes() {
    create(new String('äöü', 'iso-8859-1'))->getBytes('ASCII');
  }

  #[@test]
  public function getAsciiAsAsciiBytes() {
    $this->assertEquals(
      new \lang\types\Bytes('aou'), 
      create(new String('aou', 'iso-8859-1'))->getBytes('ASCII')
    );
  }
}
