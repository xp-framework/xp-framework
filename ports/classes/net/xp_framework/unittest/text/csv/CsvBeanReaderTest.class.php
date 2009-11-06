<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvBeanReader',
    'io.streams.MemoryInputStream',
    'net.xp_framework.unittest.text.csv.Person'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvBreanReader
   */
  class CsvBeanReaderTest extends TestCase {

    /**
     * Set up test
     *
     */
    public function setUp() {
      if ('s' !== iconv_substr('s', 0, 1, 'iso-8859-1')) {
        throw new PrerequisitesNotMetError('Broken iconv library detected.');
      }
    }

    /**
     * Creates a new object reader
     *
     * @param   string str
     * @param   lang.XPClass class
     * @return  text.csv.CsvBeanReader
     */
    protected function newReader($str, XPClass $class) {
      return new CsvBeanReader(new TextReader(new MemoryInputStream($str)), $class);
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
      $in= $this->newReader('', XPClass::forName('net.xp_framework.unittest.text.csv.Person'));
      $this->assertNull($in->read(array('id', 'name', 'email')));
    }
  }
?>
