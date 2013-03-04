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
     * Setup test. Verifies PHP version constraint
     *
     */
    public function setUp() {
      static $ops= array(
        'l' => array('[' =>  'ge', ']' => 'gt'),
        'u' => array('[' =>  'lt', ']' => 'le'),
      );

      $method= $this->getClass()->getMethod($this->name);
      if ($method->hasAnnotation('runtime')) {
        $constraint= $method->getAnnotation('runtime');
        $lim= explode(',', $constraint, 2);
        $cmp= substr(PHP_VERSION, 0, 5);
        $result= (
          ($lim[0] ? version_compare($cmp, substr($lim[0], 1), $ops['l'][$lim[0]{0}]) : TRUE) &&
          ($lim[1] ? version_compare($cmp, substr($lim[1], 0, -1), $ops['u'][$lim[1]{strlen($lim[1]) - 1}]) : TRUE)
        );

        if (!$result) {
          throw new PrerequisitesNotMetError('PHP version '.$compare.' not compatible', NULL, $limits);
        }
      }
    }

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
    #[@test, @runtime('[5.3.0,'), @expect(class= 'lang.Error', withMessage= '/Call to undefined method Object::run/')]
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
    #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method MissingMethodsTest_BaseFixture::run/')]
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
