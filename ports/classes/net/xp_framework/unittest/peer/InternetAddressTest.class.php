<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
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
     */
    #[@test]
    public function createAddress() {
      $address= new InternetAddress('kiesel@example.com');
      $this->assertEquals('kiesel', $address->localpart);
      $this->assertEquals('example.com', $address->domain);
    }
    
    /**
     * Test fromString
     *
     */
    #[@test]
    public function testFromString() {
      $strings= array(
        'Alex Kiesel <kiesel@example.com>',
        'kiesel@example.com (Alex Kiesel)',
        '"Alex Kiesel" <kiesel@example.com>',
        'kiesel@example.com',
        '=?iso-8859-1?Q?Alex_Kiesel?= <kiesel@example.com>'
      );
      foreach ($strings as $string) {
        $address= InternetAddress::fromString($string);
        $this->assertEquals('kiesel', $address->localpart);
        $this->assertEquals('example.com', $address->domain);
      }
    }

    /**
     * Test escaping
     *
     */
    #[@test]
    public function colonIsEscaped() {
      $this->assertEquals(
        '=?iso-8859-1?Q?I=3A=3ADev?= <idev@example.com>',
        create(new InternetAddress('idev@example.com', 'I::Dev'))->toString()
      );
    }

    /**
     * Test escaping
     *
     */
    #[@test]
    public function umlautsAreEscaped() {
      $this->assertEquals(
        '=?iso-8859-1?Q?M=FCcke?= <muecke@example.com>',
        create(new InternetAddress('muecke@example.com', 'Mücke'))->toString()
      );
    }
    
    /**
     * Test escaping
     *
     */
    #[@test]
    public function spaceIsEscaped() {
      $this->assertEquals(
        '=?iso-8859-1?Q?Alex_Kiesel?= <kiesel@example.com>', 
        create(new InternetAddress('kiesel@example.com', 'Alex Kiesel'))->toString()
      );
    }
  }
?>
