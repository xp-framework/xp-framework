<?php namespace net\xp_framework\unittest\scriptlet\workflow;

use scriptlet\xml\workflow\casters\ToEmailAddress;


/**
 * Test the ToEmailAddress caster
 *
 * @see  xp://peer.mail.InternetAddress
 * @see  xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
 * @see  xp://scriptlet.xml.workflow.casters.ToEmailAddress
 */
class ToEmailAddressTest extends AbstractCasterTest {

  /**
   * Return the caster
   *
   * @return  scriptlet.xml.workflow.casters.ParamCaster
   */
  protected function caster() {
    return new ToEmailAddress();
  }

  /**
   * Test numerous valid email addresses
   */
  #[@test, @values(array('xp@example.com', 'xp-cvs@example.com'))]
  public function validEmailAdresses($email) {
    $this->assertEquals(new \peer\mail\InternetAddress($email), $this->castValue($email));
  }

  /**
   * Test input without an @ sign
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function stringWithoutAt() {
    $this->castValue('FOO');
  }

  /**
   * Test empty input
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function emptyInput() {
    $this->castValue('');
  }

  /**
   * Test "@" by itself
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function onlyAtSign() {
    $this->castValue('@');
  }
}
