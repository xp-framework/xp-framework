<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.StringTokenizer');

  /**
   * Markup builder based on regular expressions
   *
   * @purpose  Plain text to markup converter
   */
  class MarkupBuilder extends Object {
    var
      $patterns= array(
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
      ),
      $replacements= array(
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
        '<emoticon id="wink_smile" text="$0"/>'
      );

    /**
     * Retrieve markup for specified text
     *
     * @access  public
     * @param   string text
     * @return  string
     */
    function markupFor($text) {
      static $nl2br= array("\r" => '', "\n" => '<br/>');

      $st= &new StringTokenizer($text, '<>', $returnDelims= TRUE);
      $out= '';
      $translation= $nl2br;
      while ($st->hasMoreTokens()) {
        if ('<' == ($token= $st->nextToken())) {
          
          // Found beginning of tag
          $tag= $st->nextToken('>');
          switch (strtolower($tag)) {
            case 'pre':
              $translation= array();
              break;

            case '/pre':
              $translation= $nl2br;
              break;
          }

          $out.= '<'.$tag;
          continue;
        }
        $out.= strtr(preg_replace(
          $this->patterns, 
          $this->replacements, 
          $token
        ), $translation);
      }

      return $out;
    }
  }
?>
