<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.RestContext'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
   */
  class RestContextTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new RestContext();
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function marshal_this_generically() {
      $this->assertEquals(
        $this,
        $this->fixture->marshal($this)
      );
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function marshal_this_with_typemarshaller() {
      $this->fixture->addMarshaller('unittest.TestCase', newinstance('webservices.rest.TypeMarshaller', array(), '{
        public function marshal($t) {
          return $t->getClassName()."::".$t->getName();
        }
        public function unmarshal($name) {
          // Not needed
        }
      }'));
      $this->assertEquals(
        $this->getClassName().'::'.$this->getName(),
        $this->fixture->marshal($this)
      );
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function unmarshal_this_with_typemarshaller() {
      $this->fixture->addMarshaller('unittest.TestCase', newinstance('webservices.rest.TypeMarshaller', array(), '{
        public function marshal($t) {
          // Not needed
        }
        public function unmarshal($name) {
          sscanf($name, "%[^:]::%s", $class, $test);
          return XPClass::forName($class)->newInstance($test);
        }
      }'));
      $this->assertEquals(
        $this,
        $this->fixture->unmarshal($this->getClass(), $this->getClassName().'::'.$this->getName())
      );
    }
  }
?>
