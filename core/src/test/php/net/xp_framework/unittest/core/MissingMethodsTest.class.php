<?php namespace net\xp_framework\unittest\core;

/**
 * Verifies lang.Object's `__call()` implementation
 *
 * @see   https://github.com/xp-framework/xp-framework/issues/133
 */
class MissingMethodsTest extends \unittest\TestCase {

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method lang.Object::run()/')]
  public function missingMethodInvocation() {
    $o= new \lang\Object();
    $o->run();
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method lang.Object::run()/')]
  public function missingParentMethodInvocation() {
    $f= \lang\ClassLoader::defineClass('MissingMethodsTest_Fixture', 'lang.Object', array(), '{
      public function run() {
        parent::run();
      }
    }');
    call_user_func(array($f->newInstance(), 'run'));
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method MissingMethodsTest_BaseFixture::run()/')]
  public function missingParentParentMethodInvocation() {
    $b= \lang\ClassLoader::defineClass('MissingMethodsTest_BaseFixture', 'lang.Object', array(), '{}');
    $c= \lang\ClassLoader::defineClass('MissingMethodsTest_ChildFixture', $b->getName(), array(), '{
      public function run() {
        parent::run();
      }
    }');
    call_user_func(array($c->newInstance(), 'run'));
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method lang.Object::run()/')]
  public function missingParentPassMethodInvocation() {
    $b= \lang\ClassLoader::defineClass('MissingMethodsTest_PassBaseFixture', 'lang.Object', array(), '{
      public function run() {
        parent::run();
      }
    }');
    $c= \lang\ClassLoader::defineClass('MissingMethodsTest_PassChildFixture', $b->getName(), array(), '{
      public function run() {
        parent::run();
      }
    }');
    call_user_func(array($c->newInstance(), 'run'));
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined static method lang.Object::run()/')]
  public function missingStaticParentMethodInvocation() {
    $f= \lang\ClassLoader::defineClass('MissingMethodsTest_StaticFixture', 'lang.Object', array(), '{
      public static function run() {
        parent::run();
      }
    }');
    call_user_func(array($f->literal(), 'run'));
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined static method MissingMethodsTest_StaticBaseFixture::run()/')]
  public function missingStaticParentParentMethodInvocation() {
    $b= \lang\ClassLoader::defineClass('MissingMethodsTest_StaticBaseFixture', 'lang.Object', array(), '{}');
    $c= \lang\ClassLoader::defineClass('MissingMethodsTest_StaticChildFixture', $b->getName(), array(), '{
      public static function run() {
        parent::run();
      }
    }');
    call_user_func(array($c->literal(), 'run'));
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined static method lang.Object::run()/')]
  public function missingStaticParentPassMethodInvocation() {
    $b= \lang\ClassLoader::defineClass('MissingMethodsTest_StaticPassBaseFixture', 'lang.Object', array(), '{
      public static function run() {
        parent::run();
      }
    }');
    $c= \lang\ClassLoader::defineClass('MissingMethodsTest_StaticPassChildFixture', $b->getName(), array(), '{
      public static function run() {
        parent::run();
      }
    }');
    call_user_func(array($c->literal(), 'run'));
  }
}
