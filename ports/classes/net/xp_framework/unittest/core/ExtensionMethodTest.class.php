<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.ArrayList',
    'util.collections.Vector',
    'net.xp_framework.unittest.core.ArrayListExtensions',
    'net.xp_framework.unittest.core.IListExtensions',
    'net.xp_framework.unittest.core.UsesStringExtensions'
  );

  /**
   * Tests extension methods
   *
   * @see   xp://net.xp_framework.unittest.core.ArrayListExtensions
   * @see   xp://net.xp_framework.unittest.core.ClassExtensions
   */
  class ExtensionMethodTest extends TestCase {

    /**
     * Test ArrayListExtensions::find() method for an instance of ArrayList
     *
     */
    #[@test]
    public function findInArrayList() {
      $this->assertEquals(
        5,
        create(new ArrayList(1, 2, 3, 4, 5, 6))->find(create_function('$e', 'return $e % 5 == 0;'))
      );
    }
  
    /**
     * Test ArrayListExtensions::findAll() method for an instance of ArrayList
     *
     */
    #[@test]
    public function findAllInArrayList() {
      $this->assertEquals(
        new ArrayList(2, 4, 6),
        create(new ArrayList(1, 2, 3, 4, 5, 6))->findAll(create_function('$e', 'return $e % 2 == 0;'))
      );
    }

    /**
     * Test ArrayListExtensions::find() method for an instance of an 
     * ArrayList subclass
     *
     */
    #[@test]
    public function findInArrayListSubclass() {
      $this->assertEquals(
        1,
        newinstance('lang.types.ArrayList', array(1, 2, 3), '{}')->find(create_function('$e', 'return $e % 2 == 1;'))
      );
    }

    /**
     * Test call to non-existant method in ArrayList class
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= 'Call to undefined method ArrayList::nonExistant')]
    public function callNonExistantArrayListMethod() {
      ArrayList::newInstance(0)->nonExistant();
    }

    /**
     * Test IListExtensions::find() method for an instance of Vector
     * (which implements IList)
     *
     */
    #[@test]
    public function findInVector() {
      $v= create('new Vector<String>', array(new String('Hello'), new String('World!')));
      $this->assertEquals(
        new String('World!'),
        $v->find(create_function('$e', 'return $e->length() > 5;'))
      );
    }
  
    /**
     * Test IListExtensions::findAll() method for an instance of Vector
     * (which implements IList)
     *
     */
    #[@test]
    public function findAllInVector() {
      $v= create('new Vector<String>', array(new String('Hi'), new String('World'), new String('!')));
      $this->assertEquals(
        create('new Vector<String>', array(new String('Hi'), new String('!'))),
        $v->findAll(create_function('$e', 'return $e->length() < 5;'))
      );
    }

    /**
     * Test extension methods cannot overwrite real methods
     *
     */
    #[@test]
    public function extensionMethodCannotOverwriteExisting() {
      newinstance('lang.Object', array(), '{
        static function __static() {
          xp::extensions("util.collections.IList", __CLASS__);
        }
        
        #[@extension]
        public static function isEmpty(IList $l) {
          throw new IllegalStateException("Unreachable");
        }
      }');

      create('new Vector<String>()')->isEmpty();
    }

    /**
     * Test extension methods must be static
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= 'Call to undefined method Vector::nonStaticExtensionMethod')]
    public function extensionMethodMustBeStatic() {
      newinstance('lang.Object', array(), '{
        static function __static() {
          xp::extensions("util.collections.IList", __CLASS__);
        }
        
        #[@extension]
        public function nonStaticExtensionMethod(IList $l) {
          throw new IllegalStateException("Unreachable");
        }
      }');

      create('new Vector<String>()')->nonStaticExtensionMethod();
    }

    /**
     * Test extension methods must be static
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= 'Call to undefined method Vector::notAnnotatedExtensionMethod')]
    public function extensionMethodMustBeAnnotated() {
      newinstance('lang.Object', array(), '{
        static function __static() {
          xp::extensions("util.collections.IList", __CLASS__);
        }
        
        public static function notAnnotatedExtensionMethod(IList $l) {
          throw new IllegalStateException("Unreachable");
        }
      }');

      create('new Vector<String>()')->notAnnotatedExtensionMethod();
    }

    /**
     * Test extension methods apply to all scopes - that is, even though
     * this class doesn't really import the StringExtensions class, the 
     * extension methods therein are available because a class using this
     * StringExtension class is loaded. This is a limitation of the runtime 
     * implementation.
     *
     */
    #[@test]
    public function applyToAllScopes() {
      $this->assertTrue(create(new String('Hello'))->matches(Pattern::compile('H[ae]llo')));
    }
  }
?>
