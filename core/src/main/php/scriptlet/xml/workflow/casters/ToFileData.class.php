<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.casters.ParamCaster',
    'scriptlet.xml.workflow.FileData'
  );
  
  /**
   * Casts given values to a FileData object
   *
   * @purpose  Caster
   */
  class ToFileData extends ParamCaster {
  
    /**
     * Cast a given value
     *
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      $files= array();
      if (is_array($value['name'])) {
        // multiple files
        foreach ($value['name'] as $i => $name) {
          $files[]= new FileData(
            $name,
            $value['type'][$i],
            $value['size'][$i],
            $value['tmp_name'][$i]
          );
        }
      } else {
        // single file
        $files[]= new FileData(
          $value['name'],
          $value['type'],
          $value['size'],
          $value['tmp_name']
        );
      }
      return $files;
    }
  }
?>
