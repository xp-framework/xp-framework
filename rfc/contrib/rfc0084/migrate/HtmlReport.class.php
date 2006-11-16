<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Report');

  /**
   * Report implementation which will output HTML
   *
   * @purpose  Report implementation
   */
  class HtmlReport extends Report {

    /**
     * Summarize this report
     *
     * @access  public
     * @param   &io.collections.IOCollection collection
     * @param   &io.File out
     * @param   array<string, &Rule> rules
     */
    function summarize(&$collection, &$out, $rules) {
      $css= '
        body {
          font: 10pt Arial, helvetica;
        }
        pre, code {
          font: 10pt "Courier new", courier;
          margin: 0;
        }
        code {
          background-color: #efefef;
          white-space: pre;
        }
        pre {
          margin-bottom: 8px;
        }
        fieldset {
          margin-bottom: 10px;
          border: 1px solid #cccccc;
        }
        fieldset legend {
          font-weight: bold;
        }
        li {
          font-weight: bold;
        }
        .renamedrule {
          color: #000099;
        }
        .movedrule {
          color: #ffa800;
        }
        .deprecatedrule {
          color: #990000;
        }
      ';

      $out->open(FILE_MODE_WRITE);
      $out->write('<html><head><title>RFC #0084 Migration report</title><style type="text/css">'.$css.'</style></head><body>');
      
      // Executive summary
      $out->write('<fieldset><legend>Executive summary</legend><ul>');
      foreach ($this->packages as $package => $count) {
        $out->write(sprintf(
          '<li><span class="%s">Uses of package %s: %d</span><br/>This package has been %s</li>',
          get_class($rules[$package]),
          $package,
          $count,
          $rules[$package]->toString()
        ));
      }
      $out->write('</ul></fieldset><hr/>');
      
      // Details
      foreach ($this->messages as $uri => $messages) {
        $out->write('<fieldset><legend>Report for '.str_replace($collection->getURI(), '', $uri)."</legend><ul>");
        foreach ($messages as $package => $occurrences) {
          $out->write(sprintf(
            "<li><span class='%s'>Use of package %s</span><br/>\n",
            get_class($rules[$package]),
            $package
          ));

          foreach ($occurrences as $occurrence) {
            $out->write(sprintf(
              "<code>%s</code>\n<pre>%s^-- line %d, offset %d</pre>\n",
              $occurrence[2],
              str_repeat(' ', $occurrence[1]),
              $occurrence[0],
              $occurrence[1]
            ));
          }

          $out->write("</li>\n");
        }
        $out->write("</ul></fieldset>\n");
      }
      $out->write('</body>');
      $out->close();
    }
    
    /**
     * Creates a string representation of this report's type
     *
     * @access  public
     * @return  string
     */
    function getType() {
      return 'HTML';
    }
  }
?>
