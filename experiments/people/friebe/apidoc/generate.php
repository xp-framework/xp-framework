<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.Folder',
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

    function start(&$root) {
      $build= &new Folder($root->option('build', 'build'));
      if (!$build->exists()) {
        $build->create();
      }
      
      while ($root->classes->hasNext()) {
        $classdoc= &$root->classes->next();
        Console::writeLine('Q: ', $classdoc->toString());
        
        FileUtil::setContents(
          new File($build->getURI().$classdoc->qualifiedName().'.classdoc'),
          serialize($classdoc)
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
