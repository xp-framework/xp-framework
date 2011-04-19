<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Syntax base class
   *
   * @purpose  Abstract base class
   */
  abstract class Syntax extends Object {
    private static $syntaxes= array();
    protected $parser= NULL;
    
    static function __static() {
      foreach (Package::forName('xp.compiler.syntax')->getPackages() as $syntax) {
        self::$syntaxes[$syntax->getSimpleName()]= $syntax->loadClass('Syntax')->newInstance();
      }
    }
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->parser= $this->newParser();
    }
    
    /**
     * Retrieve a syntax for a given name
     *
     * @param   string syntax
     * @return  xp.compiler.syntax.Compiler
     */
    public static function forName($syntax) {
      if (!isset(self::$syntaxes[$syntax])) {
        throw new IllegalArgumentException('Syntax "'.$syntax.'" not supported');
      }
      return self::$syntaxes[$syntax];
    }

    /**
     * Retrieve a list of available syntaxes
     *
     * @return  array<string, xp.compiler.Syntax>
     */
    public static function available() {
      return self::$syntaxes;
    }
    
    /**
     * Parse
     *
     * @param   io.streams.InputStream in
     * @param   string source default NULL
     * @return  xp.compiler.ast.ParseTree tree
     */
    public function parse(InputStream $in, $source= NULL) {
      return $this->parser->parse($this->newLexer($in, $source ? $source : $in->toString()));
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->hashCode().')';
    }
    
    /**
     * Creates a parser instance
     *
     * @return  text.parser.generic.AbstractParser
     */
    protected abstract function newParser();

    /**
     * Creates a lexer instance
     *
     * @param   io.streams.InputStream in
     * @param   string source
     * @return  text.parser.generic.AbstractLexer
     */
    protected abstract function newLexer(InputStream $in, $source);
  }
?>
