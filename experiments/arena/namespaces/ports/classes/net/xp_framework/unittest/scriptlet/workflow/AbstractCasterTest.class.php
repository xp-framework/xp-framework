<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractCasterTest.class.php 8974 2006-12-27 17:29:09Z friebe $
 */

  namespace net::xp_framework::unittest::scriptlet::workflow;

  ::uses('unittest.TestCase');
  
  /**
   * Scriptlet/Caster test case
   *
   * @see       xp://scriptlet.xml.workflow.casters.ParamCaster
   * @purpose   Base class for Caster test
   */
  abstract class AbstractCasterTest extends unittest::TestCase {
    public
      $caster = NULL;

    /**
     * Return the caster
     *
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    protected abstract function caster();

    /**
     * Setup method.
     *
     */
    public function setUp() {
      $this->caster= $this->caster();
    }

    /**
     * Helper method that uses the caster to cast a value. Returns 
     * the casted value.
     *
     * @param   mixed value
     * @return  mixed
     * @throws  lang.IllegalArgumentException in case the caster fails
     */
    protected function castValue($value) {
      if (!is_array($casted= call_user_func(array($this->caster, 'castValue'), array((string)$value)))) {
        throw(new lang::IllegalArgumentException('Cannot cast '.$value));
      }
      return array_pop($casted);
    }
  }
?>
