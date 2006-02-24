<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'remote.Remote',
    'xml.Node',
    'io.File',
    'io.Folder',
    'xml.DomXSLProcessor',
    'util.cmd.ParamString'
  );
  
  // {{{ string processInterface(remote.reflect.BeanDescription description, string purpose, string language [, bool showXml= FALSE])
  //     Transforms a bean description and returns the sourcecode
  function processInterface(&$description, $purpose, $language, $showXml= FALSE) {
    $node= &Node::fromObject($description, 'description');
    $node->setAttribute('purpose', $purpose);

    $proc= &new DomXSLProcessor();
    $proc->setXSLFile(dirname(__FILE__).DIRECTORY_SEPARATOR.$language.'.xsl');
    $proc->setXMLBuf($node->getSource(INDENT_NONE));
    
    $showXml && Console::writeLine($purpose, ' => ', $node->getSource(INDENT_DEFAULT));

    try(); {
      $proc->run();
    } if (catch('xml.TransformerException', $e)) {
      return throw($e);
    }
    
    return $proc->output();
  }
  // }}}

  // {{{ string processClass(remote.reflect.ClassWrapper wrapper, string language [, bool showXml= FALSE])
  //     Transforms a bean description and returns the sourcecode
  function processClass(&$wrapper, $language, $showXml= FALSE) {
    $node= &Node::fromObject($wrapper, 'class');
    $node->setAttribute('name', $wrapper->getName());
    foreach ($wrapper->fields as $name => $type) {
      $node->addChild(new Node('field', NULL, array(
        'name' => $name,
        'type' => is_a($type, 'ClassReference') ? $type->referencedName() : $type
      )));
    }

    $proc= &new DomXSLProcessor();
    $proc->setXSLFile(dirname(__FILE__).DIRECTORY_SEPARATOR.$language.'.xsl');
    $proc->setXMLBuf($node->getSource(INDENT_NONE));
    
    $showXml && Console::writeLine($wrapper->getName(), ' => ', $node->getSource(INDENT_DEFAULT));

    try(); {
      $proc->run();
    } if (catch('xml.TransformerException', $e)) {
      return throw($e);
    }
    
    return $proc->output();
  }
  // }}}
  
  // {{{ void writeTo(string path, string classname, string source)
  //     Writes the sourcecode to the classname
  function writeTo($path, $classname, $source) {
    $file= &new File(
      rtrim($path, DIRECTORY_SEPARATOR).
      DIRECTORY_SEPARATOR.
      strtr($classname, '.', DIRECTORY_SEPARATOR).'.class.php'
    );
    Console::writeLine('---> ', $classname);
    try(); {
      $dir= &new Folder($file->getPath());
      if (!$dir->exists()) $dir->create();

      $file->open(FILE_MODE_WRITE);
      $file->write($source);
      $file->close();
    } if (catch('io.IOException', $e)) {
      return throw($e);
    }
    
    Console::writeLine('     >> ', str_replace(dirname(__FILE__), '.', $file->getURI()));
  }
  // }}}
  
  // {{{ void createClasses(string jndi, &lang.ClassLoader cl, &remote.Remote remote, &remote.reflect.ClassReference[] set)
  //     Create classes
  function classSetOf($jndi, &$cl, &$remote, &$references) {
    $set= &new HashSet();
    foreach ($references as $classref) {
      try(); {
        $class= &$remote->lookup('Class:'.$jndi.':'.$classref->referencedName());
      } if (catch('Exception', $e)) {
        Console::writeLine('*** ', $classref->referencedName(), ' ~ ', $e->toString());
        continue;
      }
      
      $set->add($class);
      $set->addAll(classSetOf($jndi, $cl, $remote, $class->classSet()));
    }

    return $set->toArray();
  }
  // }}}
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Generates stub classes by using the ESDL service

Usage
-----
$ php stubclassesfor.php <hostname> <jndiname> [-p <port>] [-o <outputpath>] [-x]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * jndiname is the name of the bean.

  * port is the port the ESDL-MBean is listening on. It defaults to 6449.
  
  * outputpath is the path the files should be written to. It defaults
    to SKELETON_PATH, that is, where lang.base.php resides.
  
  * The "-x" switch shows the XML before it is transformed
__
    );
    exit(1);
  }
  
  $jndi= $p->value(2);
  try(); {
    $remote= &Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6449).'/');
    $remote && $description= &$remote->lookup('Services:'.$jndi);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  if (!$description) {
    Console::writeLine('Bean '.$p->value(2).' not found');
    exit(1);
  }

  $path= $p->value('output', 'o', SKELETON_PATH);
  $showXml= $p->exists('xml');
  
  // Create all classes
  foreach (classSetOf($jndi, ClassLoader::getDefault(), $remote, $description->classSet()) as $classwrapper) {
    try(); {
      writeTo(
        $path, 
        $classwrapper->getName(), 
        processClass($classwrapper, 'xp', $showXml)
      );
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      continue;
    }
  }

  // Write home and remote interfaces
  writeTo(
    $path, 
    $description->interfaces[HOME_INTERFACE]->getClassName(), 
    processInterface($description, 'home', 'xp', $showXml)
  );
  writeTo(
    $path, 
    $description->interfaces[REMOTE_INTERFACE]->getClassName(), 
    processInterface($description, 'remote', 'xp', $showXml)
  );
  // }}}
?>
