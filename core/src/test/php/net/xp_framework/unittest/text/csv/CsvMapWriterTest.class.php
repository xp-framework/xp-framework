<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvMapWriter',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvMapWriter
   */
  class CsvMapWriterTest extends TestCase {
    protected $out= NULL;

    /**
     * Creates a new Map writer
     *
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvMapWriter
     */
    protected function newWriter(CsvFormat $format= NULL) {
      $this->out= new MemoryOutputStream();
      return new CsvMapWriter(new TextWriter($this->out), $format);
    }
  
    /**
     * Test writing
     *
     */
    #[@test]
    public function writeRecord() {
      $this->newWriter()->write(array('id' => 1549, 'name' => 'Timm', 'email' => 'friebe@example.com'));
      $this->assertEquals("1549;Timm;friebe@example.com\n", $this->out->getBytes());
    }

    /**
     * Test writing
     *
     */
    #[@test]
    public function writeRecordWithHeaders() {
      $out= $this->newWriter();
      $out->setHeaders(array('id', 'name', 'email'));
      $out->write(array('id' => 1549, 'name' => 'Timm', 'email' => 'friebe@example.com'));
      $this->assertEquals("id;name;email\n1549;Timm;friebe@example.com\n", $this->out->getBytes());
    }

    /**
     * Test writing
     *
     */
    #[@test]
    public function writeUnorderedRecordWithHeaders() {
      $out= $this->newWriter();
      $out->setHeaders(array('id', 'name', 'email'));
      $out->write(array('email' => 'friebe@example.com', 'name' => 'Timm', 'id' => 1549));
      $this->assertEquals("id;name;email\n1549;Timm;friebe@example.com\n", $this->out->getBytes());
    }


    /**
     * Test writing
     *
     */
    #[@test]
    public function writeIncompleteRecordWithHeaders() {
      $out= $this->newWriter();
      $out->setHeaders(array('id', 'name', 'email'));
      $out->write(array('id' => 1549, 'email' => 'friebe@example.com'));
      $this->assertEquals("id;name;email\n1549;;friebe@example.com\n", $this->out->getBytes());
    }

    /**
     * Test writing
     *
     */
    #[@test]
    public function writeEmptyRecordWithHeaders() {
      $out= $this->newWriter();
      $out->setHeaders(array('id', 'name', 'email'));
      $out->write(array());
      $this->assertEquals("id;name;email\n;;\n", $this->out->getBytes());
    }

    /**
     * Test writing
     *
     */
    #[@test]
    public function writeRecordWithExtraDataWithHeaders() {
      $out= $this->newWriter();
      $out->setHeaders(array('id', 'name', 'email'));
      $out->write(array('id' => 1549, 'name' => 'Timm', 'email' => 'friebe@example.com', 'extra' => 'WILL_NOT_APPEAR'));
      $this->assertEquals("id;name;email\n1549;Timm;friebe@example.com\n", $this->out->getBytes());
    }
  }
?>
