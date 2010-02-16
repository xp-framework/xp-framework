<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'util.collections.HashTable');

  /**
   * Test the XP reflection API
   *
   * @see      xp://lang.reflect.Parameter
   * @purpose  Testcase
   */
  class ParameterTest extends TestCase {

    /**
     * Method without functionality to be used by tests.
     *
     */
    private function initialize() { }
  
    /**
     * Method without functionality to be used by tests.
     *
     * @param   string name
     */
    private function setName($name) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   util.Date date default NULL
     */
    private function setDate($date= NULL) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   string format
     * @param   string* values
     */
    private function printf($format, $values= NULL) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   * value
     * @param   util.collections.Map context
     */
    private function serialize($value, Map $context) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   util.collections.HashTable map
     */
    private function setHashTable(HashTable $map) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   array<string, *> map
     */
    private function setHash(array $map) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   string[] map default array
     */
    private function setArray(array $map= array()) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   int a
     * @param   int b default 1
     */
    private function inc($a, $b= 1) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   boolean new
     */
    private function setStatus($new= FALSE) { }
  
    /**
     * Tests Method::numParameters()
     *
     */
    #[@test]
    public function numParameters() {
      $this->assertEquals(0, $this->getClass()->getMethod('initialize')->numParameters(), 'initialize');
      $this->assertEquals(1, $this->getClass()->getMethod('setName')->numParameters(), 'setName');
      $this->assertEquals(1, $this->getClass()->getMethod('setDate')->numParameters(), 'setDate');
      $this->assertEquals(2, $this->getClass()->getMethod('printf')->numParameters(), 'printf');
      $this->assertEquals(2, $this->getClass()->getMethod('serialize')->numParameters(), 'serialize');
    }

    /**
     * Tests Method::getParameter
     *
     */
    #[@test]
    public function getExistingParameter() {
      $this->assertClass($this->getClass()->getMethod('setName')->getParameter(0), 'lang.reflect.Parameter');
    }

    /**
     * Tests Method::getParameter
     *
     */
    #[@test]
    public function getNonExistantParameter() {
      $this->assertNull($this->getClass()->getMethod('initialize')->getParameter(0));
    }

    /**
     * Tests Method::getParameters
     *
     */
    #[@test]
    public function initializeParameters() {
      $this->assertEquals(array(), $this->getClass()->getMethod('initialize')->getParameters());
    }

    /**
     * Tests Method::getParameters
     *
     */
    #[@test]
    public function setNameParameters() {
      $params= $this->getClass()->getMethod('setName')->getParameters();
      $this->assertArray($params);
      $this->assertEquals(1, sizeof($params));
      $this->assertClass($params[0], 'lang.reflect.Parameter');
    }

    /**
     * Tests Method::getParameters
     *
     */
    #[@test]
    public function serializeParameters() {
      $params= $this->getClass()->getMethod('serialize')->getParameters();
      $this->assertArray($params);
      $this->assertEquals(2, sizeof($params));
      $this->assertClass($params[0], 'lang.reflect.Parameter');
      $this->assertClass($params[1], 'lang.reflect.Parameter');
    }

    /**
     * Helper method to retrieve a method's parameter by its offset
     *
     * @param   string name
     * @param   string offset
     * @return  lang.reflect.Parameter
     */
    protected function methodParameter($name, $offset) {
      return $this->getClass()->getMethod($name)->getParameter($offset);
    }
  
    /**
     * Tests Parameter::getName()
     *
     */
    #[@test]
    public function name() {
      $this->assertEquals('name', $this->methodParameter('setName', 0)->getName(), 'setName#0');
      $this->assertEquals('date', $this->methodParameter('setDate', 0)->getName(), 'setDate#0');
      $this->assertEquals('value', $this->methodParameter('serialize', 0)->getName(), 'serialize#0');
      $this->assertEquals('context', $this->methodParameter('serialize', 1)->getName(), 'serialize#1');
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function stringType() {
      $this->assertEquals(Primitive::$STRING, $this->methodParameter('setName', 0)->getType());
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function integerType() {
      $this->assertEquals(Primitive::$INTEGER, $this->methodParameter('inc', 0)->getType(), 'inc$a');
      $this->assertEquals(Primitive::$INTEGER, $this->methodParameter('inc', 1)->getType(), 'inc$b');
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function booleanType() {
      $this->assertEquals(Primitive::$BOOLEAN, $this->methodParameter('setStatus', 0)->getType());
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function anyType() {
      $this->assertEquals(Type::$VAR, $this->methodParameter('serialize', 0)->getType());
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function classType() {
      $this->assertEquals(XPClass::forName('util.Date'), $this->methodParameter('setDate', 0)->getType());
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function interfaceType() {
      $this->assertEquals(XPClass::forName('util.collections.Map'), $this->methodParameter('serialize', 1)->getType());
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function arrayType() {
      $this->assertEquals(Primitive::$ARRAY, $this->methodParameter('setArray', 0)->getType());
    }

    /**
     * Tests Parameter::getType()
     *
     */
    #[@test]
    public function varArgsArrayType() {
      $this->assertEquals(Primitive::$ARRAY, $this->methodParameter('printf', 1)->getType());
    }

    /**
     * Tests Parameter::getTypeRestriction()
     *
     */
    #[@test]
    public function typeRestriction() {
      $this->assertNull($this->methodParameter('setName', 0)->getTypeRestriction());
    }

    /**
     * Tests Parameter::getTypeRestriction()
     *
     */
    #[@test]
    public function isOptional() {
      $this->assertFalse($this->methodParameter('setName', 0)->isOptional());
      $this->assertTrue($this->methodParameter('setDate', 0)->isOptional());
    }

    /**
     * Tests Parameter::getDefaultValue()
     *
     */
    #[@test]
    public function nullDefaultValue() {
      $this->assertNull($this->methodParameter('setDate', 0)->getDefaultValue());
    }

    /**
     * Tests Parameter::getDefaultValue()
     *
     */
    #[@test]
    public function integerDefaultValue() {
      $this->assertEquals(1, $this->methodParameter('inc', 1)->getDefaultValue());
    }

    /**
     * Tests Parameter::getDefaultValue()
     *
     */
    #[@test]
    public function booleanDefaultValue() {
      $this->assertEquals(FALSE, $this->methodParameter('setStatus', 0)->getDefaultValue());
    }

    /**
     * Tests Parameter::getDefaultValue()
     *
     */
    #[@test]
    public function arrayDefaultValue() {
      $this->assertEquals(array(), $this->methodParameter('setArray', 0)->getDefaultValue());
    }

    /**
     * Tests Parameter::toString()
     *
     */
    #[@test]
    public function stringOfOptional() {
      $this->assertEquals(
        'lang.reflect.Parameter<lang.Primitive<boolean> new= false>', 
        $this->methodParameter('setStatus', 0)->toString()
      );
    }

    /**
     * Tests Parameter::toString()
     *
     */
    #[@test]
    public function stringOfAnyTyped() {
      $this->assertEquals(
        'lang.reflect.Parameter<lang.Type<var> value>', 
        $this->methodParameter('serialize', 0)->toString()
      );
    }

    /**
     * Tests Parameter::toString()
     *
     */
    #[@test]
    public function stringOfClassTyped() {
      $this->assertEquals(
        'lang.reflect.Parameter<lang.XPClass<util.collections.Map> context>', 
        $this->methodParameter('serialize', 1)->toString()
      );
    }

    /**
     * Tests Parameter::getDefaultValue() throws an exception if
     * an Parameter does not have a default value
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function defaultValueOfNonOptional() {
      $this->methodParameter('setName', 0)->getDefaultValue();
    }

    /**
     * Tests non-type hinted parameter's type restriction is NULL
     *
     */
    #[@test]
    public function unRestrictedParamType() {
      $this->assertNull($this->methodParameter('setDate', 0)->getTypeRestriction());
    }

    /**
     * Tests type hinted parameter's type is returned via getTypeRestriction()
     *
     */
    #[@test]
    public function restrictedParamClassType() {
      $this->assertEquals(
        XPClass::forName('util.collections.HashTable'),
        $this->methodParameter('setHashTable', 0)->getTypeRestriction()
      );
    }

    /**
     * Tests type hinted parameter's type is returned via getTypeRestriction()
     *
     */
    #[@test]
    public function restrictedParamArrayType() {
      $this->assertEquals(
        Primitive::$ARRAY,
        $this->methodParameter('setHash', 0)->getTypeRestriction(),
        'setHash'
      );
      $this->assertEquals(
        Primitive::$ARRAY,
        $this->methodParameter('setArray', 0)->getTypeRestriction(),
        'setArray'
      );
    }
  }
?>
