<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Report');

  /**
   * Report implementation which will output text/plain
   *
   * @purpose  Report implementation
   */
  class TextReport extends Report {

    /**
     * Summarize this report
     *
     * @access  public
     * @param   &io.collections.IOCollection collection
     * @param   &io.File out
     * @param   array<string, &Rule> rules
     */
    function summarize(&$collection, &$out, $rules) {
      $out->open(FILE_MODE_WRITE);
      foreach ($this->messages as $uri => $messages) {
        $out->write('== Report for '.str_replace($collection->getURI(), '', $uri)." ==\n");
        foreach ($messages as $package => $occurrences) {
          $out->write(sprintf(
            "   Use of package %s - this package has been %s {\n",
            $package,
            $rules[$package]->toString()
          ));

          foreach ($occurrences as $occurrence) {
            $out->write(sprintf(
              "   %s\n   %s^-- line %d, offset %d\n",
              $occurrence[2],
              str_repeat(' ', $occurrence[1]),
              $occurrence[0],
              $occurrence[1]
            ));
          }

          $out->write("   }\n");
        }
        $out->write("\n");
      }
      $out->close();
    }
    
    /**
     * Creates a string representation of this report's type
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return 'textual';
    }
  }
?>
