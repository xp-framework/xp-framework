<?php
/* This class is part of the XP framework website
 *
 * $Id: collections.php 4787 2005-02-28 17:57:35Z friebe $ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.Properties', 
    'xml.Tree',
    'io.Folder',
    'text.PHPParser'
  );
  
  // {{{ Helper class for information gathering
  class XpHierarchyClass extends Object {
    var
      $name       = '',
      $package    = '',
      $extends    = '',
      $methods    = array(),
      $children   = array();
    
    function __construct($name, $package, $extends) {
      $this->name= $name;
      $this->package= $package;
      $this->extends= $extends;
    }
    
    function setMethods($methods) {
      $this->methods= $methods;
    }
    
    function addChild(&$c) {
      $this->children[]= &$c;
    }
    
    function buildNode() {
      $n= &new Node('class', NULL, array(
        'name'    => $this->name,
        'package' => $this->package,
        'extends' => $this->extends
      ));
      
      // Add methods
      if (sizeof($this->methods)) {
        $m= &$n->addChild(new Node('methods'));
        foreach ($this->methods as $method) {
          $m->addChild(new Node('method', $method->name));
        }
      }
      
      // Add children
      foreach (array_keys($this->children) as $idx) {
        $n->addChild($this->children[$idx]->buildNode());
      }
      
      return $n;
    }
  }
  // }}}
  
  // {{{ string fqcnFor(stromg $shortname, string $possibilities)
  //     Create a FQCN from the "extends" string.
  function fqcnFor($shortname, $possibilities) {

    // Core classes need not be noted in the uses()-clause, so first
    // check if we have one of those loaded and use the runtime-reflection.
    if (
      class_exists($shortname) &&
      0 == strncmp('lang.', xp::nameOf(strtolower($shortname)), 5)
    ) {
      return xp::nameOf(strtolower($shortname));
    }
    
    // Try to match the short class name from extends to a FQCN by matching
    // with the possibilities, usually the classes being uses()'ed.
    // We cannot use real reflection because that would make this script die
    // due to redeclared classes.
    foreach ($possibilities as $fqcn) {
      if ('.'.strtolower($shortname) == substr(strtolower($fqcn), -1 - strlen($shortname))) {
        return $fqcn;
      }
    }
    
    return throw(new IllegalArgumentException('Could not resolve short name for FQCN "'.$shortname.'"'));
  }
  // }}}
  
  // {{{ void parseclass(&array $classlist, string classname, string $uri)
  //     Parses the given class and build information container object for it
  function parseclass(&$classlist, $classname, $uri) {
    if (isset($classlist[$classname])) {
      Console::writeLine('Already know '.$classname.', skipping...');
      continue;
    }

    try(); {
      $p= &new PHPParser($uri);
      $p->parse();
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
    
    if (!sizeof($p->classes)) {
      fputs(STDERR, '!!! No classes in file '.$uri.' ?'."\n");
      return;
    }
    
    try(); {
      $extends= (isset($p->classes[0]->extends) 
        ? fqcnFor($p->classes[0]->extends, $p->uses) 
        : NULL
      );
    } if (catch('IllegalArgumentException', $e)) {
      $e->printStackTrace();
      $extends= NULL;
    }
    
    $class= &new XpHierarchyClass(
      $classname,
      substr($classname, 0, strrpos($classname, '.')),
      $extends
    );
    $class->setMethods($p->classes[0]->functions);
    
    $classlist[$classname]= &$class;
  }
  // }}}
  
  // {{{ void recurse(&array classlist, string base, string package)
  //     Recurses through all directories, parsing classes
  function recurse(&$classlist, $base, $package= '', $uri= '') {
    static $except= array('META-INF', 'CVS', '.svn');
    static $include= array('class');
    
    $f= &new Folder($base.DIRECTORY_SEPARATOR.$uri);
    if (!$f->exists()) {
      return throw(new IllegalArgumentException('Folder "'.$f->getURI().'" does not exist'));
    }

    // Go through folder entries
    while (FALSE !== ($entry= $f->getEntry())) {

      // Ignore well-known files and directories
      if (in_array($entry, $except)) continue;

      // Calculate package name
      $newpackage= ltrim($package.str_replace(
        DIRECTORY_SEPARATOR, 
        '.', 
        $uri
      ), '.');

      // Recurse into subdirectories
      if (is_dir($f->getURI().$entry)) {
        recurse($classlist, $base, $package, str_replace($base, '', $f->getURI().$entry));
        continue;
      }

      // Only take documentable files into account
      sscanf($entry, '%[^\.].%s', $filename, $indicator);
      $indicator= substr($indicator, 0, -4);
      if (!in_array($indicator, $include)) continue;

      // Console::writeLine('---> Parsing '.$newpackage.'.'.$filename);
      parseclass($classlist, $newpackage.'.'.$filename, $f->getUri().$entry);
    }
    $f->close();
  }
  // }}}
 
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef('Usage: %s %s <collection_property_file>', $p->value(-1), $p->value(0));
    exit(-2);
  }

  $tree= &new Tree('inheritance');  
  $prop= &new Properties($p->value(1));
  $classlist= array();
  if ($sect= $prop->getFirstSection()) do {

    // Process a single collection
    $base= realpath($prop->readString($sect, 'base')).DIRECTORY_SEPARATOR;
    $prefix= rtrim($prop->readString($sect, 'prefix'), '.').'.';
    
    recurse($classlist, $base);
  } while ($sect= $prop->getNextSection());
  
  // Hold the list of classes that are "non-root"
  $toberemoved= array();
  
  // Link every class into it's parent class, keep it in the main list
  // to be able to find it for it's children
  foreach ($classlist as $fqcn=> $class) {
  
    // This class does not extend any other
    if (0 == strlen($class->extends)) continue;
    
    // This class' parent could not be found in the main list
    if (!isset($classlist[$class->extends])) {
      fputs(STDERR, 'Parent class '.$class->extends.' not found!'."\n");
      fputs(STDERR, var_export($class, 1)."\n");
      continue;
    }
    
    // Link 'em
    $classlist[$class->extends]->addChild($classlist[$fqcn]);
    
    // Remember to remove from main list
    $toberemoved[]= $fqcn;
  }
  
  foreach ($toberemoved as $t) { unset($classlist[$t]); }
  
  // Starting from the mainlist, build the XML tree...
  foreach ($classlist as $fqcn=> $class) {
    $tree->addChild($classlist[$fqcn]->buildNode());
  }
  
  // ... and dump it.
  Console::writeLine($tree->getDeclaration()."\n".$tree->getSource(0));
  // }}}
?>
