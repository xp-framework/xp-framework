<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Serializable', 'SerializationException');

  /**
   * Serializer
   *
   * @purpose  Abstract base class
   */
  abstract class Serializer extends Object {
  
    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   mixed var
     * @return  string
     * @throws  SerializationException
     */  
    protected function representationOf($var) {
      switch (gettype($var)) {
        case 'NULL':    return 'N;';
        case 'boolean': return 'b:'.$var.';';
        case 'integer': return 'i:'.$var.';';
        case 'float':   return 'd:'.$var.';';
        case 'string':  return 's:'.strlen($var).':"'.$var.'";';
        case 'array':
          $s= 'a:'.sizeof($var).':{';
          foreach (array_keys($var) as $key) {
            $s.= $this->representationOf($key).$this->representationOf($var[$key]);
          }
          return $s.'}';

        case 'object':
          if (!$var instanceof Serializable) {
            throw new SerializationException(
              xp::typeOf($var).' does not implement the Serializable interface'
            );
          }
          $name= xp::typeOf($var);
          for (
            $r= new Reflection_Class($var),
            $props= $r->getProperties(),
            $size= sizeof($props),
            $s.= 'O:'.strlen($name).':"'.$name.'":'.$size.':{',
            $i= 0;
            $i < $size;
            $i++
          ) {
            switch ($props[$i]->getModifiers() & (P_PUBLIC | P_PROTECTED | P_PRIVATE)) {
              case P_PUBLIC:    $ref= $props[$i]->name; break;
              case P_PROTECTED: $ref= "\0*\0{$props[$i]->name}"; break;
              case P_PRIVATE:   $ref= "\0{$props[$i]->class}\0{$props[$i]->name}"; break;
            }
            $s.= $this->representationOf($ref).$this->representationOf($var->{$ref});
          }
          unset($r);
          return $s.'}';

        case 'resource': return ''; // Ignore (resources can't be serialized)
        default: throw new SerializationException(
          'Cannot serialize unknown type '.xp::typeOf($var)
        );
      }
    }
  
    /**
     * Serialize an object
     *
     * @access  public
     * @param   &io.Serializable object
     */
    abstract public function serialize(Serializable $object);
  }
?>
