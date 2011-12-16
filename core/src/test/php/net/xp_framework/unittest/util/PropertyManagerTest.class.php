<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.PropertyManager',
    'lang.ClassLoader'
  );

  /**
   * Tests for util.PropertyManager singleton
   *
   * @see   xp://util.PropertyManager
   */
  class PropertyManagerTest extends TestCase{
    private function fixture() {
      $class= ClassLoader::getDefault()->defineClass('NonSingletonPropertyManager', 'util.PropertyManager', array(), '{
        public static function newInstance() {
          return new self();
        }
      }');
      
      return $class->getMethod('newInstance')->invoke(NULL);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function isSingleton() {
      $this->assertEquals(
        PropertyManager::getInstance()->hashCode(),
        PropertyManager::getInstance()->hashCode()
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testCanAcquireNewInstance() {
      $instance= $this->fixture();
      $this->assertInstanceOf('util.PropertyManager', $instance);
      $this->assertNotEquals($instance->hashCode(), $this->fixture()->hashCode());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function registerProperties() {
      $fixture= $this->fixture();
      $this->assertFalse($fixture->hasProperties('props'));
      $fixture->register('props', Properties::fromString('[section]'));
      
      $this->assertTrue($fixture->hasProperties('props'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function hasConfiguredPathProperties() {
      $fixture= $this->fixture();
      $fixture->configure(dirname(__FILE__));
      
      $this->assertTrue($fixture->hasProperties('example'));
      return $fixture;
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function doesNotHaveConfiguredPathProperties() {
      $this->assertFalse($this->hasConfiguredPathProperties()->hasProperties('does-not-exist'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function getPropertiesReturnsSameObject() {
      $fixture= $this->hasConfiguredPathProperties();
      $this->assertEquals(
        $fixture->getProperties('example')->hashCode(),
        $fixture->getProperties('example')->hashCode()
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function registerOverwritesExistingProperties() {
      $fixture= $this->hasConfiguredPathProperties();
      $fixture->register('example', Properties::fromString('[any-section]'));
      $this->assertEquals('any-section', $fixture->getProperties('example')->getFirstSection());
    }
  }
?>
