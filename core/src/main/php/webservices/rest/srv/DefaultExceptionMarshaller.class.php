<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.TypeMarshaller');

  /**
   * Default exception mapping
   *
   * <code>
   *   { "message" : "Exception message" }
   * </code>
   */
  class DefaultExceptionMarshaller extends Object implements TypeMarshaller {

    /**
     * Marshals the type
     *
     * @param  lang.Throwable type
     * @return var
     */
    public function marshal($t) {
      return array('message' => $t->getMessage());
    }

    /**
     * Unmarshals input
     *
     * @param  lang.Type target
     * @param  var in
     * @return lang.Throwable
     */
    public function unmarshal(Type $target, $in) {
      return $in instanceof Throwable ? $in : $target->newInstance((string)$in);
    }
  }
?>