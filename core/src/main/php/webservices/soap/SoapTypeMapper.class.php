<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses(
    'lang.Type',
    'lang.types.String',
    'lang.types.Short',
    'lang.types.Integer',
    'lang.types.Double',
    'lang.types.Boolean',
    'lang.types.ArrayList'
  );

  /**
   * Maps primitive or boxed primitives to soap types
   *
   */
  abstract class SoapTypeMapper extends Object {
    protected
      $handler= array(
        'Parameter' => TRUE,
        'SoapType'  => TRUE,
        'String'    => TRUE,
        'Long'      => TRUE,
        'Integer'   => TRUE,
        'Short'     => TRUE,
        'Double'    => TRUE,
        'Boolean'   => TRUE,
        'Bytes'     => TRUE,
        'Character' => TRUE,
        'Date'      => TRUE
      );

    /**
     * Check if type of object is supported
     *
     * @param   lang.Generic object
     * @return  boolean
     */
    public function supports($object) {
      foreach ($this->handler as $handler => $t) {
        if ($object instanceof $handler) return TRUE;
      }

      return FALSE;
    }

    /**
     * Box parameter into soap equivalent
     *
     * @param   lang.Generic object
     * @return  mixed
     * @throws  lang.IllegalArgumentException if type is not supported
     */
    public function box($object) {
      foreach ($this->handler as $handler => $t) {
        if (!$object instanceof $handler) continue;

        return call_user_func(array($this, 'box'.$handler), $object);
      }

      throw new IllegalArgumentException('Type '.xp::typeOf($object).' is not supported.');
    }

    /**
     * Box named parameter
     *
     * @param   webservices.soap.Parameter
     * @return  mixed
     */
    protected abstract function boxParameter($object);

    /**
     * Box SoapType
     *
     * @param   webservices.soap.types.SoapType object
     * @return  mixed
     */
    protected abstract function boxSoapType($object);

    /**
     * Box string
     *
     * @param   lang.types.String object
     * @return  mixed
     */
    protected abstract function boxString($object);

    /**
     * Box long
     *
     * @param   lang.types.Long object
     * @return  mixed
     */
    protected abstract function boxLong($object);

    /**
     * Box integer
     *
     * @param   lang.types.Integer object
     * @return  mixed
     */
    protected abstract function boxInteger($object);

    /**
     * Box short
     *
     * @param   lang.types.Short object
     * @return  mixed
     */
    protected abstract function boxShort($object);

    /**
     * Box double
     *
     * @param   lang.types.Double object
     * @return  mixed
     */
    protected abstract function boxDouble($object);

    /**
     * Box boolean
     *
     * @param   lang.types.Boolean object
     * @return  mixed
     */
    protected abstract function boxBoolean($object);

    /**
     * Box bytes
     *
     * @param   lang.types.Bytes object
     * @return  mixed
     */
    protected abstract function boxBytes($object);

    /**
     * Box character
     *
     * @param   lang.types.Character object
     * @return  mixed
     */
    protected abstract function boxCharacter($object);

    /**
     * Box date
     *
     * @param   util.Date object
     * @return  mixed
     */
    protected abstract function boxDate($object);
  }
?>
