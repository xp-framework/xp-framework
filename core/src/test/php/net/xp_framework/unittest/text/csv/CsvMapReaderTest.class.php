<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvMapReader',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvMapReader
   */
  class CsvMapReaderTest extends TestCase {

    /**
     * Creates a new object reader
     *
     * @param   string str
     * @param   string[] keys
     * @return  text.csv.CsvMapReader
     */
    protected function newReader($str, array $keys= array()) {
      return new CsvMapReader(new TextReader(new MemoryInputStream($str)), $keys);
    }

    /**
     * Test setKeys() and getKeys()
     *
     */
    #[@test]
    public function setKeys() {
      with ($keys= array('id', 'name', 'email')); {
        $in= $this->newReader('');
        $in->setKeys($keys);
        $this->assertEquals($keys, $in->getKeys());
      }
    }

    /**
     * Test withKeys() and getKeys()
     *
     */
    #[@test]
    public function withKeys() {
      with ($keys= array('id', 'name', 'email')); {
        $in= $this->newReader('');
        $this->assertEquals($in, $in->withKeys($keys));
        $this->assertEquals($keys, $in->getKeys());
      }
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function readRecord() {
      $in= $this->newReader('1549;Timm;friebe@example.com', array('id', 'name', 'email'));
      $this->assertEquals(
        array('id' => '1549', 'name' => 'Timm', 'email' => 'friebe@example.com'), 
        $in->read()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readRecordWithHeaders() {
      $in= $this->newReader("id;name;email\n1549;Timm;friebe@example.com");
      $in->setKeys($in->getHeaders());
      $this->assertEquals(
        array('id' => '1549', 'name' => 'Timm', 'email' => 'friebe@example.com'), 
        $in->read()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readEmpty() {
      $in= $this->newReader('', array('id', 'name', 'email'));
      $this->assertNull($in->read());
    }

    /**
     * Test no new field is returned when an extra element appears in input
     *
     */
    #[@test]
    public function readRecordWithExcess() {
      $in= $this->newReader('1549;Timm;friebe@example.com;WILL_NOT_APPEAR', array('id', 'name', 'email'));
      $this->assertEquals(
        array('id' => '1549', 'name' => 'Timm', 'email' => 'friebe@example.com'), 
        $in->read()
      );
    }

    /**
     * Test NULL is returned in the corresponding field when an element is missing in input
     *
     */
    #[@test]
    public function readRecordWithUnderrun() {
      $in= $this->newReader('1549;Timm', array('id', 'name', 'email'));
      $this->assertEquals(
        array('id' => '1549', 'name' => 'Timm', 'email' => NULL), 
        $in->read()
      );
    }
  }
?>
