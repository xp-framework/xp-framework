<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.tds.FreeTdsLookup'
  );

  /**
   * TestCase
   *
   * @see   xp://rdbms.tds.FreeTdsLookup#locateConf
   */
  class FreeTdsConfigLocationTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function noAlternativesFound() {
      $fixture= newinstance('rdbms.tds.FreeTdsLookup', array(), '{
        protected function parse() {
          throw new IllegalStateException("Should never be called!");
        }
        
        protected function locateConf() {
          return NULL;
        }
      }');
      $dsn= new DSN('sybase://test');
      $fixture->lookup($dsn);
      $this->assertEquals($dsn, $dsn);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function fileReturned() {
      $fixture= newinstance('rdbms.tds.FreeTdsLookup', array(), '{
        protected function parse() {
          return array("test" => array(
            "host" => $this->conf->getFilename(),
            "port" => 1999
          ));
        }
        
        protected function locateConf() {
          return new File("it.worked");
        }
      }');
      $dsn= new DSN('sybase://test');
      $fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://it.worked:1999'), $dsn);
    }
  }
?>
