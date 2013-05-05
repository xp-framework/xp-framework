<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'util.Observable');

  /**
   * Test Observable class
   *
   * @see  xp://util.Observable
   */
  class ObservableTest extends TestCase {
    protected static $observable;

    /**
     * Creates observable
     */
    #[@beforeClass]
    public static function defineObservable() {
      self::$observable= ClassLoader::defineClass('net.xp_framework.unittest.util.ObservableFixture', 'util.Observable', array(), '{
        private $value= 0;

        public function setValue($value) {
          $this->value= $value;
          $this->setChanged();
          $this->notifyObservers();
        }

        public function getValue() {
          return $this->value;
        }
      }');
    }

    /**
     * Tests hasChanged() method
     */
    #[@test]
    public function originally_unchanged() {
      $o= self::$observable->newInstance();
      $this->assertFalse($o->hasChanged());
    }

    /**
     * Tests setChanged() method
     */
    #[@test]
    public function changed() {
      $o= self::$observable->newInstance();
      $o->setChanged();
      $this->assertTrue($o->hasChanged());
    }

    /**
     * Tests clearChanged() method
     */
    #[@test]
    public function change_cleared() {
      $o= self::$observable->newInstance();
      $o->setChanged();
      $o->clearChanged();
      $this->assertFalse($o->hasChanged());
    }

    /**
     * Tests addObserver() method
     */
    #[@test]
    public function add_observer_returns_added_observer() {
      $observer= newinstance('util.Observer', array(), '{
        public function update($obs, $arg= NULL) { /* Intentionally empty */ }
      }');
      $o= self::$observable->newInstance();
      $this->assertEquals($observer, $o->addObserver($observer));
    }

    /**
     * Tests notifyObservers() method
     */
    #[@test]
    public function observer_gets_called_with_observable() {
      $observer= newinstance('util.Observer', array(), '{
        public $calls= array();
        public function update($obs, $arg= NULL) {
          $this->calls[]= array($obs, $arg);
        }
      }');
      $o= self::$observable->newInstance();
      $o->addObserver($observer);
      $o->setValue(5);
      $this->assertEquals(array(array($o, NULL)), $observer->calls);
    }
  }
?>
