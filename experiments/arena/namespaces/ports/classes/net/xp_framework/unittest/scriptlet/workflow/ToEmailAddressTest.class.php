<?php
/* This class is part of the XP framework
 *
 * $Id: ToEmailAddressTest.class.php 8974 2006-12-27 17:29:09Z friebe $
 */

  namespace net::xp_framework::unittest::scriptlet::workflow;

  ::uses(
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
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    protected function caster() {
      return new scriptlet::xml::workflow::casters::ToEmailAddress();
    }

    /**
     * Test numerous valid email addresses
     *
     */
    #[@test]
    public function validEmailAdresses() {
      foreach (array('xp@php3.de', 'xp-cvs@php3.de') as $email) {
        $this->assertEquals(new peer::mail::InternetAddress($email), $this->castValue($email));
      }
    }

    /**
     * Test input without an @ sign
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringWithoutAt() {
      $this->castValue('FOO');
    }

    /**
     * Test empty input
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyInput() {
      $this->castValue('');
    }
  }
?>
