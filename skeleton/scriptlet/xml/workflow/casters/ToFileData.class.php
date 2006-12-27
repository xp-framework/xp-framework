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
      return array(new FileData(
        $value['name'],
        $value['type'],
        $value['size'],
        $value['tmp_name']
      ));
    }
  }
?>
