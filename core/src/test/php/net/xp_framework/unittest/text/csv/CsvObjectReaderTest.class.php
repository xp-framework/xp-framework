<?php namespace net\xp_framework\unittest\text\csv;

use unittest\TestCase;
use text\csv\CsvObjectReader;
use io\streams\MemoryInputStream;


/**
 * TestCase
 *
 * @see      xp://text.csv.CsvObjectReader
 */
class CsvObjectReaderTest extends TestCase {

  /**
   * Creates a new object reader
   *
   * @param   string str
   * @param   lang.XPClass class
   * @return  text.csv.CsvObjectReader
   */
  protected function newReader($str, \lang\XPClass $class) {
    return new CsvObjectReader(new \io\streams\TextReader(new MemoryInputStream($str)), $class);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readAddress() {
    $in= $this->newReader('Timm;Karlsruhe;76137', \lang\XPClass::forName('net.xp_framework.unittest.text.csv.Address'));
    $this->assertEquals(
      new Address('Timm', 'Karlsruhe', '76137'), 
      $in->read(array('name', 'city', 'zip'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readPerson() {
    $in= $this->newReader('1549;Timm;friebe@example.com', \lang\XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
    $this->assertEquals(
      new Person('1549', 'Timm', 'friebe@example.com'), 
      $in->read(array('id', 'name', 'email'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readPersonReSorted() {
    $in= $this->newReader('friebe@example.com;1549;Timm', \lang\XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
    $this->assertEquals(
      new Person('1549', 'Timm', 'friebe@example.com'), 
      $in->read(array('email', 'id', 'name'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readPersonCompletely() {
    $in= $this->newReader('1549;Timm;friebe@example.com', \lang\XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
    $this->assertEquals(
      new Person('1549', 'Timm', 'friebe@example.com'), 
      $in->read()
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readPersonPartially() {
    $in= $this->newReader('1549;Timm;friebe@example.com', \lang\XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
    $this->assertEquals(
      new Person('1549', 'Timm', ''), 
      $in->read(array('id', 'name'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readEmpty() {
    $in= $this->newReader('', \lang\XPClass::forName('net.xp_framework.unittest.text.csv.Address'));
    $this->assertNull($in->read(array('name', 'city', 'zip')));
  }
}
