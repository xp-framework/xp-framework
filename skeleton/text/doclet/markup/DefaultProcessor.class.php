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
        '#\r#',
        '#&(?![a-z0-9\#]+;)#',
        '#(^|\n)\* ([^\n]+(\n  [^\n]+)*)#',
        '#(^|\n)[0-9]+\) ([^\n]+(\n  [^\n]+)*)#',
        '#(?<!</lu>)<lu>#',
        '#</lu>(?!<lu>)(\n|$)#',
        '#(?<!</lo>)<lo>#',
        '#</lo>(?!<lo>)(\n|$)#',
        '#<(/?)(lu|lo)>#',
        '#&lt;(/?(ul|li|ol|tt|em))&gt;#',
        '#(^| )_([^_ ]+)_([ \.,]|$)#', 
        '#(^| )\*([^* ]+)\*([ \.,]|$)#',
        '#(^| )/([^/ ]+)/([ \.,]|$)#',
        '#\[\[(([a-z]+?)://)?([^ \]]+)( ([^\]]+))?\]\]#',
        '#\[(([a-z]+)://([^ ]+)) ([^\]]+)]#',
        '#((https?)://([^<>\)\]\s\t\r\n]+))#',
        '#mailto:([^@]+@[^.]+\.[a-z]{2,8})#',
        '#bug \#([0-9]+)#i',
        '#rfc \#([0-9]+)#i',
        '#entry \#([0-9]+)#i',
        '#category \#([0-9]+)#i',
        '#:\-?\)#',
        '#:\-?\(#',
        '#;\-?\)#',
        "#([^\n]+)\n[=]{3,}#",
        "#([^\n]+)\n[-]{3,}#",
        "#([^\n]+)\n[~]{3,}#",
        '#\n\n#',
      ),
      $replacements= array(
        '&lt;',
        '&gt;',
        '',
        '&amp;', 
        '<lu>$2</lu>',
        '<lo>$2</lo>',
        '<ul><li>',
        '</li></ul>',
        '<ol><li>',
        '</li></ol>',
        '<$1li>',
        '<$1>',
        '$1<u>$2</u>$3', 
        '$1<b>$2</b>$3',
        '$1<i>$2</i>$3',
        '<image src="$3" rel="$2" format="$5"/>',
        '<link href="$3" rel="$2">$4</link>',
        '<link href="$3" rel="$2"/>',
        '<mailto recipient="$1"/>',
        '<bug id="$1"/>',
        '<rfc id="$1"/>',
        '<blogentry id="$1"/>',
        '<blogcategory id="$1"/>',
        '<emoticon id="regular_smile" text="$0"/>',
        '<emoticon id="sad_smile" text="$0"/>',
        '<emoticon id="wink_smile" text="$0"/>',
        '</p><h1>$1</h1><p>',
        '</p><h2>$1</h2><p>',
        '</p><h3>$1</h3><p>',
        "<br/><br/>\n",
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
