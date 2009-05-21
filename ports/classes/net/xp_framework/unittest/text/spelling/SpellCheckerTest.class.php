<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.spelling.SpellChecker'
  );

  /**
   * TestCase
   *
   * @see      xp://text.spelling.SpellChecker
   */
  class SpellCheckerTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      if (!extension_loaded('pspell')) {
        throw new PrerequisitesNotMetError('Spelling not available', NULL, array('ext/pspell'));
      }
    }
    
    /**
     * Create a new spell checker instance with the specified language
     *
     * @param   string language
     * @return  text.spelling.SpellChecker
     */
    protected function spelling($language) {
      return new SpellChecker($language);
    }

    /**
     * Tests creating a spellchecker with an unavailable language
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unavailableLanguage() {
      $this->spelling('@@unavailable@@');
    }
    
    /**
     * Tests check() method
     *
     */
    #[@test]
    public function correctlySpelled() {
      $this->assertTrue($this->spelling('en')->check('Hello'));
    }

    /**
     * Tests check() method
     *
     */
    #[@test]
    public function misSpelled() {
      $this->assertFalse($this->spelling('en')->check('Bahnhof'));
    }

    /**
     * Tests suggestionsFor() method
     *
     */
    #[@test]
    public function suggestions() {
      $suggestionsFor= $this->spelling('en')->suggestionsFor('delibrate');
      $this->assertArray($suggestionsFor);
      $this->assertNotEquals(0, sizeof($suggestionsFor));
    }
  }
?>
