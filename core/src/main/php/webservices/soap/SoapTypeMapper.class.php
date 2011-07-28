<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses(
    'lang.Type',
    'lang.types.String',
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
        'Character' => TRUE
      );

    public function supports($object) {
      foreach ($this->handler as $handler => $t) {
        if ($object instanceof $handler) return TRUE;
      }

      return FALSE;
    }


    public function box($object) {
      foreach ($this->handler as $handler => $t) {
        if (!$object instanceof $handler) continue;

        return call_user_func(array($this, 'box'.$handler), $object);
      }

      throw new IllegalArgumentException('Type '.xp::typeOf($object).' is not supported.');
    }

    protected abstract function boxParameter($object);
    protected abstract function boxSoapType($object);
    protected abstract function boxString($object);
    protected abstract function boxLong($object);
    protected abstract function boxInteger($object);
    protected abstract function boxShort($object);
    protected abstract function boxDouble($object);
    protected abstract function boxBoolean($object);
    protected abstract function boxBytes($object);
    protected abstract function boxCharacter($object);
  }
?>
