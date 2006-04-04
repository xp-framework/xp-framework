<?php

/**
 * Stack manager for classes / and functions
 *
 *
 * 
 */
 
$GLOBALS['_BinaryPHP_Generator'] = array(
    'functions' => array(),
    'active'    => 0,
    'activeClass' => null,
    'classes' => array(),
);
 
class BinaryPHP_Generator
{
     
    
    /**
    * add a function to the stack
    *
    * @param   object  BinaryPHP_Generator_function 
    *
    * @return   int         id in stack
    * @access   public
    */
  
    function addFunction($func) {
        // we copy (not reference here)
        
        $id = count($GLOBALS['_BinaryPHP_Generator']['functions']);
        $func->id = $id;
        $GLOBALS['_BinaryPHP_Generator']['functions'][$id] = $func;
        return $id;
    }
    
    /**
    * retreive a function from the stack..
    *
    * @param   int    id in stack
    *
    * @return  object  BinaryPHP_Generator_function 
    * @access   public
    */
     
    function &getFunction($id=false) {
       
        if ($id === false) {
            return $GLOBALS['_BinaryPHP_Generator']['functions'][
               $GLOBALS['_BinaryPHP_Generator']['active']  
            ];
        }
        return $GLOBALS['_BinaryPHP_Generator']['functions'][$id];
    }
    /**
    * retreive a function from using the name
    *
    * @param   string name of function.
    *
    * @return   BinaryPHP_Generator_function | false  
    * @access   public
    */
     
    function &getFunctionByName($name) {
        foreach(array_keys($GLOBALS['_BinaryPHP_Generator']['functions']) as $i) {
            if ($GLOBALS['_BinaryPHP_Generator']['functions'][$i]->name == $name) {
                return $GLOBALS['_BinaryPHP_Generator']['functions'][$i];
            }
        }
        $f = false;
        return $f;
    
    }
    /**
    * set the active Function
    *
    * @param   int    id in stack
    *
    
    * @access   public
    */
     
    function  setActiveFunction($id=0) {
       
        $GLOBALS['_BinaryPHP_Generator']['active'] = $id;
    }
     /**
    * set the active class
    *
    * @param   object
    * @return int id of the class in the stack.
    * @access   public
    */
     
    function  addClass($class) 
    {
        $id = count($GLOBALS['_BinaryPHP_Generator']['classes']);
        $class->id = $id;
        $GLOBALS['_BinaryPHP_Generator']['classes'][$id] = $class;
        $GLOBALS['_BinaryPHP_Generator']['activeClass'] = $id;
        return $id;
    }
    
    function  &getActiveClass() 
    {
        $id = $GLOBALS['_BinaryPHP_Generator']['activeClass'];
        return $GLOBALS['_BinaryPHP_Generator']['classes'][$id];
    }
    
    
      /**
    * retreive number of functions on stack
    *
    *
    * @return  int   number in stack..
    * @access   public
    */
     
    function countFunctions() {
        return count($GLOBALS['_BinaryPHP_Generator']['functions']);
    }
    
    
}
 
