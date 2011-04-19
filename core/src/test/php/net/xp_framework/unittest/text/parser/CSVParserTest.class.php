<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.parser.CSVParser'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.parser.CSVParser
   * @purpose  TestCase
   */
  class CSVParserTest extends TestCase {
  
    /**
     * Setup
     *
     */
    public function setup() {
      $this->stream= new Stream();
      $this->parser= new CSVParser();
      $this->parser->setInputStream($this->stream);
    }

    /**
     * Test guess of delimiters
     *
     */
    #[@test]
    function testGuessDelimiter() {
      $this->stream->write('a,b,c,d,e,f');
      $this->stream->rewind();
      
      $this->assertEquals(',', $this->parser->guessDelimiter());
    }

    /**
     * Test record with escape character
     *
     */
    #[@test]
    public function testRecordWithEscapes() {
      $this->stream->write('"UTDI.DE",13.56,-0.63,"9:16am","2/28/2007",2274004');
      $this->stream->rewind();
      $this->parser->setColDelimiter(',');
      
      with ($record= $this->parser->getNextRecord()); {
        $this->assertEquals('UTDI.DE', array_shift($record));
        $this->assertEquals('13.56', array_shift($record));
        $this->assertEquals('-0.63', array_shift($record));
        $this->assertEquals('9:16am', array_shift($record));
        $this->assertEquals('2/28/2007', array_shift($record));
        $this->assertEquals('2274004', array_shift($record));
      }
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function lastColumn() {
      $this->stream->write('foo|bar|baz|'.PHP_EOL);
      $this->stream->write('bla|bla|bla|'.PHP_EOL);
      $this->stream->write('bla|bla|bla|'.PHP_EOL);
      $this->stream->rewind();
      $this->parser->setColDelimiter('|');
      $this->parser->getHeaderRecord();
      
      $this->assertEquals(array(
        'foo' => 'bla',
        'bar' => 'bla',
        'baz' => 'bla'
        ), $this->parser->getNextRecord()
      );
      $this->assertEquals(array(
        'foo' => 'bla',
        'bar' => 'bla',
        'baz' => 'bla'
        ), $this->parser->getNextRecord()
      );
    }    
  }
?>
