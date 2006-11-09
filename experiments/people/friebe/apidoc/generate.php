<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'xml.Tree',
    'text.doclet.Doclet',
    'io.collections.FileCollection', 
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );
  
  // {{{ AllClassesIterator
  //     ClassIterator wrapper
  class AllClassesIterator extends Object {
    var
      $aggregate = NULL,
      $classpath = '',
      $root      = NULL,
      $stop      = FALSE;
  
    function __construct(&$aggregate, $classpath) {
      $this->aggregate= &$aggregate;
      $this->classpath= array_flip(array_map('realpath', explode(PATH_SEPARATOR, $classpath)));
    }
  
    function hasNext() {
      return $this->stop ? FALSE : $this->aggregate->hasNext();
    }
    
    function classNameForElement(&$element) {
      $uri= realpath($element->getURI());
      $path= dirname($uri);

      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($this->classpath[$path])) {
          return strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.'); 
        }

        $path= substr($path, 0, $pos); 
      }

      $this->stop= TRUE;
      throw(new IllegalArgumentException('Cannot infer classname from '.$element->toString()));
    }
    
    function &next() {
      try(); {
        $classname= $this->classNameForElement($this->aggregate->next());
      } if (catch('IllegalArgumentException', $e)) {
        return throw($e);
      }
      
      return $this->root->classNamed($classname);
    }
  }
  // }}}

  
  // {{{ GeneratorDoclet
  //     Specialized doclet
  class GeneratorDoclet extends Doclet {

    function tagAttribute($tags, $which, $attribute= 'text') {
      return isset($tags[$which]) ? $tags[$which]->{$attribute} : NULL;
    }
    
    function annotationNode($list) {
      $n= &new Node('annotations');
      foreach ($list as $annotation) {
        $a= &$n->addChild(new Node('annotation', NULL, array('name' => $annotation->name())));
        if (is_array($annotation->value)) {
          $a->addChild(Node::fromArray($annotation->value, 'value'));
        } else if (is_scalar($annotation->value)) {
          $a->addChild(new Node('value', $annotation->value, array('type' => gettype($annotation->value))));
        } else if (NULL === $annotation->value) {
          $a->addChild(new Node('value', NULL, array('type' => '')));
        } else {
          $a->addChild(new Node('value', xp::stringOf($annotation->value), array('type' => xp::typeOf($annotation->value))));
        }
      }
      return $n;
    }

    function classNode(&$classdoc) {
      $n= &new Node('class', NULL, array(
        'name'    => $classdoc->qualifiedName(),
        'type'    => $classdoc->classType()
      ));
      
      // Apidoc
      $n->addChild(new Node('comment', $classdoc->commentText()));
      $n->addChild(new Node('purpose', $this->tagAttribute($classdoc->tags('purpose'), 0, 'text')));
      foreach ($classdoc->tags('see') as $ref) {
        $n->addChild(new Node('see', NULL, array('href' => $ref->text)));
      }
      foreach ($classdoc->tags('test') as $ref) {
        $n->addChild(new Node('test', NULL, array('href' => $ref->text)));
      }

      // Annotations
      $n->addChild($this->annotationNode($classdoc->annotations()));

      // Superclass
      $extends= &$n->addChild(new Node('extends'));
      $classdoc->superclass && $extends->addChild($this->classNode($classdoc->superclass));

      // Interfaces
      $interfaces= &$n->addChild(new Node('implements'));
      for ($classdoc->interfaces->rewind(); $classdoc->interfaces->hasNext(); ) {
        $interfaces->addChild($this->classNode($classdoc->interfaces->next()));
      }

      // Fields
      $fields= &$n->addChild(new Node('fields'));
      foreach ($classdoc->fields as $name => $value) {
        $fields->addChild(new Node('field', $value, array(
          'name'  => $name
        )));
      }
      
      // Methods
      $methods= &$n->addChild(new Node('methods'));
      foreach ($classdoc->methods as $method) {
        $m= &$methods->addChild(new Node('method', NULL, array(
          'name'   => $method->name(),
          'access' => $this->tagAttribute($method->tags('access'), 0, 'text'),
          'return' => $this->tagAttribute($method->tags('return'), 0, 'type')
        )));
        
        // Apidoc
        $m->addChild(new Node('comment', $method->commentText()));
        foreach ($method->tags('see') as $ref) {
          $m->addChild(new Node('see', NULL, array('href' => $ref->text)));
        }

        // Annotations
        $m->addChild($this->annotationNode($method->annotations()));
        
        // Thrown exceptions
        foreach ($method->tags('throws') as $thrown) {
          $m->addChild(new Node('exception', $thrown->text, array(
            'class' => $thrown->exception->qualifiedName()
          )));
        }
        
        foreach ($method->arguments as $name => $default) {
          $m->addChild(new Node('argument', $default, array('name' => $name)));
        }
      }

      return $n;
    }

    function start(&$root) {
      $build= &new Folder($root->option('build', 'build'));
      if (!$build->exists()) {
        $build->create();
      }
      
      while ($root->classes->hasNext()) {
        $classdoc= &$root->classes->next();
        Console::writeLine('Q: ', $classdoc->toString());
        
        // Create XML tree
        $tree= &new Tree('doc');
        $tree->addChild($this->classNode($classdoc));
        
        // Write to file
        FileUtil::setContents(
          new File($build->getURI().$classdoc->qualifiedName().'.xml'),
          $tree->getDeclaration()."\n".$tree->getSource(INDENT_DEFAULT)
        );
      }
    }

    function &iteratorFor(&$root, $classes) {
      $scan= &new Folder($root->option('scan'));
      if (!$scan->exists()) {
        return throw(new IllegalArgumentException($scan->getURI().' does not exist!'));
      }

      $iterator= &new AllClassesIterator(
        new FilteredIOCollectionIterator(
          new FileCollection($scan->getURI()), 
          new ExtensionEqualsFilter('class.php'),
          TRUE
        ),
        ini_get('include_path')
      );
      $iterator->root= &$root;
      return $iterator;
    }
    
    
    function validOptions() {
      return array('scan' => HAS_VALUE);
    }
  }
  // }}}
  
  // {{{ main
  RootDoc::start(new GeneratorDoclet(), new ParamString());
  // }}}
?>
