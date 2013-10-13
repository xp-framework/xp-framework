<?php namespace net\xp_framework\unittest\rdbms\tds;

use unittest\TestCase;
use rdbms\tds\FreeTdsLookup;


/**
 * TestCase
 *
 * @see   xp://rdbms.tds.FreeTdsLookup
 */
class FreeTdsLookupTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new FreeTdsLookup($this->getClass()->getPackage()->getResourceAsStream('freetds.conf'));
  }
  
  /**
   * Test
   *
   */
  #[@test]
  public function lookup() {
    $dsn= new \rdbms\DSN('sybase://CARLA');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new \rdbms\DSN('sybase://carla.example.com:5000'), $dsn);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function lookupCaseInsensitive() {
    $dsn= new \rdbms\DSN('sybase://carla');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new \rdbms\DSN('sybase://carla.example.com:5000'), $dsn);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function lookupNonExistantHost() {
    $dsn= new \rdbms\DSN('sybase://nonexistant');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new \rdbms\DSN('sybase://nonexistant'), $dsn);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function lookupExistingHostWithoutQueryKey() {
    $dsn= new \rdbms\DSN('sybase://banane');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new \rdbms\DSN('sybase://banane'), $dsn);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function lookupIpv4() {
    $dsn= new \rdbms\DSN('sybase://wurst4');
    $this->fixture->lookup($dsn);
    $this->assertEquals(new \rdbms\DSN('sybase://192.0.43.10:1998'), $dsn);
  }
}
