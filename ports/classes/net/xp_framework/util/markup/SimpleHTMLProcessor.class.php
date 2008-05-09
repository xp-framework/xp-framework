<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.util.markup.MarkupProcessor');

  /**
   * Processor for HTML tag generation of special commands
   *
   * @purpose  Processor
   */
  class SimpleHTMLProcessor extends MarkupProcessor {
    public
      $patterns= array(
        '#\r#',
        '#(- ){1}([\w\S ]*)(\n){1}#'
      ),
      $replacements= array(
        "",
        "<li>$2</li>"
      );
    

    /**
     * Process
     *
     * @param   string token
     * @return  string
     */
    public function process($token) {
      $token = ereg_replace(132, '"', $token); // left double quote
      $token = ereg_replace(145, '"', $token); // left single quote
      $token = ereg_replace(146, '"', $token); // right single quote
      $token = ereg_replace(147, '"', $token); // left double quote
      $token = ereg_replace(148, '"', $token); // right double quote
      $token = ereg_replace(150, "-", $token); // en dash
      $token = ereg_replace(151, "-", $token); // em dash
      
      $token= preg_replace($this->patterns, $this->replacements, $token); 
      $lines= explode("\n", $token);
      $html_lines= array();
      
      foreach ($lines AS $key => $line) {
        if (trim($line) != '') {
          if (substr($line, 0, 4) == '<li>')
            $html_lines[]= '<ul>'.$line.'</ul>';
          else
            $html_lines[]= '<p>'.$line.'</p>';
        }
      }
      
      return implode('', $html_lines);
    }
  }
?>
