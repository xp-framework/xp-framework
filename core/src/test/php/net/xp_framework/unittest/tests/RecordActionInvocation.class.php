<?php namespace net\xp_framework\unittest\tests;

/**
 * This class is used in the TestActionTest 
 */
class RecordActionInvocation extends \lang\Object implements \unittest\TestAction {
  protected $field= null;

  /**
   * Constructor
   *
   * @param string $field
   */
  public function __construct($field) {
    $this->field= $field;
  }

  /**
   * Before test: Update field
   *
   * @param  unittest.TestCase $t
   */
  public function beforeTest(\unittest\TestCase $t) {
    $f= $t->getClass()->getField($this->field);
    $f->set($t, array_merge($f->get($t), array('before')));
  }

  /**
   * After test: Update field
   *
   * @param  unittest.TestCase $t
   */
  public function afterTest(\unittest\TestCase $t) {
    $f= $t->getClass()->getField($this->field);
    $f->set($t, array_merge($f->get($t), array('after')));
  }
}
