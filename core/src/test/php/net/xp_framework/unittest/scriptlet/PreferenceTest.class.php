<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.Preference'
  );

  /**
   * TestCase
   *
   * @see   http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
   * @see   xp://scriptlet.Preference
   */
  class PreferenceTest extends TestCase {

    /**
     * Test all()
     *
     */
    #[@test]
    public function single_preference() {
      $this->assertEquals(
        array('text/xml'), 
        create(new Preference('text/xml'))->all()
      );
    }

    /**
     * Test all()
     *
     */
    #[@test]
    public function preferences_separated_by_comma() {
      $this->assertEquals(
        array('text/xml', 'text/plain'), 
        create(new Preference('text/xml,text/plain'))->all()
      );
    }

    /**
     * Test all()
     *
     */
    #[@test]
    public function preferences_separated_by_comma_and_space() {
      $this->assertEquals(
        array('text/xml', 'text/plain'), 
        create(new Preference('text/xml, text/plain'))->all()
      );
    }

    /**
     * Test all()
     *
     */
    #[@test]
    public function preferences_with_qvalues() {
      $this->assertEquals(
        array('text/xml', 'text/plain'), 
        create(new Preference('text/xml;q=1.0, text/plain;q=0.9'))->all()
      );
    }

    /**
     * Test all()
     *
     */
    #[@test]
    public function preferences_with_qvalues_and_spaces() {
      $this->assertEquals(
        array('text/xml', 'text/plain'), 
        create(new Preference('text/xml; q=1.0, text/plain; q=0.9'))->all()
      );
    }

    /**
     * Test preferred()
     *
     */
    #[@test]
    public function single_best_preference() {
      $this->assertEquals(
        'text/xml', 
        create(new Preference('text/xml'))->preferred()
      );
    }

    /**
     * Test preferred()
     *
     */
    #[@test]
    public function one_preference_with_qvalue_one_without() {
      $this->assertEquals(
        'text/plain', 
        create(new Preference('text/xml;q=0.9, text/plain'))->preferred()
      );
    }

    /**
     * Test preferred()
     *
     */
    #[@test]
    public function both_preferences_with_qvalues() {
      $this->assertEquals(
        'text/plain', 
        create(new Preference('text/xml;q=0.7, text/plain;q=1.0'))->preferred()
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function preference_exactly_matching_supported() {
      $this->assertEquals(
        'text/xml', 
        create(new Preference('text/xml'))->match(array('text/xml'))
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function preference_matching_one_of_supported() {
      $this->assertEquals(
        'text/plain', 
        create(new Preference('text/plain'))->match(array('text/xml', 'text/html', 'text/plain'))
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function best_preference_matching_one_of_supported() {
      $this->assertEquals(
        'text/html', 
        create(new Preference('text/plain;q=0.9, text/html'))->match(array('text/xml', 'text/html', 'text/plain'))
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function first_preference_matching_one_of_supported() {
      $this->assertEquals(
        'text/plain', 
        create(new Preference('text/plain, text/html'))->match(array('text/xml', 'text/html', 'text/plain'))
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function ie9_default_accept_match_html_vs_plaintext() {
      $this->assertEquals(
        'text/html', 
        create(new Preference('text/html, application/xhtml+xml, */*'))->match(array('text/plain', 'text/html'))
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function ff11_default_accept_match_html_vs_plaintext() {
      $this->assertEquals(
        'text/html', 
        create(new Preference('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'))->match(array('text/plain', 'text/html'))
      );
    }

    /**
     * Test match()
     *
     */
    #[@test]
    public function chrome_21_default_accept_match_html_vs_plaintext() {
      $this->assertEquals(
        'text/html', 
        create(new Preference('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'))->match(array('text/plain', 'text/html'))
      );
    }

    /**
     * Test match()
     *
     * @see  http://en.wikipedia.org/wiki/Content_negotiation
     */
    #[@test]
    public function wikipedia_example_match_html_vs_plaintext() {
      $this->assertEquals(
        'text/html', 
        create(new Preference('text/html; q=1.0, text/*; q=0.8, image/gif; q=0.6, image/jpeg; q=0.6, image/*; q=0.5, */*; q=0.1
'))->match(array('text/plain', 'text/html'))
      );
    }
  }
?>
