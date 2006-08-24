<?php

  uses(
    'org.dia.DiaObject'
  );

  /**
   * Represents an UML Class shape of a DIAgramm
   *
   * Has annotations which allows automatic instantiation with a given $ClassDoc
   * (XPClass instance)
   *
   * @see   xp://org.dia.DiaDiagram
   * 
   *
   */
  class DiaUMLClass extends DiaObject {

    /**
     * Constructor with $type an $version
     *
     * @param   string type default 'UML - Class'
     * @param   int version default 0
     *
     */
    function __construct($type, $version) {
      parent::__construct($type, $version);

      // positioning elements default to 0
      $this->add(new DiaAttribute('obj_pos', array(0, 0), 'point'));
      $this->add(new DiaAttribute('obj_bb', array(array(0, 0), array(0, 0)), 'rectangle'));
      $this->add(new DiaAttribute('elem_corner', array(0, 0), 'point'));
      $this->add(new DiaAttribute('elem_width', '0.0', 'real'));
      $this->add(new DiaAttribute('elem_height', '0.0', 'real'));
      
      // defaults
      $this->add(new DiaAttribute('visible_attributes', TRUE, 'boolean'));
      $this->add(new DiaAttribute('visible_operations', TRUE, 'boolean'));
      $this->add(new DiaAttribute('visible_comments', FALSE, 'boolean'));
      $this->add(new DiaAttribute('suppress_attributes', FALSE, 'boolean'));
      $this->add(new DiaAttribute('suppress_operations', FALSE, 'boolean'));
    }
    
    /**
     * Evaluates $name= $ClassDoc->name()
     */
    #[@fromClass(type = 'string', eval = '$ClassDoc->qualifiedName()')]
    function setName($name) {
      $this->add(new DiaAttribute('name', $name, 'string')); 
    }

    /**
     * Evaluates $ClassDoc->classType() and sets stereotype accordingly
     */
    #[@fromClass(type = 'string', eval = '$ClassDoc->classType()')]
    function setStereotype($stereotype) {
      switch ($stereotype) {
        case ORDINARY_CLASS: 
          return; // no stereotype
        case EXCEPTION_CLASS:
          $type= 'Exception';
          break;
        case ERROR_CLASS:
          $type= 'Error';
          break;
        case INTERFACE_CLASS: 
          $type= 'Interface';
          break;
        default:
          return throw(new IllegalArgumentException("Unknown class type: '$type'!"));
      }
      $this->add(new DiaAttribute('stereotype', $type, 'string'));
    }

    /**
     * Evaluates '$ClassDoc->commentText()'
     *
     */
    #[@fromClass(type = 'string', eval = '$ClassDoc->commentText()')]
    function setComment($comment) {
      $this->add(new DiaAttribute('comment', $comment, 'string'));
    }

    /**
     * Evaluates 'in_array(\'abstract\', $ClassDoc->tags(\'model\'))'
     * ($ClassDoc->parseDetail('tags') && isset($ClassDoc->tags['model'][0]) && $ClassDoc->tags['model'][0]->text() === 'abstract'); 
     */
    #[@fromClass(type = 'bool', eval = '$ClassDoc->parseDetail(\'tags\') && isset($ClassDoc->tags[\'model\'][0]) && $ClassDoc->tags[\'model\'][0]->text() === \'abstract\'')]
    function setAbstract($abstract) {
      $this->add(new DiaAttribute('abstract', $abstract, 'boolean'));
    }

    /**
     * $field= array($name => $value)
     *
     * @param   array field
     */
    #[@fromClass(type = 'attribute')]
    function addAttribute($field) {
      // add element 'attributes' if it doesn't already exist
      $attributes= &$this->getChildAttributeByName('attributes');
      if (!isset($attributes)) {
        $attributes= &new DiaAttribute('attributes');
        $this->add($attributes);
      }

      list($name, $value)= each($field);
      if (isset($value)) {
        $type= xp::typeOf(eval("return $value;"));
      } else {
        $type= '';
        $value= 'NULL';
      }
      
      $comp= &new DiaComposite('umlattribute');
      $comp->add(new DiaAttribute('name', $name, 'string'));
      $comp->add(new DiaAttribute('type', $type, 'string'));
      $comp->add(new DiaAttribute('value', $value, 'string'));
      $comp->add(new DiaAttribute('comment', '', 'string'));
      if (0 == strncmp('_', $name, 1)) {
        $visibility= 2;
      } else {
        $visibility= 0;
      }
      $comp->add(new DiaAttribute('visibility', $visibility, 'enum'));
      $comp->add(new DiaAttribute('abstract', FALSE, 'boolean'));
      $comp->add(new DiaAttribute('class_scope', FALSE, 'boolean'));
      $attributes->add($comp);
    }

    /**
     * 
     * @param   text.doclet.MethodDoc
     */
    #[@fromClass(type = 'method')]
    function addMethod(&$method) {
      // add element 'operations' if it doesn't already exist
      $operations= &$this->getChildAttributeByName('operations');
      if (!isset($operations)) {
        $operations= &new DiaAttribute('operations');
        $this->add($operations);
      }

      // create method 'composite'
      $comp= &new DiaComposite('umloperation');
      $comp->add(new DiaAttribute('name', $method->name(), 'string'));

      $Tag_return= array_shift($method->tags('return'));
      if (isset($Tag_return)) {
        $text= $Tag_return->text();
        $type= xp::typeOf($text);
      } else {
        $type= 'void';
      }
      $comp->add(new DiaAttribute('type', $type, 'string'));
      if (0 == strncmp('_', $method->name(), 1)) {
        $visibility= 2;
      } else {
        $visibility= 0;
      }
      $comp->add(new DiaAttribute('visibility', $visibility, 'enum'));
      $comp->add(new DiaAttribute('comment', trim($method->commentText()), 'string'));

      $Tag_model= array_shift($methods->tags['model']);
      if (isset($Tag_model) and $Tag_model->text() === 'abstract') {
        $abstract= TRUE;
      } else {
        $abstract= FALSE;
      }
      $comp->add(new DiaAttribute('abstract', $abstract, 'boolean'));

      // default values:
      $comp->add(new DiaAttribute('stereotype', NULL, 'string'));
      $comp->add(new DiaAttribute('inheritance_type', 2, 'enum'));
      $comp->add(new DiaAttribute('query', FALSE, 'boolean'));
      $comp->add(new DiaAttribute('class_scope', FALSE, 'boolean'));

      // create parameters 'attribute'
      $params= &new DiaAttribute('parameters');

      // loop over arguments
      foreach (array_keys($method->arguments) as $name) {
        $value= $method->arguments[$name]; // always string!
        $type= NULL;
        if (isset($value)) {
          $evalue= eval("return $value;");
          if (isset($evalue)) $type= xp::typeOf($evalue);
        }

        // create parameter 'composite'
        $param= &new DiaComposite('umlparameter');
        $param->add(new DiaAttribute('name', $name, 'string'));
        $param->add(new DiaAttribute('type', $type, 'string'));
        $param->add(new DiaAttribute('value', $value, 'string'));
        $param->add(new DiaAttribute('comment', NULL, 'string'));
        $param->add(new DiaAttribute('kind', 0, 'enum'));
        $params->add($param);
      }

      // add parameter to method
      $comp->add($params);

      // add method to 'operations'
      $operations->add($comp);
    }

  }
?>
