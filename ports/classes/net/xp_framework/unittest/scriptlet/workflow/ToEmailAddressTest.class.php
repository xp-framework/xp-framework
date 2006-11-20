<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest',
    'scriptlet.xml.workflow.casters.ToEmailAddress'
  );
  
  /**
   * Test the ToEmailAddress caster
   *
   * @see       xp://peer.mail.InternetAddress
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
   * @see       scriptlet.xml.workflow.casters.ToEmailAddress
   * @purpose   ToEmailAddress test
   */
  class ToEmailAddressTest extends AbstractCasterTest {

    /**
     * Return the caster
     *
     * @access  protected
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    function &caster() {
      return new ToEmailAddress();
    }

    /**
     * Test numerous valid email addresses
     *
     * @access  public
     */
    #[@test]
    function validEmailAdresses() {
      foreach (array('xp@php3.de', 'xp-cvs@php3.de') as $email) {
        $this->assertEquals(new InternetAddress($email), $this->castValue($email));
      }
    }

    /**
     * Test input without an @ sign
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function stringWithoutAt() {
      $this->castValue('FOO');
    }

    /**
     * Test empty input
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function emptyInput() {
      $this->castValue('');
    }
  }
?>
