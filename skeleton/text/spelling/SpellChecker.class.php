<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Spell checker
   *
   * <code>
   *   $spell= new SpellChecker('en');
   *   $spell->check('Hello');          // TRUE
   *   $spell->check('Bahnhof');        // FALSE
   *
   *   $spell->suggestionsFor('delibrate'); //  [ "deliberate", "deliberator", ... ]
   * </code>
   *
   * @ext      pspell
   */
  class SpellChecker extends Object {
    protected $handle= NULL;
    
    /**
     * Constructor
     *
     * @see     php://pspell_new
     * @param   string language
     * @param   string spelling
     * @param   string jargon
     * @param   string encoding 
     * @param   int mode 
     * @throws  lang.IllegalArgumentException
     */
    public function __construct($language, $spelling= NULL, $jargon= NULL, $encoding= NULL, $mode= PSPELL_NORMAL) {
      if (FALSE === ($this->handle= pspell_new($language, $spelling, $jargon, $encoding, $mode))) {
        throw new IllegalArgumentException('Could not create spell checker');
      }
    }
    
    /**
     * Checks a single word
     *
     * @param   string word
     * @return  bool
     */
    public function check($word) {
      return pspell_check($this->handle, $word);
    }

    /**
     * Retrieve suggestions for a given word.
     *
     * @param   string word
     * @return  string[]
     */
    public function suggestionsFor($word) {
      return pspell_suggest($this->handle, $word);
    }
  }
?>
