<?php
/* This class is part of the XP framework
 *
 * $Id: MarkupBuilder.class.php,v 1.22 2005/03/15 18:02:06 friebe Exp $ 
 */

  namespace de::uska::markup;

  ::uses('text.StringTokenizer');

  /**
   * Markup builder based on regular expressions
   *
   * @purpose  Plain text to markup converter
   */
  class MarkupBuilder extends lang::Object {
    public
      $patterns= array(
        '#&(?![a-z0-9\#]+;)#',
        '#(^| )_([^_]+)_([ \.,]|$)#', 
        '#(^| )\*([^*]+)\*([ \.,]|$)#',
        '#(^| )/([^/]+)/([ \.,]|$)#',
        '#(https?://[^\)\s\t\r\n]+)#',
        '#mailto:([^@]+@.+\.[a-z]{2,8})#',
        '#(_|=|-){10,}#'
      ),
      $replacements= array(
        '&amp;',
        '$1<u>$2</u>$3', 
        '$1<b>$2</b>$3',
        '$1<i>$2</i>$3',
        '<link href="$1"/>',
        '<mailto recipient="$1"/>',
        '<hr/>'
      );

    /**
     * Retrieve markup for specified text
     *
     * @param   string text
     * @return  string
     */
    public function markupFor($text) {
      static $nl2br= array("\r" => '', "\n" => "<br/>\n");

      $patterns= $this->patterns;
      $replacements= $this->replacements;

      $st= new text::StringTokenizer($text, '<>', $returnDelims= TRUE);
      $out= '';
      $translation= $nl2br;
      while ($st->hasMoreTokens()) {
        if ('<' == ($token= $st->nextToken())) {
          
          // Found beginning of tag
          $tag= $st->nextToken('>');
          switch (strtolower($tag)) {
            case 'pre':
              $translation= array();
              $patterns= array('#[\s\t]+\n#', '#&(?![a-z0-9\#]+;)#');
              $replacements= array("\n", '&amp;');
              break;

            case '/pre':
              $translation= $nl2br;
              $patterns= $this->patterns;
              $replacements= $this->replacements;
              break;
          }

          $out.= '<'.$tag;
          continue;
        }
        $out.= strtr(preg_replace(
          $patterns, 
          $replacements, 
          $token
        ), $translation);
      }

      return $out;
    }
  }
?>
