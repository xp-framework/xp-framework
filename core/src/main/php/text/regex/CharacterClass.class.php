<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.regex.Scanner');

  /**
   * POSIX character class
   *
   * @see      php://ctype
   * @see      http://en.wikipedia.org/wiki/Regular_expression
   * @test     xp://net.xp_framework.unittest.text.CharacterClassTest
   */
  class CharacterClass extends Object {
    public static 
      $ALNUM, 
      $WORD, 
      $ALPHA, 
      $BLANK,
      $CNTRL,
      $DIGIT, 
      $GRAPH, 
      $LOWER,
      $PRINT,
      $PUNCT,
      $SPACE,
      $UPPER, 
      $XDIGIT;

    protected $matcher = NULL;
    
    static function __static() {
      self::$ALNUM= new self(new Scanner('%[a-zA-Z0-9]'));
      self::$WORD= new self(new Scanner('%[a-zA-Z0-9_]'));
      self::$ALPHA= new self(new Scanner('%[a-zA-Z]'));
      self::$BLANK= new self(new Scanner("%[\t ]"));
      self::$CNTRL= new self(new Scanner("%[\x00-\x1F\x7F]"));
      self::$DIGIT= new self(new Scanner('%[0-9]'));
      self::$GRAPH= new self(new Scanner("%[\x21-\x7E]"));
      self::$LOWER= new self(new Scanner('%[a-z]'));
      self::$PRINT= new self(new Scanner("%[\x20-\x7E]"));
      self::$PUNCT= new self(new Scanner('%[]!"#$%&\'()*+,./:;<=>?@[^_`{|}~-]'));
      self::$SPACE= new self(new Scanner("%[ \t\r\n\x0B\x0C]"));     // \v and \f only defined in PHP >= 5.2.6
      self::$UPPER= new self(new Scanner('%[A-Z]'));
      self::$XDIGIT= new self(new Scanner('%[0-9a-fA-F]'));
    }
    
    /**
     * Creates a new character class
     *
     * @param   text.regex.Matcher
     */
    protected function __construct(Matcher $matcher) {
      $this->matcher= $matcher;
    }
    
    /**
     * Returns whether a given string matches this character class
     *
     * @param   string str
     * @return  bool
     */
    public function matches($str) {
      $r= $this->matcher->match($str);
      return 1 === $r->length() && $str === current($r->group(0));
    }
  }
?>
