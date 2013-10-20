<?php namespace net\xp_framework\unittest\scriptlet;

use unittest\TestCase;
use scriptlet\Preference;


/**
 * TestCase
 *
 * @see   http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
 * @see   xp://scriptlet.Preference
 */
class PreferenceTest extends TestCase {

  /**
   * Test constructor
   *
   */
  #[@test]
  public function create_with_single_preference() {
    $this->assertEquals(
      create(new Preference(array('text/xml'))),
      create(new Preference('text/xml'))
    );
  }

  /**
   * Test constructor
   *
   */
  #[@test]
  public function create_with_multiple_preferences() {
    $this->assertEquals(
      create(new Preference(array('text/xml', 'text/plain'))),
      create(new Preference('text/xml,text/plain'))
    );
  }

  /**
   * Test constructor
   *
   */
  #[@test]
  public function create_with_multiple_preferences_and_qvalues() {
    $this->assertEquals(
      create(new Preference(array('text/xml;q=1.0', 'text/plain;q=0.9'))),
      create(new Preference('text/xml;q=1.0,text/plain;q=0.9'))
    );
  }

  /**
   * Test constructor
   *
   */
  #[@test]
  public function create_with_multiple_preferences_and_qvalues_reordered() {
    $this->assertEquals(
      create(new Preference(array('text/xml;q=1.0', 'text/plain;q=0.9'))),
      create(new Preference('text/plain;q=0.9,text/xml;q=1.0'))
    );
  }

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
   * Test all()
   *
   */
  #[@test]
  public function preferences_reordered() {
    $this->assertEquals(
      array('text/plain', 'text/xml'), 
      create(new Preference('text/xml;q=0.9, text/plain;q=1.0'))->all()
    );
  }

  /**
   * Test all()
   *
   */
  #[@test]
  public function rfc2616_more_specific_ranges_override() {
    $this->assertEquals(
      array('text/html;level=1', 'text/html', 'text/*', '*/*'), 
      create(new Preference('text/*, text/html, text/html;level=1, */*'))->all()
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
  public function text_any_matching_one_of_supported() {
    $this->assertEquals(
      'text/html', 
      create(new Preference('text/*'))->match(array('application/xml', 'text/html', 'text/plain'))
    );
  }

  /**
   * Test match()
   *
   */
  #[@test]
  public function text_any_matching_first_of_supported() {
    $this->assertEquals(
      'text/plain', 
      create(new Preference('text/*'))->match(array('text/plain', 'text/html'))
    );
  }

  /**
   * Test match()
   *
   */
  #[@test]
  public function any_any_matches_first_of_supported() {
    $this->assertEquals(
      'application/xml', 
      create(new Preference('*/*'))->match(array('application/xml', 'text/html', 'text/plain'))
    );
  }

  /**
   * Test match()
   *
   */
  #[@test]
  public function application_any_matches_first_of_supported() {
    $this->assertEquals(
      'application/xml', 
      create(new Preference('*/*;q=0.1; application/*'))->match(array('application/xml', 'text/html', 'text/plain'))
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

  /**
   * Test match()
   *
   */
  #[@test]
  public function application_json_not_supported() {
    $this->assertNull(
      create(new Preference('application/json'))->match(array('text/html', 'text/plain'))
    );
  }

  /**
   * Test match()
   *
   */
  #[@test]
  public function application_any_not_supported() {
    $this->assertNull(
      create(new Preference('application/*'))->match(array('text/html', 'text/plain'))
    );
  }

  /**
   * Test toString()
   *
   */
  #[@test]
  public function single_preference_string() {
    $this->assertEquals(
      'scriptlet.Preference<text/xml>',
      create(new Preference('text/xml'))->toString()
    );
  }

  /**
   * Test toString()
   *
   */
  #[@test]
  public function preferences_string() {
    $this->assertEquals(
      'scriptlet.Preference<text/xml, text/html>',
      create(new Preference('text/xml, text/html'))->toString()
    );
  }

  /**
   * Test toString()
   *
   */
  #[@test]
  public function preferences_with_qvalue_string() {
    $this->assertEquals(
      'scriptlet.Preference<text/xml, text/html;q=0.8>',
      create(new Preference('text/xml, text/html;q=0.8'))->toString()
    );
  }

  /**
   * Test toString()
   *
   */
  #[@test]
  public function one_point_zero_qvalue_omitted_in_string() {
    $this->assertEquals(
      'scriptlet.Preference<text/xml, text/html;q=0.8>',
      create(new Preference('text/xml;q=1.0, text/html;q=0.8'))->toString()
    );
  }

  /**
   * Test qualityOf()
   *
   */
  #[@test]
  public function quality_of_xml() {
    $this->assertEquals(
      1.0,
      create(new Preference('text/xml;q=1.0, text/html;q=0.8'))->qualityOf('text/xml')
    );
  }

  /**
   * Test qualityOf()
   *
   */
  #[@test]
  public function quality_of_html() {
    $this->assertEquals(
      0.8,
      create(new Preference('text/xml;q=1.0, text/html;q=0.8'))->qualityOf('text/html')
    );
  }

  /**
   * Test qualityOf()
   *
   */
  #[@test]
  public function quality_of_plain() {
    $this->assertEquals(
      1.0,
      create(new Preference('text/xml, text/plain'))->qualityOf('text/plain')
    );
  }

  /**
   * Test qualityOf()
   *
   */
  #[@test]
  public function quality_of_plain_with_asterisk() {
    $this->assertEquals(
      0.99999,
      create(new Preference('text/*'))->qualityOf('text/plain', 6)
    );
  }
}
