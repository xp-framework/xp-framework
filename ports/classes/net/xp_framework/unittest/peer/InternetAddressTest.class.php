<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'peer.mail.InternetAddress'
  );

  /**
   * Testcase for InternetAddress class
   *
   * @purpose  Testcase
   */
  class InternetAddressTest extends TestCase {

    /**
     * Test construction
     *
     * @access  public
     */
    #[@test]
    function createAddress() {
      $i= &new InternetAddress('kiesel@example.com');
    }
    
    /**
     * Test fromString
     *
     * @access  public
     */
    #[@test]
    function testFromString() {
      $strings= array(
        'Alex Kiesel <kiesel@example.com>',
        'kiesel@example.com (Alex Kiesel)',
        '"Alex Kiesel" <kiesel@example.com>',
        'kiesel@example.com',
        '=?iso-8859-1?Q?Alex_Kiesel?= <kiesel@example.com>'
      );
      foreach ($strings as $string) {
        $address= &InternetAddress::fromString($string);
        $this->assertEquals('kiesel', $address->localpart);
        $this->assertEquals('example.com', $address->domain);
      }
    }
    
    /**
     * Test toString
     *
     * @access  public
     */
    #[@test]
    function testToString() {
      $address= &new InternetAddress('kiesel@example.com', 'Alex Kiesel');
      $this->assertEquals('=?iso-8859-1?Q?Alex_Kiesel?= <kiesel@example.com>', $address->toString());
    }
  }
?>
