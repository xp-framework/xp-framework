<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvObjectWriter',
    'io.streams.MemoryOutputStream',
    'net.xp_framework.unittest.text.csv.Address',
    'net.xp_framework.unittest.text.csv.Person'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvObjectWriter
   */
  class CsvObjectWriterTest extends TestCase {
    protected $out= NULL;

    /**
     * Creates a new object writer
     *
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvObjectWriter
     */
    protected function newWriter(CsvFormat $format= NULL) {
      $this->out= new MemoryOutputStream();
      return new CsvObjectWriter(new TextWriter($this->out), $format);
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

    /**
     * Test writing an address object
     *
     */
    #[@test]
    public function writeAddress() {
      $this->newWriter()->write(new net·xp_framework·unittest·text·csv·Address('Timm', 'Karlsruhe', '76137'));
      $this->assertEquals("Timm;Karlsruhe;76137\n", $this->out->getBytes());
    }

    /**
     * Test writing an address object
     *
     */
    #[@test]
    public function writeAddressPartially() {
      $this->newWriter()->write(new net·xp_framework·unittest·text·csv·Address('Timm', 'Karlsruhe', '76137'), array('city', 'zip'));
      $this->assertEquals("Karlsruhe;76137\n", $this->out->getBytes());
    }
  }
?>
