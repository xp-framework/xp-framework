<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvObjectReader',
    'io.streams.MemoryInputStream',
    'net.xp_framework.unittest.text.csv.Address',
    'net.xp_framework.unittest.text.csv.Person'
  );

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
    protected function newReader($str, XPClass $class) {
      return new CsvObjectReader(new TextReader(new MemoryInputStream($str)), $class);
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function readAddress() {
      $in= $this->newReader('Timm;Karlsruhe;76137', XPClass::forName('net.xp_framework.unittest.text.csv.Address'));
      $this->assertEquals(
        new net·xp_framework·unittest·text·csv·Address('Timm', 'Karlsruhe', '76137'), 
        $in->read(array('name', 'city', 'zip'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readPerson() {
      $in= $this->newReader('1549;Timm;friebe@example.com', XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
      $this->assertEquals(
        new net·xp_framework·unittest·text·csv·Person('1549', 'Timm', 'friebe@example.com'), 
        $in->read(array('id', 'name', 'email'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readPersonReSorted() {
      $in= $this->newReader('friebe@example.com;1549;Timm', XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
      $this->assertEquals(
        new net·xp_framework·unittest·text·csv·Person('1549', 'Timm', 'friebe@example.com'), 
        $in->read(array('email', 'id', 'name'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readPersonCompletely() {
      $in= $this->newReader('1549;Timm;friebe@example.com', XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
      $this->assertEquals(
        new net·xp_framework·unittest·text·csv·Person('1549', 'Timm', 'friebe@example.com'), 
        $in->read()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readPersonPartially() {
      $in= $this->newReader('1549;Timm;friebe@example.com', XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
      $this->assertEquals(
        new net·xp_framework·unittest·text·csv·Person('1549', 'Timm', ''), 
        $in->read(array('id', 'name'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readEmpty() {
      $in= $this->newReader('', XPClass::forName('net.xp_framework.unittest.text.csv.Address'));
      $this->assertNull($in->read(array('name', 'city', 'zip')));
    }
  }
?>
