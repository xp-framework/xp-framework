<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvBeanWriter',
    'io.streams.MemoryOutputStream',
    'net.xp_framework.unittest.text.csv.Person'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvBeanWriter
   */
  class CsvBeanWriterTest extends TestCase {
    protected $out= NULL;

    /**
     * Creates a new bean writer
     *
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvBeanWriter
     */
    protected function newWriter(CsvFormat $format= NULL) {
      $this->out= new MemoryOutputStream();
      return new CsvBeanWriter(new TextWriter($this->out), $format);
    }
  
    /**
     * Test writing a person object
     *
     */
    #[@test]
    public function writePerson() {
      $this->newWriter()->write(new net·xp_framework·unittest·text·csv·Person(1549, 'Timm', 'friebe@example.com'));
      $this->assertEquals("1549;Timm;friebe@example.com\n", $this->out->getBytes());
    }

    /**
     * Test writing a person object
     *
     */
    #[@test]
    public function writePersonReSorted() {
      $this->newWriter()->write(new net·xp_framework·unittest·text·csv·Person(1549, 'Timm', 'friebe@example.com'), array('email', 'id', 'name'));
      $this->assertEquals("friebe@example.com;1549;Timm\n", $this->out->getBytes());
    }

    /**
     * Test writing a person object
     *
     */
    #[@test]
    public function writePersonPartially() {
      $this->newWriter()->write(new net·xp_framework·unittest·text·csv·Person(1549, 'Timm', 'friebe@example.com'), array('id', 'name'));
      $this->assertEquals("1549;Timm\n", $this->out->getBytes());
    }
  }
?>
