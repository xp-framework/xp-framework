<?php namespace net\xp_framework\unittest\tests;

/**
 * This class is used in the TestClassActionTest 
 */
class RecordClassActionInvocation extends \lang\Object implements \unittest\TestClassAction {
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
   * Before test class: Update field
   *
   * @param  lang.XPClass $c
   */
  public function beforeTestClass(\lang\XPClass $c) {
    $f= $c->getField($this->field);
    $f->set(null, array_merge($f->get(null), array('before')));
  }

  /**
   * After test class: Update "run" field
   *
   * @param  lang.XPClass $c
   */
  public function afterTestClass(\lang\XPClass $c) {
    $f= $c->getField('run');
    $f->set(null, array_merge($f->get(null), array('after')));
  }
}
