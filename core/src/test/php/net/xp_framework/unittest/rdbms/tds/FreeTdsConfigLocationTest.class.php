<?php namespace net\xp_framework\unittest\rdbms\tds;

use rdbms\DSN;
use rdbms\tds\FreeTdsLookup;

/**
 * TestCase
 *
 * @see   xp://rdbms.tds.FreeTdsLookup#locateConf
 */
class FreeTdsConfigLocationTest extends \unittest\TestCase {

  #[@test]
  public function noAlternativesFound() {
    $fixture= newinstance('rdbms.tds.FreeTdsLookup', array(), '{
      protected function parse() {
        throw new IllegalStateException("Should never be called!");
      }
      
      protected function locateConf() {
        return null;
      }
    }');
    $dsn= new DSN('sybase://test');
    $fixture->lookup($dsn);
    $this->assertEquals($dsn, $dsn);
  }

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
