<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'text.doclet.Doclet', 
    'io.File', 
    'io.Folder', 
    'io.FileUtil', 
    'net.xp_framework.tools.vm.util.NameMapping',
    'net.xp_framework.tools.vm.util.SourceRewriter'
  );
  
  $help= <<<__
Subjective: Migrate PHP4 classes and scripts using XP to PHP

* Replace try(); with try
* Replace if (catch('EXCEPTION_CLASS_NAME', \$e)) with 
  catch (EXCEPTION_CLASS_NAME \$e)
* Add correct namespace to new CLASS_NAME
* Add correct namespace to extends CLASS_NAME
* Add correct namespace to static CLASS_NAME::METHOD_NAME calls
* Add package statement around classes
* Rewrite implements(__FILE__, 'IMPLEMENTED_INTERFACE') to 
  implements IMPLEMENTED_INTERFACE
* Replace return throw() with throw()
* Replace class INTERFFACE_NAME extends Interface with
  interface INTERFFACE_NAME
* Remove method body from interface methods

Usage:
php migrate.php <<fully_qualified_class_name>>
__;

  define('NS_SEPARATOR',         '.');
  
  // {{{ MigrationNameMapping
  //     Same as NameMapping, but
  class MigrationNameMapping extends NameMapping {

    function getMapping($key) {
      try(); {
        $m= parent::getMapping($key);
      } if (catch('IllegalArgumentException', $e)) {
        // DEBUG $e->printStackTrace();
        Console::writeLine('*** ', $e->getMessage());
        return $key;
      }
      
      return $m;
    }
  }
  
  // {{{ MigrationDoclet
  //     Migrates classes
  class MigrationDoclet extends Doclet {
    var
      $mapping = NULL,
      $current = NULL;

    function buildMapping(&$doc) {
      $key= strtolower($doc->name());
      if (isset($this->mapping[$key])) return;
      
      $this->names->addMapping($key, $doc->qualifiedName());
      
      // Build mapping for superclass if existant
      $doc->superclass && $this->buildMapping($doc->superclass);
      
      // Build mapping for used classes
      while ($doc->usedClasses->hasNext()) {
        $this->buildMapping($doc->usedClasses->next());
      }

      // Build mapping for interfaces
      while ($doc->interfaces->hasNext()) {
        $this->buildMapping($doc->interfaces->next());
      }
    }
    
    function start(&$root) {
      $debug= $root->option('debug');
      $this->names= &new MigrationNameMapping();
      $this->names->setNamespaceSeparator(NS_SEPARATOR);

      $this->rewriter= &new SourceRewriter();
      $this->rewriter->setNameMapping($this->names);
      
      // Build mapping for built-in-classes
      Console::writeLine('===> Starting');
      foreach (xp::registry() as $key => $val) {
        if (0 != strncmp('class.', $key, 6)) continue;
        $this->names->addMapping(xp::reflect($key), trim(xp::registry($key), '<>'));
      }

      if ($output= $root->option('output')) {
        Console::writeLine('---> Writing to ', $output);
        $base= &new Folder($output);
      }
      
      while ($root->classes->hasNext()) {
        $this->current= &$root->classes->next();
        $debug && Console::writeLine('---> Processing ', $this->current->qualifiedName());
        
        // Build mapping short names => long names
        $this->buildMapping($this->current);
        $this->names->setCurrentClass($this->current);

        // Tokenize file
        $tokens= token_get_all(file_get_contents($root->findClass($this->current->qualifiedName())));
        try(); {
          $out= $this->rewriter->rewrite($tokens, $debug);
        } if (catch('Exception', $e)) {
          $e->printStackTrace();
          continue;
        }
        
        if ($output) {
          try(); {
            $target= &new File($base->getURI().strtr($this->names->packagedNameOf($this->current->qualifiedName()), NS_SEPARATOR, DIRECTORY_SEPARATOR).'.xp');
            $f= &new Folder($target->getPath());
            $f->exists() || $f->create();
            FileUtil::setContents($target, $out);
          } if (catch('IOException', $e)) {
            return throw($e);
          }
          Console::writeLine('---> Wrote ', $target->getURI());
        } else {
          Console::write($out);
        }
      }
    }
    
    function validOptions() {
      return array(
        'debug'   => OPTION_ONLY,
        'output'  => HAS_VALUE
      );
    }
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  if ($p->exists('help', '?')) {
    Console::writeLine($help);
    exit(1);
  }

  RootDoc::start(new MigrationDoclet(), $p);
  // }}}
?>
