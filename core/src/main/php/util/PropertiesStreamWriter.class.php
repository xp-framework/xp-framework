<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'io.streams.OutputStream',
    'util.PropertyAccess'
  );

  /**
   * Write util.PropertyAccess objects to a stream
   *
   */
  class PropertiesStreamWriter extends Object {

    /**
     * Write to stream
     *
     * @param  util.PropertyAccess prop
     * @param  io.streams.OutputStream out
     */
    public function write(PropertyAccess $prop, OutputStream $out) {
      $section= $prop->getFirstSection();

      do {
        $out->write(sprintf("[%s]\n", $section));
        
        foreach ($prop->readSection($section) as $key => $val) {
          if (';' == $key{0}) {
            $out->write(sprintf("\n; %s\n", $val)); 
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
            $out->write(sprintf(
              "%s=%s\n",
              $key,
              strval($val)
            ));
          }
        }
        $out->write("\n");
      } while ($section= $prop->getNextSection());
    }
  }