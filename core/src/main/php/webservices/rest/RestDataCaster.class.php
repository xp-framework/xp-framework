<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.routing.RestRoutingItem'
  );
  
  /**
   * Common data caster
   *
   * @test    xp://net.xp_framework.unittest.rest.RestDataCasterTest
   * @purpose Caster
   */
  class RestDataCaster extends Object {
    
    /**
     * Simplify data structure by generating a simple array containing
     * the data
     * 
     * @param mixed[] data The data to simplify
     * @return mixed[]
     */
    public static function simple($data) {
      switch (xp::typeOf($data)) {
        case 'NULL':
          return NULL;
        
        case 'integer':
        case 'string':
        case 'boolean':
          return $data;
        
        case 'array':
          $result= array();
          foreach ($data as $key => $value) {
            $result[$key]= self::simple($value);
          }
          return $result;
        
        case 'util.Hashmap':
          return self::simple($data->toArray());
        
        default:
          if ($data instanceof Object) {
            $fields= array();
            foreach ($data->getClass()->getFields() as $field) {
              if ($field->getModifiers() & MODIFIER_PUBLIC) {
                $fields[$field->getName()]= self::simple($field->get($data));
                
              } else if ($data->getClass()->hasMethod('get'.ucfirst($field->getName()))) {
                $fields[$field->getName()]= self::simple($data->getClass()->getMethod('get'.ucfirst($field->getName()))->invoke($data));
              }
            }
            return $fields;
          
          } else if (is_object($data)) {
            return self::simple((array)$data);
          }
        
          throw new IllegalStateException('Can not cast '.xp::typeOf($data).' to simple type');
      }
    }
    
    /**
     * Cast data to given type
     * 
     * @param mixed[] data The data to cast
     * @param lang.Type type The target type
     */
    public static function cast($data, $type) {
      return self::simplify($data);
    }
  }
?>
