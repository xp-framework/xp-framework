<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.workflow.Wrapper'
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
  }
?>
