<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'io.streams.OutputStream',
    'io.streams.TextWriter',
    'util.PropertyAccess'
  );

  /**
   * Write util.PropertyAccess objects to a stream
   *
   * @test  xp://net.xp_framework.unittest.util.PropertiesStreamWriterTest
   */
  class PropertiesStreamWriter extends Object {
    protected $charset = xp::ENCODING;

    /**
     * Constructor.
     *
     * @param string charset
     */
    public function __construct($charset= NULL) {
      if ($charset) {
        $this->charset= $charset;
      }
    }

    /**
     * Write to stream
     *
     * @param  util.PropertyAccess prop
     * @param  io.streams.OutputStream out
     */
    public function write(PropertyAccess $prop, OutputStream $out) {
      $writer= new TextWriter($out, $this->charset);

      $section= $prop->getFirstSection();
      do {

        // Skip for empty properties
        if (!$section) continue;

        $writer->writeLine(sprintf("[%s]", $section));
        
        foreach ($prop->readSection($section) as $key => $val) {
          if (';' == $key{0}) {
            $writer->writeLine();
            $writer->writeLine(sprintf("; %s", $val));
          } else {
            if ($val instanceof Hashmap) {
              $str= '';
              foreach ($val->keys() as $k) {
                $str.= '|'.$k.':'.$val->get($k);
              }
              $val= (string)substr($str, 1);
            } 
            if (is_array($val)) $val= implode('|', $val);
            if (is_string($val)) $val= '"'.$val.'"';
            $writer->writeLine(sprintf(
              "%s=%s",
              $key,
              strval($val)
            ));
          }
        }
        $writer->writeLine();
      } while ($section= $prop->getNextSection());
    }
  }