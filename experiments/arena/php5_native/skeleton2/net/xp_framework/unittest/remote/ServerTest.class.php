<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'remote.server.ContainerManager',
    'remote.server.deploy.Deployer',
    'net.xp_framework.beans.stateless.RoundtripPeer',
    'lang.reflect.Proxy',
    'util.Date'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ServerTest extends TestCase {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setUp() {
      $cm= &new ContainerManager();
      $deployer= &new Deployer();
      $deployer->deployBean(
        XPClass::forName('net.xp_framework.beans.stateless.RoundtripBean'),
        $cm
      );
      
      $this->directory= &NamingDirectory::getInstance();
    }  
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    public function lookup() {
      $this->directory->lookup('xp/demo/Roundtrip');
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    public function create() {
      $proxy= &$this->directory->lookup('xp/demo/Roundtrip');
      $proxy->create();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    public function invoke() {
      $proxy= &$this->directory->lookup('xp/demo/Roundtrip');
      $bean= &$proxy->create();
      $this->assertEquals('Hello World', $bean->echoString('Hello World'));
      $this->assertEquals(2, $bean->echoInt(2));
      $this->assertEquals(2.5, $bean->echoDouble(2.5));
      $this->assertTrue($bean->echoBool(TRUE));
      $this->assertNull($bean->echoNull());
      $this->assertClass($bean->echoDate(Date::now()), 'util.Date');
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function exceptionalInvokation() {
      $proxy= &$this->directory->lookup('xp/demo/Roundtrip');
      $bean= &$proxy->create();
      
      $bean->echoArray('Tricked you, it is a string, dude.');
    }    
    
  }
?>
