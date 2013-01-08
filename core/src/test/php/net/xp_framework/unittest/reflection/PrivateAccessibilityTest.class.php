<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.ClassLoader',
    'net.xp_framework.unittest.reflection.PrivateAccessibilityFixture',
    'net.xp_framework.unittest.reflection.PrivateAccessibilityFixtureChild',
    'net.xp_framework.unittest.reflection.PrivateAccessibilityFixtureCtorChild'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.reflect.Constructor
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Field
   */
  class PrivateAccessibilityTest extends TestCase {
    private static 
      $fixture          = NULL, 
      $fixtureChild     = NULL,
      $fixtureCtorChild = NULL;
    
    /**
     * Initialize fixture, fixtureChild and fixtureCtorChild members
     *
     */
    #[@beforeClass]
    public static function initializeClasses() {
      if (version_compare(PHP_VERSION, '5.3.2', 'lt')) {
        throw new PrerequisitesNotMetError('Private not supported', NULL, array('PHP 5.3.2'));
      }

      self::$fixture= XPClass::forName('net.xp_framework.unittest.reflection.PrivateAccessibilityFixture');
      self::$fixtureChild= XPClass::forName('net.xp_framework.unittest.reflection.PrivateAccessibilityFixtureChild');
      self::$fixtureCtorChild= XPClass::forName('net.xp_framework.unittest.reflection.PrivateAccessibilityFixtureCtorChild');
    }

    /**
     * Invoke private constructor from here should not work
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateConstructor() {
      self::$fixture->getConstructor()->newInstance(array());
    }
  
    /**
     * Invoke private constructor from same class
     *
     */
    #[@test]
    public function invokingPrivateConstructorFromSameClass() {
      $this->assertInstanceOf(self::$fixture, PrivateAccessibilityFixture::construct(self::$fixture));
    }

    /**
     * Invoke private constructor from parent class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateConstructorFromParentClass() {
      PrivateAccessibilityFixtureCtorChild::construct(self::$fixture);
    }

    /**
     * Invoke private constructor from child class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateConstructorFromChildClass() {
      PrivateAccessibilityFixtureCtorChild::construct(self::$fixtureChild);
    }

    /**
     * Invoke private constructor from here should work if it's accessible
     *
     */
    #[@test]
    public function invokingPrivateConstructorMadeAccessible() {
      $this->assertInstanceOf(self::$fixture, self::$fixture
        ->getConstructor()
        ->setAccessible(TRUE)
        ->newInstance(array())
      );
    }

    /**
     * Invoke private method from here should not work
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateMethod() {
      self::$fixture->getMethod('target')->invoke(PrivateAccessibilityFixture::construct(self::$fixture));
    }

    /**
     * Invoke private method from same class
     *
     */
    #[@test]
    public function invokingPrivateMethodFromSameClass() {
      $this->assertEquals('Invoked', PrivateAccessibilityFixture::invoke(self::$fixture));
    }

    /**
     * Invoke private method from parent class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateMethodFromParentClass() {
      PrivateAccessibilityFixtureChild::invoke(self::$fixture);
    }

    /**
     * Invoke private method from child class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateMethodFromChildClass() {
      PrivateAccessibilityFixtureChild::invoke(self::$fixtureChild);
    }

    /**
     * Invoke private method from here should work if it's accessible
     *
     */
    #[@test]
    public function invokingPrivateMethodMadeAccessible() {
      $this->assertEquals('Invoked', self::$fixture
        ->getMethod('target')
        ->setAccessible(TRUE)
        ->invoke(PrivateAccessibilityFixture::construct(self::$fixture))
      );
    }

    /**
     * Invoke private method from here should not work
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateStaticMethod() {
      self::$fixture->getMethod('staticTarget')->invoke(NULL);
    }

    /**
     * Invoke private method from same class
     *
     */
    #[@test]
    public function invokingPrivateStaticMethodFromSameClass() {
      $this->assertEquals('Invoked', PrivateAccessibilityFixture::invokeStatic(self::$fixture));
    }

    /**
     * Invoke private method from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateStaticMethodFromParentClass() {
      PrivateAccessibilityFixtureChild::invokeStatic(self::$fixture);
    }

    /**
     * Invoke private method from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokingPrivateStaticMethodFromChildClass() {
      PrivateAccessibilityFixtureChild::invokeStatic(self::$fixtureChild);
    }

    /**
     * Invoke private method from here should work if it's accessible
     *
     */
    #[@test]
    public function invokingPrivateStaticMethodMadeAccessible() {
      $this->assertEquals('Invoked', self::$fixture
        ->getMethod('staticTarget')
        ->setAccessible(TRUE)
        ->invoke(NULL)
      );
    }

    /**
     * Read private member from here should not work
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function readingPrivateMember() {
      self::$fixture->getField('target')->get(PrivateAccessibilityFixture::construct(self::$fixture));
    }

    /**
     * Read private member from same class
     *
     */
    #[@test]
    public function readingPrivateMemberFromSameClass() {
      $this->assertEquals('Target', PrivateAccessibilityFixture::read(self::$fixture));
    }

    /**
     * Read private member from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function readingPrivateMemberFromParentClass() {
      PrivateAccessibilityFixtureChild::read(self::$fixture);
    }

    /**
     * Read private member from same class
     *
     */
    #[@test, @ignore('$this->getClass()->getField($field) does not yield private field declared in parent class')]
    public function readingPrivateMemberFromChildClass() {
      PrivateAccessibilityFixtureChild::read(self::$fixtureChild);
    }

    /**
     * Read private member from here should work if it's accessible
     *
     */
    #[@test]
    public function readingPrivateMemberMadeAccessible() {
      $this->assertEquals('Target', self::$fixture
        ->getField('target')
        ->setAccessible(TRUE)
        ->get(PrivateAccessibilityFixture::construct(self::$fixture))
      );
    }

    /**
     * Read private member from here should not work
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function readingPrivateStaticMember() {
      self::$fixture->getField('staticTarget')->get(NULL);
    }

    /**
     * Read private static member from same class
     *
     */
    #[@test]
    public function readingPrivateStaticMemberFromSameClass() {
      $this->assertEquals('Target', PrivateAccessibilityFixture::readStatic(self::$fixture));
    }

    /**
     * Read private static member from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function readingPrivateStaticMemberFromParentClass() {
      PrivateAccessibilityFixtureChild::readStatic(self::$fixture);
    }

    /**
     * Read private static member from same class
     *
     */
    #[@test, @ignore('$this->getClass()->getField($field) does not yield private field declared in parent class')]
    public function readingPrivateStaticMemberFromChildClass() {
      PrivateAccessibilityFixtureChild::readStatic(self::$fixtureChild);
    }

    /**
     * Read private member from here should work if it's accessible
     *
     */
    #[@test]
    public function readingPrivateStaticMemberMadeAccessible() {
      $this->assertEquals('Target', self::$fixture
        ->getField('staticTarget')
        ->setAccessible(TRUE)
        ->get(NULL)
      );
    }

    /**
     * Write private member from here should not work
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writingPrivateMember() {
      self::$fixture->getField('target')->set(PrivateAccessibilityFixture::construct(self::$fixture), NULL);
    }

    /**
     * Write private member from same class
     *
     */
    #[@test]
    public function writingPrivateMemberFromSameClass() {
      $this->assertEquals('Modified', PrivateAccessibilityFixture::write(self::$fixture));
    }

    /**
     * Write private member from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writingPrivateMemberFromParentClass() {
      PrivateAccessibilityFixtureChild::write(self::$fixture);
    }

    /**
     * Write private member from same class
     *
     */
    #[@test, @ignore('$this->getClass()->getField($field) does not yield private field declared in parent class')]
    public function writingPrivateMemberFromChildClass() {
      PrivateAccessibilityFixtureChild::write(self::$fixtureChild);
    }

    /**
     * Write private member from here should work if it's accessible
     *
     */
    #[@test]
    public function writingPrivateMemberMadeAccessible() {
      with ($f= self::$fixture->getField('target'), $i= PrivateAccessibilityFixture::construct(self::$fixture)); {
        $f->setAccessible(TRUE);
        $f->set($i, 'Modified');
        $this->assertEquals('Modified', $f->get($i));
      }
    }

    /**
     * Write private static member from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writingPrivateStaticMember() {
      self::$fixture->getField('staticTarget')->set(NULL, 'Modified');
    }

    /**
     * Write private static member from same class
     *
     */
    #[@test]
    public function writingPrivateStaticMemberFromSameClass() {
      $this->assertEquals('Modified', PrivateAccessibilityFixture::writeStatic(self::$fixture));
    }

    /**
     * Write private static member from same class
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writingPrivateStaticMemberFromParentClass() {
      PrivateAccessibilityFixtureChild::writeStatic(self::$fixture);
    }

    /**
     * Write private static member from same class
     *
     */
    #[@test, @ignore('$this->getClass()->getField($field) does not yield private field declared in parent class')]
    public function writingPrivateStaticMemberFromChildClass() {
      PrivateAccessibilityFixtureChild::writeStatic(self::$fixtureChild);
    }

    /**
     * Write private member from here should work if it's accessible
     *
     */
    #[@test]
    public function writingPrivateStaticMemberMadeAccessible() {
      with ($f= self::$fixture->getField('staticTarget')); {
        $f->setAccessible(TRUE);
        $f->set(NULL, 'Modified');
        $this->assertEquals('Modified', $f->get(NULL));
      }
    }
  }
?>
