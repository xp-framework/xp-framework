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
    #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method Object::run/')]
    public function missingStaticParentMethodInvocation() {
      $o= newinstance('lang.Object', array(), '{
        public static function run() {
          parent::run();
        }
      }');
      call_user_func(array($o, 'run'));
    }
  }
?>
