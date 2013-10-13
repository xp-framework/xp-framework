<?php namespace net\xp_framework\unittest\scriptlet\workflow;

use scriptlet\xml\workflow\Wrapper;
use scriptlet\xml\workflow\Handler;
use scriptlet\xml\workflow\Context;
use scriptlet\xml\XMLScriptletRequest;
use util\Date;

/**
 * TestCase
 *
 * @see  xp://scriptlet.xml.workflow.Wrapper
 */
class WrapperTest extends \unittest\TestCase {
  protected
    $wrapper= null,
    $handler= null;
 
  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->wrapper= new Wrapper();
    $this->handler= newinstance('scriptlet.xml.workflow.Handler', array(), '{}');
    $this->handler->setWrapper($this->wrapper);
    
    // Register parameters
    $this->wrapper->registerParamInfo(
      'orderdate',
      OCCURRENCE_OPTIONAL,
      Date::fromString('1977-12-14'),
      array('scriptlet.xml.workflow.casters.ToDate')
    );
    $this->wrapper->registerParamInfo(
      'shirt_size',
      OCCURRENCE_UNDEFINED,
      null,                // No default, required attribute
      null,                // No cast necessary
      null,                // No precheck necessary, non-empty suffices
      array('scriptlet.xml.workflow.checkers.OptionChecker', array('S', 'M', 'L', 'XL'))
    );
    $this->wrapper->registerParamInfo(
      'shirt_qty',
      OCCURRENCE_UNDEFINED,
      null,                // No default, required attribute
      array('scriptlet.xml.workflow.casters.ToInteger'),
      array('scriptlet.xml.workflow.checkers.NumericChecker'),
      array('scriptlet.xml.workflow.checkers.IntegerRangeChecker', 1, 10)
    );
    $this->wrapper->registerParamInfo(
      'notify_me',
      OCCURRENCE_OPTIONAL | OCCURRENCE_MULTIPLE,
      array(),
      null,
      null,
      null,
      'core:string',
      array('process', 'send')
    );
    $this->wrapper->registerParamInfo(
      'options',
      OCCURRENCE_OPTIONAL | OCCURRENCE_MULTIPLE,
      array(0, 0),
      array('scriptlet.xml.workflow.casters.ToInteger')
    );
    $this->wrapper->registerParamInfo(
      'person_ids',
      OCCURRENCE_MULTIPLE,
      null,
      array('scriptlet.xml.workflow.casters.ToInteger')
    );
  }
  
  /**
   * Test the getParamNames() method
   *
   */
  #[@test]
  public function getParamNames() {
    $this->assertEquals(
      array('orderdate', 'shirt_size', 'shirt_qty', 'notify_me', 'options', 'person_ids'), 
      $this->wrapper->getParamNames()
    );
  }

  /**
   * Test the getParamInfo() method
   *
   */
  #[@test]
  public function orderDateParamInfo() {
    $this->assertEquals(OCCURRENCE_OPTIONAL, $this->wrapper->getParamInfo('orderdate', PARAM_OCCURRENCE));
    $this->assertEquals(Date::fromString('1977-12-14'), $this->wrapper->getParamInfo('orderdate', PARAM_DEFAULT));
    $this->assertEquals(null, $this->wrapper->getParamInfo('orderdate', PARAM_PRECHECK));
    $this->assertEquals(null, $this->wrapper->getParamInfo('orderdate', PARAM_POSTCHECK));
    $this->assertEquals('core:string', $this->wrapper->getParamInfo('orderdate', PARAM_TYPE));
    $this->assertEquals(array(), $this->wrapper->getParamInfo('orderdate', PARAM_VALUES));
    $this->assertClass($this->wrapper->getParamInfo('orderdate', PARAM_CASTER), 'scriptlet.xml.workflow.casters.ToDate');
  }

  /**
   * Test the getParamInfo() method
   *
   */
  #[@test]
  public function shirtSizeParamInfo() {
    $this->assertEquals(OCCURRENCE_UNDEFINED, $this->wrapper->getParamInfo('shirt_size', PARAM_OCCURRENCE));
    $this->assertEquals(null, $this->wrapper->getParamInfo('shirt_size', PARAM_DEFAULT));
    $this->assertEquals(null, $this->wrapper->getParamInfo('shirt_size', PARAM_PRECHECK));
    $this->assertEquals(null, $this->wrapper->getParamInfo('shirt_size', PARAM_CASTER));
    $this->assertEquals('core:string', $this->wrapper->getParamInfo('shirt_size', PARAM_TYPE));
    $this->assertEquals(array(), $this->wrapper->getParamInfo('shirt_size', PARAM_VALUES));
    $this->assertClass($this->wrapper->getParamInfo('shirt_size', PARAM_POSTCHECK), 'scriptlet.xml.workflow.checkers.OptionChecker');
  }

  /**
   * Test the getValue() method
   *
   */
  #[@test]
  public function getValue() {
    $this->assertEquals(null, $this->wrapper->getValue('orderdate'));
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

    $this->wrapper->load($r, $this->handler);
  }

  /**
   * Helper method to assert a certain form error is available.
   *
   * Will fail if either no errors have occured at all or if the 
   * given error can not be found.
   *
   * @throws    unittest.AssertionFailedError
   */
  protected function assertFormError($field, $code) {
    if (!$this->handler->errorsOccured()) {     // Catch border-case
      $this->fail('No errors have occured', null, $code.' in field '.$field);
    }

    foreach ($this->handler->errors as $error) {
      if ($error[0].$error[1] == $code.$field) return;
    }

    $this->fail(
      'Error '.$code.' in field '.$field.' not in formerrors', 
      $this->handler->errors, 
      '(exists)'
    );
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function defaultValueUsedForMissingValue() {
    $this->loadFromRequest(array(
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
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
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
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
      'orderdate'  => '1977-12-14',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(
      new Date('1977-12-14'),
      $this->wrapper->getValue('orderdate')
    );
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function missingSizeValue() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_qty'  => 1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFormError('shirt_size', 'missing');
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function missingQtyValue() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFormError('shirt_qty', 'missing');
  }
  
  /**
   * Test the load() method
   *
   */
  #[@test]
  public function malformedSizeValue() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => '@',
      'shirt_qty'  => 1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFormError('shirt_size', 'scriptlet.xml.workflow.checkers.OptionChecker.invalidoption');
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function malformedQtyValue() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => -1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFormError('shirt_qty', 'scriptlet.xml.workflow.checkers.IntegerRangeChecker.toosmall');
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function multipleMalformedValues() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => '@',
      'shirt_qty'  => -1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFormError('shirt_size', 'scriptlet.xml.workflow.checkers.OptionChecker.invalidoption');
    $this->assertFormError('shirt_qty', 'scriptlet.xml.workflow.checkers.IntegerRangeChecker.toosmall');
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function missingValueForMultipleSelection() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(), $this->wrapper->getValue('notify_me'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function emptyValueForMultipleSelection() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array(),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(), $this->wrapper->getValue('notify_me'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function valueForMultipleSelection() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array('send'), $this->wrapper->getValue('notify_me'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function valuesForMultipleSelection() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send', 'process'),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array('send', 'process'), $this->wrapper->getValue('notify_me'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleOptionalField() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array('0010', '0020'),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(10, 20), $this->wrapper->getValue('options'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleOptionalFieldFirstEmpty() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array(null, '0020'),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(0, 20), $this->wrapper->getValue('options'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleOptionalAllEmpty() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array('', ''),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(0, 0), $this->wrapper->getValue('options'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleOptionalParameterMissing() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(0, 0), $this->wrapper->getValue('options'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleField() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array('0010', '0020'),
      'person_ids' => array('1549', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(1549, 1552), $this->wrapper->getValue('person_ids'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleFieldFirstEmpty() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array(null, '0020'),
      'person_ids' => array('', '1552')
    ));
    $this->assertFalse($this->handler->errorsOccured());
    $this->assertEquals(array(0, 1552), $this->wrapper->getValue('person_ids'));
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleFieldAllEmpty() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array(null, '0020'),
      'person_ids' => array()
    ));
    $this->assertTrue($this->handler->errorsOccured());
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function castMultipleFieldParameterMissing() {
    $this->loadFromRequest(array(
      'orderdate'  => '',
      'shirt_size' => 'S',
      'shirt_qty'  => 1,
      'notify_me'  => array('send'),
      'options'    => array(null, '0020')
    ));
    $this->assertTrue($this->handler->errorsOccured());
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function missingFileUpload() {

    // Register "file_upload" param
    $this->wrapper->registerParamInfo(
      'file_upload',
      OCCURRENCE_UNDEFINED,
      null,
      array('scriptlet.xml.workflow.casters.ToFileData'),
      array('scriptlet.xml.workflow.checkers.FileUploadPrechecker'),
      null
    );

    $this->loadFromRequest(array(
      'orderdate'   => '',
      'shirt_size'  => 'S',
      'shirt_qty'   => 1,
      'notify_me'   => array('send'),
      'options'     => array(null, '0020'),
      'person_ids'  => array('', '1552'),
      'file_upload' => array(
        'name'     => '',
        'type'     => '',
        'tmp_name' => '',
        'error'    => UPLOAD_ERR_NO_FILE,
        'size'     => 0
      )
    ));
    $this->assertFormError('file_upload', 'missing');
  }

  /**
   * Test the load() method
   *
   */
  #[@test]
  public function ignoreMissingOptionalFileUpload() {

    // Register "file_upload" param
    $this->wrapper->registerParamInfo(
      'file_upload',
      OCCURRENCE_OPTIONAL,
      null,
      array('scriptlet.xml.workflow.casters.ToFileData'),
      array('scriptlet.xml.workflow.checkers.FileUploadPrechecker'),
      null
    );

    $this->loadFromRequest(array(
      'orderdate'   => '',
      'shirt_size'  => 'S',
      'shirt_qty'   => 1,
      'notify_me'   => array('send'),
      'options'     => array(null, '0020'),
      'person_ids'  => array('', '1552'),
      'file_upload' => array(
        'name'     => '',
        'type'     => '',
        'tmp_name' => '',
        'error'    => UPLOAD_ERR_NO_FILE,
        'size'     => 0
      )
    ));
    $this->assertFalse($this->handler->errorsOccured());
  }
}
