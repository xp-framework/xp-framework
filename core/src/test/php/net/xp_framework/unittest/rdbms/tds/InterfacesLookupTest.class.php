<?php namespace net\xp_framework\unittest\rdbms\tds;

use rdbms\DSN;
use rdbms\tds\InterfacesLookup;

/**
 * TestCase
 *
 * @see   xp://rdbms.tds.InterfacesLookup
 */
class InterfacesLookupTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new InterfacesLookup($this->getClass()->getPackage()->getResourceAsStream('interfaces'));
  }
  
  #[@test]
  public function lookup() {
    $dsn= new DSN('sybase://CARLA');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new DSN('sybase://carla.example.com:5000'), $dsn);
  }

  #[@test]
  public function lookupCaseInsensitive() {
    $dsn= new DSN('sybase://carla');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new DSN('sybase://carla.example.com:5000'), $dsn);
  }

  #[@test]
  public function lookupNonExistantHost() {
    $dsn= new DSN('sybase://nonexistant');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new DSN('sybase://nonexistant'), $dsn);
  }

  #[@test]
  public function lookupExistingHostWithoutQueryKey() {
    $dsn= new DSN('sybase://banane');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new DSN('sybase://banane'), $dsn);
  }

  #[@test]
  public function lookupIpv4() {
    $dsn= new DSN('sybase://wurst4');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new DSN('sybase://192.0.43.10:1998'), $dsn);
  }


  #[@test]
  public function lookupKeyIndentedWithTabs() {
    $dsn= new DSN('sybase://tabs');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new DSN('sybase://tabs.example.com:1999'), $dsn);
  }
}
