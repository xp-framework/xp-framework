<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvListReader',
    'text.csv.CsvListWriter',
    'text.csv.processors.AsInteger',
    'text.csv.processors.AsDouble',
    'text.csv.processors.AsDate',
    'text.csv.processors.FormatDate',
    'text.csv.processors.AsBool',
    'text.csv.processors.FormatBool',
    'text.csv.processors.AsEnum',
    'text.csv.processors.FormatEnum',
    'text.csv.processors.constraint.Optional',
    'text.csv.processors.constraint.Required',
    'text.csv.processors.constraint.Unique',
    'net.xp_framework.unittest.core.Coin',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CellProcessor
   */
  class CellProcessorTest extends TestCase {
    protected $out= NULL;

    /**
     * Creates a new list reader
     *
     * @param   string str
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvListReader
     */
    protected function newReader($str, CsvFormat $format= NULL) {
      return new CsvListReader(new TextReader(new MemoryInputStream($str)), $format);
    }

    /**
     * Creates a new list writer
     *
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvListWriter
     */
    protected function newWriter(CsvFormat $format= NULL) {
      $this->out= new MemoryOutputStream();
      return new CsvListWriter(new TextWriter($this->out), $format);
    }
  
    /**
     * Test AsInteger processor
     *
     */
    #[@test]
    public function asInteger() {
      $in= $this->newReader('1549;Timm')->withProcessors(array(
        new AsInteger(),
        NULL
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
        NULL
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
        NULL
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
        NULL
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
        NULL
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
        NULL
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
        NULL
      ));
      $this->assertEquals(array(new Date('2009-09-09 15:45'), 'Order placed'), $in->read());
    }

    /**
     * Test AsDate processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function invalidAsDate() {
      $this->newReader('YYYY-MM-DD HH:MM;Order placed')->withProcessors(array(
        new AsDate(),
        NULL
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
        NULL
      ))->read();
    }

    /**
     * Test AsDate processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyAsDateWithNullDefault() {
      $this->newReader(';Order placed')->withProcessors(array(
        create(new AsDate())->withDefault(NULL),
        NULL
      ))->read();
    }

    /**
     * Test AsDate processor returns the default date
     *
     */
    #[@test]
    public function emptyAsDateWithDefault() {
      $now= Date::now();
      $in= $this->newReader(';Order placed')->withProcessors(array(
        create(new AsDate())->withDefault($now),
        NULL
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
        NULL
      ));
      $writer->write(array(new Date('2009-09-09 15:45'), 'Order placed'));
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
        NULL
      ))->write(array(new Object(), 'Order placed'));
    }

    /**
     * Test DateFormat processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function formatNull() {
      $this->newWriter()->withProcessors(array(
        new FormatDate('Y-m-d H:i'),
        NULL
      ))->write(array(NULL, 'Order placed'));
    }

    /**
     * Test DateFormat processor
     *
     */
    #[@test]
    public function formatNullWithDefault() {
      $now= Date::now();
      $writer= $this->newWriter()->withProcessors(array(
        create(new FormatDate('Y-m-d H:i'))->withDefault($now),
        NULL
      ));
      $writer->write(array(NULL, 'Order placed'));
      $this->assertEquals($now->toString('Y-m-d H:i').";Order placed\n", $this->out->getBytes());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test]
    public function trueAsBool() {
      $in= $this->newReader('Timm;true')->withProcessors(array(
        NULL,
        new AsBool()
      ));
      $this->assertEquals(array('Timm', TRUE), $in->read());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test]
    public function oneAsBool() {
      $in= $this->newReader('Timm;1')->withProcessors(array(
        NULL,
        new AsBool()
      ));
      $this->assertEquals(array('Timm', TRUE), $in->read());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test]
    public function yAsBool() {
      $in= $this->newReader('Timm;Y')->withProcessors(array(
        NULL,
        new AsBool()
      ));
      $this->assertEquals(array('Timm', TRUE), $in->read());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test]
    public function falseAsBool() {
      $in= $this->newReader('Timm;false')->withProcessors(array(
        NULL,
        new AsBool()
      ));
      $this->assertEquals(array('Timm', FALSE), $in->read());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test]
    public function zeroAsBool() {
      $in= $this->newReader('Timm;0')->withProcessors(array(
        NULL,
        new AsBool()
      ));
      $this->assertEquals(array('Timm', FALSE), $in->read());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test]
    public function nAsBool() {
      $in= $this->newReader('Timm;N')->withProcessors(array(
        NULL,
        new AsBool()
      ));
      $this->assertEquals(array('Timm', FALSE), $in->read());
    }

    /**
     * Test AsBool processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyAsBool() {
      $this->newReader('Timm;')->withProcessors(array(
        NULL,
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
        NULL,
        new FormatBool()
      ));
      $writer->write(array('A', TRUE));
      $this->assertEquals("A;true\n", $this->out->getBytes());
    }

    /**
     * Test FormatBool rocessor
     *
     */
    #[@test]
    public function formatTrueAsY() {
      $writer= $this->newWriter()->withProcessors(array(
        NULL,
        new FormatBool('Y', 'N')
      ));
      $writer->write(array('A', TRUE));
      $this->assertEquals("A;Y\n", $this->out->getBytes());
    }

    /**
     * Test FormatBool rocessor
     *
     */
    #[@test]
    public function formatFalse() {
      $writer= $this->newWriter()->withProcessors(array(
        NULL,
        new FormatBool()
      ));
      $writer->write(array('A', FALSE));
      $this->assertEquals("A;false\n", $this->out->getBytes());
    }

    /**
     * Test FormatBool rocessor
     *
     */
    #[@test]
    public function formatFalseAsN() {
      $writer= $this->newWriter()->withProcessors(array(
        NULL,
        new FormatBool('Y', 'N')
      ));
      $writer->write(array('A', FALSE));
      $this->assertEquals("A;N\n", $this->out->getBytes());
    }

    /**
     * Test AsEnum processor
     *
     */
    #[@test]
    public function pennyCoin() {
      $in= $this->newReader('200;penny')->withProcessors(array(
        NULL,
        new AsEnum(XPClass::forName('net.xp_framework.unittest.core.Coin'))
      ));
      $this->assertEquals(array('200', Coin::$penny), $in->read());
    }

    /**
     * Test AsEnum processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function invalidCoin() {
      $this->newReader('200;dollar')->withProcessors(array(
        NULL,
        new AsEnum(XPClass::forName('net.xp_framework.unittest.core.Coin'))
      ))->read();
    }

    /**
     * Test AsEnum processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyCoin() {
      $this->newReader('200;')->withProcessors(array(
        NULL,
        new AsEnum(XPClass::forName('net.xp_framework.unittest.core.Coin'))
      ))->read();
    }

    /**
     * Test FormatEnum processor
     *
     */
    #[@test]
    public function formatEnumValue() {
      $writer= $this->newWriter()->withProcessors(array(
        NULL,
        new FormatEnum()
      ));
      $writer->write(array('200', Coin::$penny));
      $this->assertEquals("200;penny\n", $this->out->getBytes());
    }

    /**
     * Test AsEnum processor
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function formatNonEnum() {
      $this->newWriter()->withProcessors(array(
        NULL,
        new FormatEnum()
      ))->write(array('200', new Object()));
    }

    /**
     * Test Optional processor
     *
     */
    #[@test]
    public function optionalString() {
      $in= $this->newReader('200;OK')->withProcessors(array(
        NULL,
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
        NULL,
        new Optional()
      ));
      $this->assertEquals(array('666', NULL), $in->read());
    }

    /**
     * Test Optional processor
     *
     */
    #[@test]
    public function optionalEmptyWithDefault() {
      $in= $this->newReader('666;')->withProcessors(array(
        NULL,
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
        NULL
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
        NULL
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
        NULL
      ))->write(array(NULL, 'Test'));
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
        NULL
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
        NULL
      ))->write(array(NULL, 'Test'));
      $this->assertEquals("(unknown);Test\n", $this->out->getBytes());
    }

    /**
     * Test Required processor
     *
     */
    #[@test]
    public function requiredString() {
      $in= $this->newReader('200;OK')->withProcessors(array(
        NULL,
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
        NULL,
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
        NULL
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
        NULL
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
      $this->assertEquals(array(200, NULL), $in->read());
    }

    /**
     * Test Unique processor
     *
     */
    #[@test]
    public function readUnique() {
      $in= $this->newReader("200;OK\n200;NACK")->withProcessors(array(
        new Unique(),
        NULL
      ));
      $this->assertEquals(array('200', 'OK'), $in->read());
      try {
        $in->read();
        $this->fail('Duplicate value not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }
    }

    /**
     * Test Unique processor
     *
     */
    #[@test]
    public function writeUnique() {
      $writer= $this->newWriter()->withProcessors(array(
        new Unique(),
        NULL,
      ));

      $writer->write(array('200', 'OK'));
      try {
        $writer->write(array('200', 'NACK'));
        $this->fail('Duplicate value not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }

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
        NULL
      ));
      try {
        $in->read();
        $this->fail('Unwanted value not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }
      $this->assertEquals(array('404', 'Not found'), $in->read());
    }

    /**
     * Test exceptions caused by processors do not break reading
     *
     */
    #[@test]
    public function processorExceptionsDoNotBreakReadingMultiline() {
      $in= $this->newReader("200;'OK\nThank god'\n404;'Not found\nFamous'", create(new CsvFormat())->withQuote("'"))->withProcessors(array(
        $this->newUnwantedValueProcessor('200'),
        NULL
      ));
      try {
        $in->read();
        $this->fail('Unwanted value not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }
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
        NULL
      ));

      try {
        $writer->write(array('200', 'OK'));
        $this->fail('Unwanted value not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }

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
        NULL,
        $this->newUnwantedValueProcessor('Internal Server Error')
      ));

      try {
        $writer->write(array('500', 'Internal Server Error'));
        $this->fail('Unwanted value not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }

      $writer->write(array('404', 'Not found'));
      $this->assertEquals("404;Not found\n", $this->out->getBytes());
    }
  }
?>
