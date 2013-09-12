<?php namespace net\xp_framework\unittest\text\csv;

use unittest\TestCase;
use text\csv\CsvObjectWriter;
use io\streams\MemoryOutputStream;


/**
 * TestCase
 *
 * @see      xp://text.csv.CsvObjectWriter
 */
class CsvObjectWriterTest extends TestCase {
  protected $out= null;

  /**
   * Creates a new object writer
   *
   * @param   text.csv.CsvFormat format
   * @return  text.csv.CsvObjectWriter
   */
  protected function newWriter(\text\csv\CsvFormat $format= null) {
    $this->out= new MemoryOutputStream();
    return new CsvObjectWriter(new \io\streams\TextWriter($this->out), $format);
  }

  /**
   * Test writing a person object
   *
   */
  #[@test]
  public function writePerson() {
    $this->newWriter()->write(new Person(1549, 'Timm', 'friebe@example.com'));
    $this->assertEquals("1549;Timm;friebe@example.com\n", $this->out->getBytes());
  }

  /**
   * Test writing a person object
   *
   */
  #[@test]
  public function writePersonReSorted() {
    $this->newWriter()->write(new Person(1549, 'Timm', 'friebe@example.com'), array('email', 'id', 'name'));
    $this->assertEquals("friebe@example.com;1549;Timm\n", $this->out->getBytes());
  }

  /**
   * Test writing a person object
   *
   */
  #[@test]
  public function writePersonPartially() {
    $this->newWriter()->write(new Person(1549, 'Timm', 'friebe@example.com'), array('id', 'name'));
    $this->assertEquals("1549;Timm\n", $this->out->getBytes());
  }

  /**
   * Test writing an address object
   *
   */
  #[@test]
  public function writeAddress() {
    $this->newWriter()->write(new Address('Timm', 'Karlsruhe', '76137'));
    $this->assertEquals("Timm;Karlsruhe;76137\n", $this->out->getBytes());
  }

  /**
   * Test writing an address object
   *
   */
  #[@test]
  public function writeAddressPartially() {
    $this->newWriter()->write(new Address('Timm', 'Karlsruhe', '76137'), array('city', 'zip'));
    $this->assertEquals("Karlsruhe;76137\n", $this->out->getBytes());
  }
}
