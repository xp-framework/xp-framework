<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.Tokenizer', 'io.streams.Reader');
 
  /**
   * A stream tokenizer is a tokenizer that works on streams.
   * 
   * Example:
   * <code>
   *   $st= new TextTokenizer(new TextReader(new FileInputStream(new File('test.txt')), 'utf-8'), " \n");
   *   while ($st->hasMoreTokens()) {
   *     printf("- %s\n", $st->nextToken());
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.text.TextTokenizerTest
   * @see      xp://text.Tokenizer
   * @purpose  Tokenizer implementation
   */
  class TextTokenizer extends Tokenizer {
    protected
      $_stack = array(),
      $_buf   = '';

    /**
     * Reset this tokenizer
     *
     */
    public function reset() {
      if ('' !== $this->_buf) {
        try {
          $this->source->reset();
        } catch (IOException $e) {
          throw new IllegalStateException('Cannot reset', $e);
        }
      } 

      $this->_stack= array();
      $this->_buf= '';
    }
    
    /**
     * Tests if there are more tokens available
     *
     * @return  bool more tokens
     */
    public function hasMoreTokens() {
      return !(empty($this->_stack) && FALSE === $this->_buf);
    }
    
    /**
     * Push back a string
     *
     * @param   string str
     */
    public function pushBack($str) {
      $this->_buf= $str.implode('', $this->_stack).$this->_buf;
      $this->_stack= array();
    }
    
    /**
     * Returns the next token from this tokenizer's string
     *
     * @param   bool delimiters default NULL
     * @return  string next token
     */
    public function nextToken($delimiters= NULL) {
      if (empty($this->_stack)) {
      
        // Read until we have either find a delimiter or until we have 
        // consumed the entire content.
        do {
          $offset= strcspn($this->_buf, $delimiters ? $delimiters : $this->delimiters);
          if ($offset < strlen($this->_buf)- 1) break;
          if (NULL === ($buf= $this->source->read())) {
            break;
          }
          $this->_buf.= $buf;
        } while (TRUE);

        if (!$this->returnDelims || $offset > 0) $this->_stack[]= substr($this->_buf, 0, $offset);
        if ($this->returnDelims && $offset < strlen($this->_buf)) {
          $this->_stack[]= $this->_buf{$offset};
        }
        $this->_buf= substr($this->_buf, $offset+ 1);
      }
      
      return array_shift($this->_stack);
    }
  }
?>
