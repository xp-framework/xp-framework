<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.types.String',
    'lang.types.Integer',
    'lang.types.Double',
    'lang.types.Boolean',
    'lang.types.ArrayList'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Type extends Object {
  
    /**
     * (Insert method's description here)
     *
     * @param   mixed in
     * @return  lang.Generic
     */
    public static function boxed($in) {
      if ($in instanceof Generic) return $in; else switch (gettype($in)) {
        case 'string': return new String($in);
        case 'integer': return new Integer($in);
        case 'double': return new Double($in);
        case 'boolean': return new Boolean($in);
        case 'array': return ArrayList::newInstance($in);
        default: throw new IllegalArgumentException('Cannot box '.xp::typeOf($in));
      }
    }
  }
?>
