<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.Handler',
    'scriptlet.xml.workflow.Context',
    'scriptlet.xml.XMLScriptletRequest'
  );

  /**
   * TestCase
   *
   * @see      xp://scriptlet.xml.workflow.Wrapper
   * @purpose  Unittest
   */
  class WrapperTest extends TestCase {
    protected
      $wrapper= NULL;
 
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->wrapper= new Wrapper();
      $this->wrapper->registerParamInfo(
        'orderdate',
        OCCURRENCE_OPTIONAL,
        Date::fromString('1977-12-14'),
        array('scriptlet.xml.workflow.casters.ToDate')
      );
    }
    
    /**
     * Test the getParamNames() method
     *
     */
    #[@test]
    public function getParamNames() {
      $this->assertEquals(
        array('orderdate'), 
        $this->wrapper->getParamNames()
      );
    }

    /**
     * Test the getParamInfo() method
     *
     */
    #[@test]
    public function getParamInfo() {
      $this->assertEquals(OCCURRENCE_OPTIONAL, $this->wrapper->getParamInfo('orderdate', PARAM_OCCURRENCE));
      $this->assertEquals(Date::fromString('1977-12-14'), $this->wrapper->getParamInfo('orderdate', PARAM_DEFAULT));
      $this->assertEquals(NULL, $this->wrapper->getParamInfo('orderdate', PARAM_PRECHECK));
      $this->assertEquals(NULL, $this->wrapper->getParamInfo('orderdate', PARAM_POSTCHECK));
      $this->assertEquals('core:string', $this->wrapper->getParamInfo('orderdate', PARAM_TYPE));
      $this->assertEquals(array(), $this->wrapper->getParamInfo('orderdate', PARAM_VALUES));
      $this->assertClass($this->wrapper->getParamInfo('orderdate', PARAM_CASTER), 'scriptlet.xml.workflow.casters.ToDate');
    }

    /**
     * Test the getValue() method
     *
     */
    #[@test]
    public function getValue() {
      $this->assertEquals(NULL, $this->wrapper->getValue('orderdate'));
    }

    /**
     * Test the setValue() method
     *
     */
    #[@test]
    public function setValue() {
      with ($d= Date::now()); {
        $this->wrapper->setValue('orderdate', $d);
        $this->assertEquals($d, $this->wrapper->getValue('orderdate'));
      }
    }
    
    /**
     * Helper method to simulate form submission
     *
     */
    protected function loadFromRequest($params= array()) {
      $r= new XMLScriptletRequest();
      
      foreach ($params as $key => $value) {
        $r->setParam($key, $value);
      }

      $this->handler= newinstance('Handler', array(), '{}');
      $this->wrapper->load($r, $this->handler);
    }

    /**
     * Test the load() method
     *
     */
    #[@test]
    public function defaultValueUsedForMissingValue() {
      $this->loadFromRequest();
      $this->assertEquals(
        $this->wrapper->getParamInfo('orderdate', PARAM_DEFAULT), 
        $this->wrapper->getValue('orderdate')
      );
    }

    /**
     * Test the load() method
     *
     */
    #[@test]
    public function defaultValueUsedForEmptyValue() {
      $this->loadFromRequest(array(
        'orderdate' => ''
      ));
      $this->assertEquals(
        $this->wrapper->getParamInfo('orderdate', PARAM_DEFAULT), 
        $this->wrapper->getValue('orderdate')
      );
    }

    /**
     * Test the load() method
     *
     */
    #[@test]
    public function valueUsed() {
      $this->loadFromRequest(array(
        'orderdate' => '1977-12-14'
      ));
      $this->assertEquals(
        new Date('1977-12-14'),
        $this->wrapper->getValue('orderdate')
      );
    }
  }
?>
