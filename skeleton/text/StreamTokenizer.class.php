<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.Tokenizer');
 
  /**
   * A stream tokenizer is a tokenizer that works on streams.
   * 
   * Example:
   * <code>
   *   $st= new StreamTokenizer(new FileInputStream(new File('test.txt')), " \n");
   *   while ($st->hasMoreTokens()) {
   *     printf("- %s\n", $st->nextToken());
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.text.StreamTokenizerTest
   * @see      xp://text.Tokenizer
   * @purpose  Tokenizer implementation
   */
  class StreamTokenizer extends Tokenizer {
    protected
      $_stack = array(),
      $_buf   = '',
      $_src   = NULL,
      $_ofs   = 0,
      $_len   = 0;

    /**
     * Reset this tokenizer
     *
     */
    public function reset() {
      $this->_stack= array();
      
      if ($this->_src) {
        if ($this->_src instanceof Seekable) {
          $this->_src->seek(0, SEEK_SET);
        } else {
          throw new IllegalStateException(
            'Cannot reset, Source '.xp::stringOf($this->_src).' is not seekable'
          );
        }
      }
      $this->_src= $this->source;
      $this->_buf= $this->_src->read();
      $this->_ofs= 0;
      $this->_len= strlen($this->_buf);
    }
    
    /**
     * Tests if there are more tokens available
     *
     * @return  bool more tokens
     */
    public function hasMoreTokens() {
      return (!empty($this->_stack) || $this->_ofs < $this->_len);
    }
    
    /**
     * Push back a string
     *
     * @param   string str
     */
    public function pushBack($str) {
      $this->_buf= (
        substr($this->_buf, 0, $this->_ofs).
        $str.implode('', $this->_stack).
        substr($this->_buf, $this->_ofs)
      );
      $this->_len= strlen($this->_buf);
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
          $offset= strcspn($this->_buf, $delimiters ? $delimiters : $this->delimiters, $this->_ofs);
          if ($offset != $this->_len) break;
          $this->_buf.= $this->_src->read();
          $this->_len= strlen($this->_buf);
        } while ($this->_src->available());

        if (!$this->returnDelims || $offset > 0) $this->_stack[]= substr($this->_buf, $this->_ofs, $offset);
        if ($this->returnDelims && $this->_ofs + $offset < $this->_len) {
          $this->_stack[]= $this->_buf{$this->_ofs + $offset};
        }
        $this->_ofs+= $offset+ 1;
      }
      
      return array_shift($this->_stack);
    }
  }
?>
