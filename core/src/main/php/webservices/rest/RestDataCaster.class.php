<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.ClassCastException',
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
        
          throw new ClassCastException('Can not cast '.xp::typeOf($data).' to simple type');
      }
    }
    
    /**
     * Complexify data by creating an object
     * 
     * @param mixed[] data The data to simplify
     * @param lang.Type type The type to use
     * @return mixed[]
     */
    public static function complex($data, Type $type) {
      switch ($type->getName()) {
        case 'NULL':
          return NULL;
        
        case 'lang.types.Integer':
          if (!is_scalar($data) || ((string)$data != (string)(int)$data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$type->getName());
          }
          
        case 'lang.types.String':
        case 'lang.types.Boolean':
          if (!is_scalar($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$type->getName());
          }
          return Primitive::unboxed($type->newInstance($data));
          
        case 'lang.types.ArrayList':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to array');
          }
          
          $result= array();
          foreach ($data as $key => $value) {
            $result[$key]= self::complex($value, XPClass::forName('lang.types.String'));
          }
          return $data;
        
        case 'util.Hashmap':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to hash map');
          }
          
          return new Hashmap($data);
        
        case 'php.stdClass':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to stdClass');
          }
          
          $result= new stdClass();
          foreach ($data as $key => $value) {
            $result->$key= self::complex($value, XPClass::forName('lang.types.String'));
          }
          return $result;
          
        default:
          if ($type instanceof XPClass) {
            if (!is_array($data)) {
              throw new ClassCastException('Can not convert '.xp::typeOf($data).' to lang.Object');
            }
            
            $result= $type->newInstance();
            foreach ($type->getFields() as $field) {
              if ($field->getModifiers() & MODIFIER_PUBLIC) {
                if (!isset($data[$field->getName()])) {
                  throw new ClassCastException('Field '.$field->getName().' missing for '.$type->getName());
                }
                
                $field->set($result, self::complex(
                  $data[$field->getName()],
                  XPClass::forName($field->getType() ? $field->getType() : 'lang.types.String')
                ));
                
              } else if ($type->hasMethod('set'.ucfirst($field->getName()))) {
                if (!isset($data[$field->getName()])) {
                  throw new ClassCastException('Field '.$field->getName().' missing for '.$type->getName());
                }
                
                $type->getMethod('set'.ucfirst($field->getName()))->invoke($result, array(self::complex(
                  $data[$field->getName()],
                  XPClass::forName($field->getType() ? $field->getType() : 'lang.types.String')
                )));
              }
            }
            return $result;
            
          } else if ($type instanceof Primitive) {
            return self::complex($data, $type->wrapperClass());
          }
        
          throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$type->getName());
      }
    }
  }
?>
