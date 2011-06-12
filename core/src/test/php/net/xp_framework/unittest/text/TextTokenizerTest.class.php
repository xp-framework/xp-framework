<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.text.AbstractTokenizerTest',
    'io.streams.TextReader',
    'io.streams.MemoryInputStream',
    'text.TextTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.TextTokenizer
   * @purpose  TestCase
   */
  class TextTokenizerTest extends AbstractTokenizerTest {
  
    /**
     * Retrieve a tokenizer instance
     *
     * @param   string source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     * @return  text.Tokenizer
     */
    protected function tokenizerInstance($source, $delimiters= ' ', $returnDelims= FALSE) {
      return new TextTokenizer(new TextReader(new MemoryInputStream($source)), $delimiters, $returnDelims);
    }
  }
?>
