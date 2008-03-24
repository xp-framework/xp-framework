<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.markup.MarkupProcessor');

  /**
   * Default processor for text not contained within any special
   * tags.
   *
   * @purpose  Processor
   */
  class DefaultProcessor extends MarkupProcessor {
    public
      $patterns= array(
        '#<#',
        '#>#',
        '#&lt;(/?(ul|li|ol|tt))&gt;#',
        '#\r#',
        '#\n\n#',
        '#&(?![a-z0-9\#]+;)#',
        '#(^| )_([^_]+)_([ \.,]|$)#', 
        '#(^| )\*([^*]+)\*([ \.,]|$)#',
        '#(^| )/([^/]+)/([ \.,]|$)#',
        '#(https?://[^\)\s\t\r\n]+)#',
        '#mailto:([^@]+@[^.]+\.[a-z]{2,8})#',
        '#bug \#([0-9]+)#i',
        '#entry \#([0-9]+)#i',
        '#category \#([0-9]+)#i',
        '#:\-?\)#',
        '#:\-?\(#',
        '#;\-?\)#',
        "#\n([^\n]+)\n[=]{3,}\n#",
        "#\n([^\n]+)\n[-]{3,}\n#",
        "#\n([^\n]+)\n[~]{3,}\n#",
      ),
      $replacements= array(
        '&lt;',
        '&gt;',
        '<$1>',
        '',
        "<br/><br/>\n",
        '&amp;', 
        '$1<u>$2</u>$3', 
        '$1<b>$2</b>$3',
        '$1<i>$2</i>$3',
        '<link href="$1"/>',
        '<mailto recipient="$1"/>',
        '<bug id="$1"/>',
        '<blogentry id="$1"/>',
        '<blogcategory id="$1"/>',
        '<emoticon id="regular_smile" text="$0"/>',
        '<emoticon id="sad_smile" text="$0"/>',
        '<emoticon id="wink_smile" text="$0"/>',
        '</p><h1>$1</h1><p>',
        '</p><h2>$1</h2><p>',
        '</p><h3>$1</h3><p>',
      );

    /**
     * Process
     *
     * @param   string token
     * @return  string
     */
    public function process($token) {
      return preg_replace($this->patterns, $this->replacements, $token);
    }
  }
?>
