<?php namespace net\xp_framework\unittest\text\csv;

use unittest\TestCase;
use text\csv\CsvListReader;
use text\csv\CsvListWriter;
use text\csv\processors\AsInteger;
use text\csv\processors\AsDouble;
use text\csv\processors\AsDate;
use text\csv\processors\FormatDate;
use text\csv\processors\AsBool;
use text\csv\processors\FormatBool;
use text\csv\processors\AsEnum;
use text\csv\processors\FormatEnum;
use text\csv\processors\FormatNumber;
use text\csv\processors\constraint\Optional;
use text\csv\processors\constraint\Required;
use text\csv\processors\constraint\Unique;
use net\xp_framework\unittest\core\Coin;
use io\streams\MemoryInputStream;
use io\streams\MemoryOutputStream;


/**
 * TestCase
 *
 * @see      xp://text.csv.CellProcessor
 */
class CellProcessorTest extends TestCase {
  protected $out= null;

  /**
   * Creates a new list reader
   *
   * @param   string str
   * @param   text.csv.CsvFormat format
   * @return  text.csv.CsvListReader
   */
  protected function newReader($str, \text\csv\CsvFormat $format= null) {
    return new CsvListReader(new \io\streams\TextReader(new MemoryInputStream($str)), $format);
  }

  /**
   * Creates a new list writer
   *
   * @param   text.csv.CsvFormat format
   * @return  text.csv.CsvListWriter
   */
  protected function newWriter(\text\csv\CsvFormat $format= null) {
    $this->out= new MemoryOutputStream();
    return new CsvListWriter(new \io\streams\TextWriter($this->out), $format);
  }

  /**
   * Test AsInteger processor
   *
   */
  #[@test]
  public function asInteger() {
    $in= $this->newReader('1549;Timm')->withProcessors(array(
      new AsInteger(),
      null
    ));
    $this->assertEquals(array(1549, 'Timm'), $in->read());
  }

  /**
   * Test AsInteger processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function stringAsInteger() {
    $this->newReader('A;Timm')->withProcessors(array(
      new AsInteger(),
      null
    ))->read();
  }

  /**
   * Test AsInteger processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function emptyAsInteger() {
    $this->newReader(';Timm')->withProcessors(array(
      new AsInteger(),
      null
    ))->read();
  }

  /**
   * Test AsDouble processor
   *
   */
  #[@test]
  public function asDouble() {
    $in= $this->newReader('1.5;em')->withProcessors(array(
      new AsDouble(),
      null
    ));
    $this->assertEquals(array(1.5, 'em'), $in->read());
  }

  /**
   * Test AsDouble processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function stringAsDouble() {
    $this->newReader('A;em')->withProcessors(array(
      new AsDouble(),
      null
    ))->read();
  }

  /**
   * Test AsDouble processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function emptyAsDouble() {
    $this->newReader(';em')->withProcessors(array(
      new AsDouble(),
      null
    ))->read();
  }

  /**
   * Test AsDate processor
   *
   */
  #[@test]
  public function asDate() {
    $in= $this->newReader('2009-09-09 15:45;Order placed')->withProcessors(array(
      new AsDate(),
      null
    ));
    $this->assertEquals(array(new \util\Date('2009-09-09 15:45'), 'Order placed'), $in->read());
  }

  /**
   * Test AsDate processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function invalidAsDate() {
    $this->newReader('YYYY-MM-DD HH:MM;Order placed')->withProcessors(array(
      new AsDate(),
      null
    ))->read();
  }

  /**
   * Test AsDate processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function emptyAsDate() {
    $this->newReader(';Order placed')->withProcessors(array(
      create(new AsDate()),
      null
    ))->read();
  }

  /**
   * Test AsDate processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function emptyAsDateWithNullDefault() {
    $this->newReader(';Order placed')->withProcessors(array(
      create(new AsDate())->withDefault(null),
      null
    ))->read();
  }

  /**
   * Test AsDate processor returns the default date
   *
   */
  #[@test]
  public function emptyAsDateWithDefault() {
    $now= \util\Date::now();
    $in= $this->newReader(';Order placed')->withProcessors(array(
      create(new AsDate())->withDefault($now),
      null
    ));
    $this->assertEquals(array($now, 'Order placed'), $in->read());
  }

  /**
   * Test DateFormat processor
   *
   */
  #[@test]
  public function formatDate() {
    $writer= $this->newWriter()->withProcessors(array(
      new FormatDate('Y-m-d H:i'),
      null
    ));
    $writer->write(array(new \util\Date('2009-09-09 15:45'), 'Order placed'));
    $this->assertEquals("2009-09-09 15:45;Order placed\n", $this->out->getBytes());
  }

  /**
   * Test DateFormat processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function formatNonDate() {
    $this->newWriter()->withProcessors(array(
      new FormatDate('Y-m-d H:i'),
      null
    ))->write(array(new \lang\Object(), 'Order placed'));
  }

  /**
   * Test DateFormat processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function formatNull() {
    $this->newWriter()->withProcessors(array(
      new FormatDate('Y-m-d H:i'),
      null
    ))->write(array(null, 'Order placed'));
  }

  /**
   * Test DateFormat processor
   *
   */
  #[@test]
  public function formatNullWithDefault() {
    $now= \util\Date::now();
    $writer= $this->newWriter()->withProcessors(array(
      create(new FormatDate('Y-m-d H:i'))->withDefault($now),
      null
    ));
    $writer->write(array(null, 'Order placed'));
    $this->assertEquals($now->toString('Y-m-d H:i').";Order placed\n", $this->out->getBytes());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test]
  public function trueAsBool() {
    $in= $this->newReader('Timm;true')->withProcessors(array(
      null,
      new AsBool()
    ));
    $this->assertEquals(array('Timm', true), $in->read());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test]
  public function oneAsBool() {
    $in= $this->newReader('Timm;1')->withProcessors(array(
      null,
      new AsBool()
    ));
    $this->assertEquals(array('Timm', true), $in->read());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test]
  public function yAsBool() {
    $in= $this->newReader('Timm;Y')->withProcessors(array(
      null,
      new AsBool()
    ));
    $this->assertEquals(array('Timm', true), $in->read());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test]
  public function falseAsBool() {
    $in= $this->newReader('Timm;false')->withProcessors(array(
      null,
      new AsBool()
    ));
    $this->assertEquals(array('Timm', false), $in->read());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test]
  public function zeroAsBool() {
    $in= $this->newReader('Timm;0')->withProcessors(array(
      null,
      new AsBool()
    ));
    $this->assertEquals(array('Timm', false), $in->read());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test]
  public function nAsBool() {
    $in= $this->newReader('Timm;N')->withProcessors(array(
      null,
      new AsBool()
    ));
    $this->assertEquals(array('Timm', false), $in->read());
  }

  /**
   * Test AsBool processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function emptyAsBool() {
    $this->newReader('Timm;')->withProcessors(array(
      null,
      new AsBool()
    ))->read();
  }

  /**
   * Test FormatBool rocessor
   *
   */
  #[@test]
  public function formatTrue() {
    $writer= $this->newWriter()->withProcessors(array(
      null,
      new FormatBool()
    ));
    $writer->write(array('A', true));
    $this->assertEquals("A;true\n", $this->out->getBytes());
  }

  /**
   * Test FormatBool rocessor
   *
   */
  #[@test]
  public function formatTrueAsY() {
    $writer= $this->newWriter()->withProcessors(array(
      null,
      new FormatBool('Y', 'N')
    ));
    $writer->write(array('A', true));
    $this->assertEquals("A;Y\n", $this->out->getBytes());
  }

  /**
   * Test FormatBool rocessor
   *
   */
  #[@test]
  public function formatFalse() {
    $writer= $this->newWriter()->withProcessors(array(
      null,
      new FormatBool()
    ));
    $writer->write(array('A', false));
    $this->assertEquals("A;false\n", $this->out->getBytes());
  }

  /**
   * Test FormatBool rocessor
   *
   */
  #[@test]
  public function formatFalseAsN() {
    $writer= $this->newWriter()->withProcessors(array(
      null,
      new FormatBool('Y', 'N')
    ));
    $writer->write(array('A', false));
    $this->assertEquals("A;N\n", $this->out->getBytes());
  }

  /**
   * Test AsEnum processor
   *
   */
  #[@test]
  public function pennyCoin() {
    $in= $this->newReader('200;penny')->withProcessors(array(
      null,
      new AsEnum(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin'))
    ));
    $this->assertEquals(array('200', \net\xp_framework\unittest\core\Coin::$penny), $in->read());
  }

  /**
   * Test AsEnum processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function invalidCoin() {
    $this->newReader('200;dollar')->withProcessors(array(
      null,
      new AsEnum(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin'))
    ))->read();
  }

  /**
   * Test AsEnum processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function emptyCoin() {
    $this->newReader('200;')->withProcessors(array(
      null,
      new AsEnum(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin'))
    ))->read();
  }

  /**
   * Test FormatEnum processor
   *
   */
  #[@test]
  public function formatEnumValue() {
    $writer= $this->newWriter()->withProcessors(array(
      null,
      new FormatEnum()
    ));
    $writer->write(array('200', \net\xp_framework\unittest\core\Coin::$penny));
    $this->assertEquals("200;penny\n", $this->out->getBytes());
  }

  /**
   * Test AsEnum processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function formatNonEnum() {
    $this->newWriter()->withProcessors(array(
      null,
      new FormatEnum()
    ))->write(array('200', new \lang\Object()));
  }

  /**
   * Test FormatNumber processor
   *
   */
  #[@test]
  public function formatNumber() {
    $writer= $this->newWriter()->withProcessors(array(
      create(new FormatNumber())->withFormat(5, '.'),
      create(new FormatNumber())->withFormat(2, ',', "'")
    ));
    $writer->write(array(3.75, 10000000.5));
    $this->assertEquals("3.75000;10'000'000,50\n", $this->out->getBytes());
  }

  /**
   * Test FormatNumber processor
   *
   */
  #[@test]
  public function formatNumberNull() {
    $writer= $this->newWriter()->withProcessors(array(
      create(new FormatNumber())->withFormat(2, '.')
    ));
    $writer->write(array(null));
    $this->assertEquals("0.00\n", $this->out->getBytes());
  }

  /**
   * Test FormatNumber processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function formatNotANumber() {
    $this->newWriter()->withProcessors(array(
      create(new FormatNumber())->withFormat(2, '.')
    ))->write(array('Hello'));
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function optionalString() {
    $in= $this->newReader('200;OK')->withProcessors(array(
      null,
      new Optional()
    ));
    $this->assertEquals(array('200', 'OK'), $in->read());
  }
  
  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function optionalEmpty() {
    $in= $this->newReader('666;')->withProcessors(array(
      null,
      new Optional()
    ));
    $this->assertEquals(array('666', null), $in->read());
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function optionalEmptyWithDefault() {
    $in= $this->newReader('666;')->withProcessors(array(
      null,
      create(new Optional())->withDefault('(unknown)')
    ));
    $this->assertEquals(array('666', '(unknown)'), $in->read());
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function writeOptionalString() {
    $this->newWriter()->withProcessors(array(
      new Optional(),
      null
    ))->write(array('A', 'Test'));
    $this->assertEquals("A;Test\n", $this->out->getBytes());
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function writeOptionalEmpty() {
    $this->newWriter()->withProcessors(array(
      new Optional(),
      null
    ))->write(array('', 'Test'));
    $this->assertEquals(";Test\n", $this->out->getBytes());
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function writeOptionalNull() {
    $this->newWriter()->withProcessors(array(
      new Optional(),
      null
    ))->write(array(null, 'Test'));
    $this->assertEquals(";Test\n", $this->out->getBytes());
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function writeOptionalWithDefault() {
    $this->newWriter()->withProcessors(array(
      create(new Optional())->withDefault('(unknown)'),
      null
    ))->write(array('', 'Test'));
    $this->assertEquals("(unknown);Test\n", $this->out->getBytes());
  }

  /**
   * Test Optional processor
   *
   */
  #[@test]
  public function writeOptionalNullWithDefault() {
    $this->newWriter()->withProcessors(array(
      create(new Optional())->withDefault('(unknown)'),
      null
    ))->write(array(null, 'Test'));
    $this->assertEquals("(unknown);Test\n", $this->out->getBytes());
  }

  /**
   * Test Required processor
   *
   */
  #[@test]
  public function requiredString() {
    $in= $this->newReader('200;OK')->withProcessors(array(
      null,
      new Required()
    ));
    $this->assertEquals(array('200', 'OK'), $in->read());
  }
  
  /**
   * Test Required processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function requiredEmpty() {
    $this->newReader('666;')->withProcessors(array(
      null,
      new Required()
    ))->read();
  }

  /**
   * Test Required processor
   *
   */
  #[@test]
  public function writeRequired() {
    $this->newWriter()->withProcessors(array(
      new Required(),
      null
    ))->write(array('A', 'B'));
    $this->assertEquals("A;B\n", $this->out->getBytes());
  }

  /**
   * Test Required processor
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function writeEmptyRequired() {
    $this->newWriter()->withProcessors(array(
      new Required(),
      null
    ))->write(array('', 'Test'));
  }

  /**
   * Test chaining in Required processor
   *
   */
  #[@test]
  public function chainingRequired() {
    $in= $this->newReader('200;OK')->withProcessors(array(
      new Required(new AsInteger()),
      new Required()
    ));
    $this->assertEquals(array(200, 'OK'), $in->read());
  }

  /**
   * Test chaining in Optional processor
   *
   */
  #[@test]
  public function chainingOptional() {
    $in= $this->newReader('200;')->withProcessors(array(
      new Optional(new AsInteger()),
      new Optional(new AsInteger())
    ));
    $this->assertEquals(array(200, null), $in->read());
  }

  /**
   * Test Unique processor
   *
   */
  #[@test]
  public function readUnique() {
    $in= $this->newReader("200;OK\n200;NACK")->withProcessors(array(
      new Unique(),
      null
    ));
    $this->assertEquals(array('200', 'OK'), $in->read());
    try {
      $in->read();
      $this->fail('Duplicate value not detected', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) { }
  }

  /**
   * Test Unique processor
   *
   */
  #[@test]
  public function writeUnique() {
    $writer= $this->newWriter()->withProcessors(array(
      new Unique(),
      null,
    ));

    $writer->write(array('200', 'OK'));
    try {
      $writer->write(array('200', 'NACK'));
      $this->fail('Duplicate value not detected', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) { }

    $this->assertEquals("200;OK\n", $this->out->getBytes());
  }
  
  /**
   * Creates a cell processor that checks for an unwanted value and
   * upon encountering it, throws a FormatException
   *
   * @param   var value
   * @return  text.csv.CellProcessor
   */
  protected function newUnwantedValueProcessor($value) {
    return newinstance('text.csv.CellProcessor', array($value), '{
      protected $unwanted= NULL;
      
      public function __construct($value, $next= NULL) {
        parent::__construct($next);
        $this->unwanted= $value;
      }
      
      public function process($in) {
        if ($this->unwanted !== $in) return $this->proceed($in);
        throw new FormatException("Unwanted value ".xp::stringOf($this->unwanted)." encountered");
      }
    }');
  }

  /**
   * Test exceptions caused by processors do not break reading
   *
   */
  #[@test]
  public function processorExceptionsDoNotBreakReading() {
    $in= $this->newReader("200;OK\n404;Not found")->withProcessors(array(
      $this->newUnwantedValueProcessor('200'),
      null
    ));
    try {
      $in->read();
      $this->fail('Unwanted value not detected', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) { }
    $this->assertEquals(array('404', 'Not found'), $in->read());
  }

  /**
   * Test exceptions caused by processors do not break reading
   *
   */
  #[@test]
  public function processorExceptionsDoNotBreakReadingMultiline() {
    $in= $this->newReader("200;'OK\nThank god'\n404;'Not found\nFamous'", create(new \text\csv\CsvFormat())->withQuote("'"))->withProcessors(array(
      $this->newUnwantedValueProcessor('200'),
      null
    ));
    try {
      $in->read();
      $this->fail('Unwanted value not detected', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) { }
    $this->assertEquals(array('404', "Not found\nFamous"), $in->read());
  }

  /**
   * Test exceptions caused by processors do not break writing
   *
   */
  #[@test]
  public function processorExceptionsDoNotBreakWriting() {
    $writer= $this->newWriter()->withProcessors(array(
      $this->newUnwantedValueProcessor('200'),
      null
    ));

    try {
      $writer->write(array('200', 'OK'));
      $this->fail('Unwanted value not detected', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) { }

    $writer->write(array('404', 'Not found'));
    $this->assertEquals("404;Not found\n", $this->out->getBytes());
  }

  /**
   * Test exceptions caused by processors do not break writing
   *
   */
  #[@test]
  public function processorExceptionsDoNotCausePartialWriting() {
    $writer= $this->newWriter()->withProcessors(array(
      null,
      $this->newUnwantedValueProcessor('Internal Server Error')
    ));

    try {
      $writer->write(array('500', 'Internal Server Error'));
      $this->fail('Unwanted value not detected', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) { }

    $writer->write(array('404', 'Not found'));
    $this->assertEquals("404;Not found\n", $this->out->getBytes());
  }
}
