<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.unittest.TestCase');
  
  /**
   * Scriptlet/Caster test case
   *
   * @model     abstract
   * @see       xp://scriptlet.xml.workflow.casters.ParamCaster
   * @purpose   Base class for Caster test
   */
  class AbstractCasterTest extends TestCase {
    var
      $caster = NULL;

    /**
     * Return the caster
     *
     * @access  protected
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    function &caster() { }

    /**
     * Setup method.
     *
     * @model   final
     * @access  public
     */
    function setUp() {
      $this->caster= &$this->caster();
    }

    /**
     * Helper method that uses the caster to cast a value. Returns 
     * the casted value.
     *
     * @access  protected
     * @param   mixed value
     * @return  mixed
     * @throws  lang.IllegalArgumentException in case the caster fails
     */
    function castValue($value) {
      if (!is_array($casted= call_user_func(array(&$this->caster, 'castValue'), array((string)$value)))) {
        return throw(new IllegalArgumentException('Cannot cast '.$value));
      }
      return array_pop($casted);
    }
  }
?>
