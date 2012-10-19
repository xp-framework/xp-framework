<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.ClassCastException',
    'webservices.rest.server.routing.RestRoutingItem'
  );
  
  /**
   * Common data caster
   *
   * @test    xp://net.xp_framework.unittest.rest.server.RestDataCasterTest
   * @purpose Caster
   */
  class RestDataCaster extends Object {
    private
      $ignoreNullFields= TRUE;
    
    /**
     * Indicates whether fields that are set to NULL are ignored in simplification.
     * 
     * @return bool
     */
    public function getIgnoreNullFields() {
      return $this->ignoreNullFields;
    }
    
    /**
     * Sets whether to ignore fields that are set to NULL in simplification.
     * 
     * @param bool ignore 
     */
    public function setIgnoreNullFields($ignore) {
      $this->ignoreNullFields= $ignore;
    }
    
    /**
     * Simplify data structure by generating a simple array containing
     * the data
     * 
     * @param var[] data The data to simplify
     * @return var[]
     */
    public function simple($data) {
      switch (xp::typeOf($data)) {
        case 'NULL':
          return NULL;
        
        case 'integer':
        case 'string':
        case 'boolean':
        case 'double':
          return $data;
        
        case 'array':
          $result= array();
          foreach ($data as $key => $value) {
            $result[$key]= $this->simple($value);
          }
          return $result;

        case 'lang.types.Integer':
        case 'lang.types.String':
        case 'lang.types.Boolean':
          return Primitive::unboxed($data);
        
        case 'util.Hashmap':
          return $this->simple($data->toArray());
        
        case 'util.Date':
          return $data->toString();
        
        default:
          if ($data instanceof Generic) {
            $fields= array();
            $class= $data->getClass();
            foreach ($class->getFields() as $field) {

              $val= NULL;
              if ($field->getModifiers() & MODIFIER_PUBLIC) {
                $val = $this->simple($field->get($data));
              } else if ($class->hasMethod('get'.ucfirst($field->getName()))) {
                $val= $this->simple($class->getMethod('get'.ucfirst($field->getName()))->invoke($data));
              } else {
                continue;
              }

              if ($this->ignoreNullFields && $val === NULL) continue;

              $fields[$field->getName()]= $val;
            }
            
            return $fields;
          } else if (is_object($data)) {
            return $this->simple((array)$data);
          }
        
          throw new ClassCastException('Can not cast '.xp::typeOf($data).' to simple type');
      }
    }
    
    /**
     * Complexify data by creating an object
     * 
     * @param var[] data The data to simplify
     * @param lang.Type type The type to use
     * @return var[]
     */
    public function complex($data, Type $type) {
      $typeName= ($type instanceof ArrayType || $type instanceof MapType) ? xp::typeOf($type) : $type->getName();

      switch ($typeName) {
        case 'NULL':
          return NULL;

        case 'lang.ArrayType':
        case 'lang.MapType':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$typeName);
          }

          $result= array();
          foreach ($data as $key => $value) {
            $result[$key]= $this->complex($value, Type::forName($type->componentType()->getName()));
          }
          return $result;
        
        case 'lang.types.Integer':
          if (!is_scalar($data) || ((string)$data != (string)(int)$data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$typeName);
          }
          
        case 'lang.types.String':
        case 'lang.types.Boolean':
          if (!is_scalar($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$typeName);
          }
          return Primitive::unboxed($type->newInstance($data));
          
        case 'lang.types.ArrayList':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to array');
          }
          
          $result= array();
          foreach ($data as $key => $value) {
            $result[$key]= $this->complex($value, XPClass::forName('lang.types.String'));
          }
          return $result;
        
        case 'util.Hashmap':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to hash map');
          }
          
          return new Hashmap($data);

        case 'util.Date':
          if (!is_string($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to util.Date');
          }
          
          return Date::fromString($data);
        
        case 'php.stdClass':
          if (!is_array($data)) {
            throw new ClassCastException('Can not convert '.xp::typeOf($data).' to stdClass');
          }
          
          $result= new stdClass();
          foreach ($data as $key => $value) {
            $result->$key= $this->complex($value, XPClass::forName('lang.types.String'));
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
                  if($this->ignoreNullFields) {
                    continue;
                  }
                  throw new ClassCastException('Field '.$field->getName().' missing for '.$type->getName());
                }
                
                $field->set($result, $this->complex(
                  $data[$field->getName()],
                  Type::forName($field->getType() ? $field->getType() : 'lang.types.String')
                ));
                
              } else if ($type->hasMethod('set'.ucfirst($field->getName()))) {
                if (!isset($data[$field->getName()])) {
                  if($this->ignoreNullFields) {
                    continue;
                  }
                  throw new ClassCastException('Field '.$field->getName().' missing for '.$type->getName());
                }
                
                $type->getMethod('set'.ucfirst($field->getName()))->invoke($result, array($this->complex(
                  $data[$field->getName()],
                  Type::forName($field->getType() ? $field->getType() : 'lang.types.String')
                )));
              }
            }
            return $result;
            
          } else if ($type instanceof Primitive) {
            return $this->complex($data, $type->wrapperClass());
          }
        
          throw new ClassCastException('Can not convert '.xp::typeOf($data).' to '.$typeName);
      }
    }
  }
?>
