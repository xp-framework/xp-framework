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

    /**
     * Creates a fixrture
     *
     * @return  util.PropertyManager
     */
    private function fixture() {
      $class= ClassLoader::getDefault()->defineClass('NonSingletonPropertyManager', 'util.PropertyManager', array(), '{
        public static function newInstance() {
          return new self();
        }
      }');
      
      return $class->getMethod('newInstance')->invoke(NULL);
    }
    
    /**
     * Returns a PropertyManager configured with the directory this test
     * class lies in.
     *
     * @return  util.PropertyManager
     */
    private function preconfigured() {
      $f= $this->fixture();
      $f->configure(dirname(__FILE__));
      return $f;
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
    public function hasConfiguredSourceProperties() {
      $fixture= $this->fixture();
      $fixture->configure(dirname(__FILE__));
      
      $this->assertTrue($fixture->hasProperties('example'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function doesNotHaveConfiguredSourceProperties() {
      $this->assertFalse($this->preconfigured()->hasProperties('does-not-exist'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function getPropertiesReturnsSameObjectIfExactlyOneAvailable() {
      $fixture= $this->preconfigured();
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
      $fixture= $this->preconfigured();
      $fixture->register('example', Properties::fromString('[any-section]'));
      $this->assertEquals('any-section', $fixture->getProperties('example')->getFirstSection());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getProperties() {
      $prop= $this->preconfigured()->getProperties('example');
      $this->assertInstanceOf('util.PropertyAccess', $prop);
      $this->assertEquals('value', $prop->readString('section', 'key'));
    }

    /**
     * Test prependSource()
     *
     */
    #[@test]
    public function prependSource() {
      $path= new FilesystemPropertySource('.');
      $this->assertEquals($path, $this->fixture()->prependSource($path));
    }

    /**
     * Test appendSource()
     *
     */
    #[@test]
    public function appendSource() {
      $path= new FilesystemPropertySource('.');
      $this->assertEquals($path, $this->fixture()->appendSource($path));
    }

    /**
     * Test hasSource()
     *
     */
    #[@test]
    public function hasSource() {
      $path= new FilesystemPropertySource(dirname(__FILE__).'/..');
      $fixture= $this->fixture();
      $this->assertFalse($fixture->hasSource($path));
    }

    /**
     * Test hasSource()
     *
     */
    #[@test]
    public function hasAppendedSource() {
      $path= new FilesystemPropertySource(dirname(__FILE__).'/..');
      $fixture= $this->fixture();
      $fixture->appendSource($path);
      $this->assertTrue($fixture->hasSource($path));
    }

    /**
     * Test removeSource()
     *
     */
    #[@test]
    public function removeSource() {
      $path= new FilesystemPropertySource(dirname(__FILE__).'/..');
      $fixture= $this->fixture();
      $this->assertFalse($fixture->removeSource($path));
    }

    /**
     * Test removeSource()
     *
     */
    #[@test]
    public function removeAppendedSource() {
      $path= new FilesystemPropertySource(dirname(__FILE__).'/..');
      $fixture= $this->fixture();
      $fixture->appendSource($path);
      $this->assertTrue($fixture->removeSource($path));
      $this->assertFalse($fixture->hasSource($path));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getPropertiesFromSecondSource() {
      $fixture= $this->fixture();
      $fixture->configure(dirname(__FILE__).'/..');
      $fixture->appendSource(new FilesystemPropertySource(dirname(__FILE__).'/.'));

      $this->assertEquals('value', $fixture->getProperties('example')->readString('section', 'key'));
    }

    /**
     * Test getSources()
     *
     */
    #[@test]
    public function getSourcesInitiallyEmpty() {
      $this->assertEquals(array(),  $this->fixture()->getSources());
    }

    /**
     * Test getSources()
     *
     */
    #[@test]
    public function getSourcesAfterAppendingOne() {
      $path= new FilesystemPropertySource('.');
      $fixture= $this->fixture();
      $fixture->appendSource($path);
      $this->assertEquals(array($path), $fixture->getSources());
    }

    /**
     * Test getSources()
     *
     */
    #[@test]
    public function getSourcesAfterPrependingOne() {
      $path= new FilesystemPropertySource('.');
      $fixture= $this->fixture();
      $fixture->prependSource($path);
      $this->assertEquals(array($path), $fixture->getSources());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getCompositeProperties() {
      $fixture= $this->fixture();
      $fixture->configure(dirname(__FILE__));

      // Register new Properties, with some value in existing section
      $fixture->register('example', Properties::fromString('[section]
dynamic-value=whatever'));

      $prop= $fixture->getProperties('example');
      $this->assertInstanceOf('util.PropertyAccess', $prop);
      $this->assertFalse($prop instanceof Properties);

      // Check key from example.ini is available
      $this->assertEquals('value', $fixture->getProperties('example')->readString('section', 'key'));

      // Check key from registered Properties is available
      $this->assertEquals('whatever', $prop->readString('section', 'dynamic-value'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memoryPropertiesAlwaysHavePrecendenceInCompositeProperties() {
      $fixture= $this->fixture();
      $fixture->configure(dirname(__FILE__));

      $this->assertEquals('value', $fixture->getProperties('example')->readString('section', 'key'));

      $fixture->register('example', Properties::fromString('[section]
key="overwritten value"'));
      $this->assertEquals('overwritten value', $fixture->getProperties('example')->readString('section', 'key'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function appendingSourcesOnlyAddsNewSources() {
      $fixture= $this->fixture();
      $fixture->appendSource(new FilesystemPropertySource(dirname(__FILE__)));
      $fixture->appendSource(new FilesystemPropertySource(dirname(__FILE__)));

      $this->assertInstanceOf('util.Properties', $fixture->getProperties('example'));
    }

    /**
     * Test getProperties()
     *
     */
    #[@test]
    public function getExistingProperties() {
      $this->assertTrue($this->preconfigured()->getProperties('example')->exists());
    }

    /**
     * Test getProperties()
     *
     */
    #[@test]
    public function getNonExistantProperties() {
      $this->assertNull($this->preconfigured()->getProperties('does-not-exist'));
    }
  }
?>
