<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Verifies lang.Object's <tt>__call()</tt> implementation
   *
   */
  class MissingMethodsTest extends TestCase {

    /**
     * Tests missing methods
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method lang.Object::run()/')]
    public function missingMethodInvocation() {
      $o= new Object();
      $o->run();
    }

    /**
     * Tests missing methods
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/133
     */
    #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method lang.Object::run()/')]
    public function missingParentMethodInvocation() {
      $o= newinstance('lang.Object', array(), '{
        public function run() {
          parent::run();
        }
      }');
      $o->run();
    }

    /**
     * Tests missing static methods
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined static method lang.Object::run()/')]
    public function missingStaticParentMethodInvocation() {
      $f= ClassLoader::defineClass('MissingMethodsTest_Fixture', 'lang.Object', array(), '{
        public static function run() {
          parent::run();
        }
      }');
      call_user_func(array($f->literal(), 'run'));
    }

    /**
     * Tests missing methods
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined static method MissingMethodsTest_BaseFixture::run()/')]
    public function missingStaticParentParentMethodInvocation() {
      $b= ClassLoader::defineClass('MissingMethodsTest_BaseFixture', 'lang.Object', array(), '{}');
      $c= ClassLoader::defineClass('MissingMethodsTest_ChildFixture', $b->getName(), array(), '{
        public static function run() {
          parent::run();
        }
      }');
      call_user_func(array($c->literal(), 'run'));
    }
  }
?>
