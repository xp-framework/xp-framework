<?php
/* This file is part of the XP framework's experiments 
 *
 * $Id$
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses('xml.Tree');
  
  // {{{ void addAttribute(&xml.Node object, string name, mixed value [, string type])
  //     Adds a dia:attribute node
  function addAttribute(&$object, $name, $value, $type= NULL) {
    $node= &$object->addChild(new Node('dia:attribute', NULL, array(
      'name'    => $name
    )));
    if (!isset($type)) $type= xp::typeOf($value);
    switch ($type) {
      case 'string': 
        $node->addChild(new Node('dia:string', '#'.$value.'#')); 
        break;

      case 'boolean': 
        $node->addChild(new Node('dia:boolean', NULL, array(
          'val' => $value ? 'true' : 'false'
        )));
        break;

      case 'int':
        $node->addChild(new Node('dia:int', NULL, array(
          'val' => $value
        )));
        break;

      case 'float':
        $node->addChild(new Node('dia:real', NULL, array(
          'val' => $value
        )));
        break;
      
      case 'enum':
        $node->addChild(new Node('dia:enum', NULL, array(
          'val' => $value
        )));
        break;

      case 'point':
        $node->addChild(new Node('dia:point', NULL, array(
          'val' => implode(',', $value)
        )));
        break;

      default:
        return throw(new IllegalArgumentException('Unknown type "'.$type.'"'));
    }
  }
  // }}}
  
  // {{{ main
  $p= &new ParamString();
  try(); {
    $class= &XPClass::forName($p->value(1));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  with ($dia= &new Tree('dia:diagram')); {
    $dia->root->setAttribute('xmlns:dia', 'http://www.lysator.liu.se/~alla/dia');
    $bg= &$dia->addChild(new Node('dia:layer', NULL, array(
      'name'    => 'Background',
      'visible' => 'true'
    )));
    $object= &$bg->addChild(new Node('dia:object', NULL, array(
      'type'    => 'UML - Class',
      'version' => 0,
      'id'      => 'O0'
    )));
    addAttribute($object, 'obj_pos', array(1, 1), 'point');
    addAttribute($object, 'name', $class->getName());
    addAttribute($object, 'stereotype', '');
    addAttribute($object, 'comment', '');
    addAttribute($object, 'abstract', FALSE);
    addAttribute($object, 'suppress_attributes', FALSE);
    addAttribute($object, 'suppress_operations', FALSE);
    
    // Add class fields
    $fields= $class->getFields();
    $s= sizeof($fields);
    addAttribute($object, 'visible_attributes', $s > 0);
    $attributes= &$object->addChild(new Node('dia:attribute', NULL, array(
      'name' => 'attributes'
    )));
    $composites= array();
    foreach (array_keys($fields) as $name) {
      if (0 == strncmp('__', $name, 2)) continue;    // Exclude magic attributes

      $composite= &new Node('dia:composite', NULL, array(
        'type' => 'umlattribute'
      ));
      addAttribute($composite, 'name', $name);
      addAttribute($composite, 'type', xp::typeOf($fields[$name]));
      addAttribute($composite, 'value', '');
      addAttribute($composite, 'visibility', '_' == $name{0} ? 2 : 0, 'enum');
      addAttribute($composite, 'comment', '');
      addAttribute($composite, 'abstract', FALSE);
      addAttribute($composite, 'query', FALSE);
      addAttribute($composite, 'class_scope', FALSE);

      $composites[sprintf(
        '%d.%s',
        '_' == $name{0} ? 1 : 0,
        $name
      )]= &$composite;
    }
    ksort($composites);
    foreach (array_keys($composites) as $key) {
      $attributes->addChild($composites[$key]);
    }
    
    // Add class methods
    $methods= $class->getMethods();
    $s= sizeof($methods);
    addAttribute($object, 'visible_operations', $s > 0);
    $operations= &$object->addChild(new Node('dia:attribute', NULL, array(
      'name' => 'operations'
    )));
    $composites= array();
    for ($i= 0; $i < $s; $i++) {
      $name= $methods[$i]->getName();
      if (0 == strncmp('__', $name, 2)) continue;    // Exclude magic methods

      $inheritance= $class->equals($methods[$i]->getDeclaringClass()) ? 2 : 1;
      $modifiers= $methods[$i]->getModifiers();

      $composite= &new Node('dia:composite', NULL, array(
        'type' => 'umloperation'
      ));
      addAttribute($composite, 'name', $name);
      addAttribute($composite, 'stereotype', '');
      addAttribute($composite, 'type', $methods[$i]->getReturnType());
      addAttribute($composite, 'visibility', '_' == $name{0} ? 2 : 0, 'enum');
      addAttribute($composite, 'comment', utf8_encode(trim($methods[$i]->getComment())));
      addAttribute($composite, 'abstract', $modifiers & MODIFIER_ABSTRACT, 'boolean');
      addAttribute($composite, 'inheritance_type', $inheritance, 'enum');
      addAttribute($composite, 'query', FALSE);
      addAttribute($composite, 'class_scope', $modifiers & MODIFIER_STATIC, 'boolean');
      
      // Add method arguments
      $parameters= &$composite->addChild(new Node('dia:attribute', NULL, array(
        'name' => 'parameters'
      )));
      $arguments= $methods[$i]->getArguments();
      for ($j= 0, $t= sizeof($arguments); $j < $t; $j++) {
        $parameter= &$parameters->addChild(new Node('dia:composite', NULL, array(
          'type' => 'umlparameter'
        )));
        
        addAttribute($parameter, 'name', $arguments[$j]->getName());
        addAttribute($parameter, 'type', $arguments[$j]->getType());
        addAttribute($parameter, 'value', '');
        addAttribute($parameter, 'comment', '');
        addAttribute($parameter, 'kind', 0, 'enum');
      }
      
      $composites[sprintf(
        '%d.%d.%d.%s',
        $inheritance,
        $modifiers & MODIFIER_PUBLIC ? 0 : 1,
        $modifiers & MODIFIER_STATIC ? 0 : 1,
        $name
      )]= &$composite;
    }
    ksort($composites);
    foreach (array_keys($composites) as $key) {
      $operations->addChild($composites[$key]);
    }
  }
  
  Console::writeLine('<?xml version="1.0" encoding="UTF-8"?>');
  Console::writeLine($dia->getSource(INDENT_DEFAULT));
  // }}}
?>
