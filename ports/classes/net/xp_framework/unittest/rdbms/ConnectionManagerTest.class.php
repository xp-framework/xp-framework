<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.ConnectionManager',
    'net.xp_framework.unittest.rdbms.mock.MockConnection'
  );

  /**
   * ConnectionManager testcase
   *
   * @see      rdbms.ConnectionManager
   * @purpose  Testcase
   */
  class ConnectionManagerTest extends TestCase {
    const
      MOCK_CONNECTION_CLASS = 'net.xp_framework.unittest.rdbms.mock.MockConnection';
  
    /**
     * Mock connection registration
     *
     */  
    #[@beforeClass]
    public static function registerMockConnection() {
      DriverManager::register('mock', XPClass::forName(self::MOCK_CONNECTION_CLASS));
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function configured(Properties $prop) {
      $class= ClassLoader::getDefault()->defineClass(
        'ConnectionManagerTestFixture',
        'rdbms.ConnectionManager',
        array(),
        '{
          public static function resetInstance() {
            self::$instance= new self();
          }
        }'
      );
      
      $class->getMethod('resetInstance')->invoke(NULL);
      $cm= $class->getMethod('getInstance')->invoke(NULL);
      $cm->configure($prop);
      return $cm;
    }
    
    /**
     * Check we're actually getting a ConnectionManager instance
     *
     */
    #[@test]
    public function instance() {
      $this->assertSubclass($this->configured(Properties::fromString('')), 'rdbms.ConnectionManager');
    }
    
    /**
     * Acquire a valid connection
     *
     */
    #[@test]
    public function acquireValid() {
      $cm= $this->configured(Properties::fromString('[mydb]
        dsn="mock://user:pass@host/db?autoconnect=1
      '));

      $this->assertSubclass($cm->getByHost('mydb', 0), self::MOCK_CONNECTION_CLASS);
    }
    
    
    /**
     * Check that configuring with a not supported scheme works.
     *
     */
    #[@test]
    public function configureInvalid() {
      $this->configured(Properties::fromString('[mydb]
        dsn="invalid://user:pass@host/db?autoconnect=1
      '));
      $this->assertTrue(TRUE);
    }
    
    /**
     * Acquiring an unsupported connection should throw a
     * rdbms.DriverNotSupportedException
     *
     */
    #[@test, @expect('rdbms.DriverNotSupportedException')]
    public function acquireInvalid() {
      $this->configured(Properties::fromString('[mydb]
        dsn="invalid://user:pass@host/db?autoconnect=1
      '))->getByHost('mydb', 0);
    }
  }
?>
