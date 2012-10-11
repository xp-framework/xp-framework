<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.tds.InterfacesLookup'
  );

  /**
   * TestCase
   *
   * @see   xp://rdbms.tds.InterfacesLookup
   */
  class InterfacesLookupTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new InterfacesLookup($this->getClass()->getPackage()->getResourceAsStream('interfaces'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function lookup() {
      $dsn= new DSN('sybase://CARLA');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://carla.example.com:5000'), $dsn);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function lookupCaseInsensitive() {
      $dsn= new DSN('sybase://carla');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://carla.example.com:5000'), $dsn);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function lookupNonExistantHost() {
      $dsn= new DSN('sybase://nonexistant');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://nonexistant'), $dsn);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function lookupExistingHostWithoutQueryKey() {
      $dsn= new DSN('sybase://banane');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://banane'), $dsn);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function lookupIpv4() {
      $dsn= new DSN('sybase://wurst4');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://192.0.43.10:1998'), $dsn);
    }


    /**
     * Test
     *
     */
    #[@test]
    public function lookupKeyIndentedWithTabs() {
      $dsn= new DSN('sybase://tabs');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://tabs.example.com:1999'), $dsn);
    }
  }
?>
