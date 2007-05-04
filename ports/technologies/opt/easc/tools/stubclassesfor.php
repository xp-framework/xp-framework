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
  function processInterface($description, $purpose, $language, $showXml= FALSE) {
    $node= Node::fromObject($description, 'description');
    $node->setAttribute('purpose', $purpose);

    $proc= new DomXSLProcessor();
    $proc->setXSLFile(dirname(__FILE__).DIRECTORY_SEPARATOR.$language.'.xsl');
    $proc->setXMLBuf($node->getSource(INDENT_NONE));
    
    $showXml && Console::writeLine($purpose, ' => ', $node->getSource(INDENT_DEFAULT));

    $proc->run();
    return $proc->output();
  }
  // }}}

  // {{{ string processClass(remote.reflect.ClassWrapper wrapper, string language [, bool showXml= FALSE])
  //     Transforms a bean description and returns the sourcecode
  function processClass($wrapper, $language, $showXml= FALSE) {
    $node= Node::fromObject($wrapper, 'class');
    $node->setAttribute('name', $wrapper->getName());
    foreach ($wrapper->fields as $name => $type) {
      $node->addChild(new Node('field', NULL, array(
        'name' => $name,
        'type' => is_a($type, 'ClassReference') ? $type->referencedName() : $type
      )));
    }

    $proc= new DomXSLProcessor();
    $proc->setXSLFile(dirname(__FILE__).DIRECTORY_SEPARATOR.$language.'.xsl');
    $proc->setXMLBuf($node->getSource(INDENT_NONE));
    
    $showXml && Console::writeLine($wrapper->getName(), ' => ', $node->getSource(INDENT_DEFAULT));

    $proc->run();
    return $proc->output();
  }
  // }}}
  
  // {{{ void writeTo(string path, string classname, string source)
  //     Writes the sourcecode to the classname
  function writeTo($path, $classname, $source) {
    $file= new File(
      rtrim($path, DIRECTORY_SEPARATOR).
      DIRECTORY_SEPARATOR.
      strtr($classname, '.', DIRECTORY_SEPARATOR).'.class.php'
    );
    if ('@' == $path) {
      Console::writeLine('---> Source of ', $classname);
      Console::writeLine($source);
    } else {
      if ($file->exists()) {
        Console::writeLine('---> Class ', $classname, ' already exists, skipping');
      } else {
        Console::writeLine('---> Generating ', $classname);
        $dir= new Folder($file->getPath());
        if (!$dir->exists()) $dir->create();

        $file->open(FILE_MODE_WRITE);
        $file->write($source);
        $file->close();
      }
    }    
    Console::writeLine('     >> ', str_replace(dirname(__FILE__), '.', $file->getURI()));
  }
  // }}}
  
  // {{{ remote.reflect.ClassReference[] classSetOf(string jndi, remote.Remote remote, remote.reflect.ClassReference[] set)
  //     Returns a unique, flat set of classes
  function classSetOf($jndi, $remote, $references) {
    $set= new HashSet();
    foreach ($references as $classref) {
      try {
        $class= $remote->lookup('Class:'.$jndi.':'.$classref->referencedName());
      } catch (Throwable $e) {
        Console::writeLine('*** ', $classref->referencedName(), ' ~ ', $e->toString());
        xp::gc();
        continue;
      }
      
      $set->add($class);
      $set->addAll(classSetOf($jndi, $remote, $class->classSet()));
    }

    return $set->toArray();
  }
  // }}}
  
  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Generates stub classes by using the ESDL service

Usage
-----
$ php stubclassesfor.php <hostname> <jndiname> [-p <port>] [-o <outputpath>] [-x] [-c classlist] [-l language]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * jndiname is the name of the bean.

  * port is the port the ESDL-MBean is listening on. It defaults to 6449.

  * classlist is expected to be a comma-separated list of (fully qualified)
    classnames. If they are omitted, all classes will be generated.
  
  * outputpath is the path the files should be written to. It defaults
    to SKELETON_PATH, that is, where lang.base.php resides.
  
  * The "-x" switch shows the XML before it is transformed
  
  * language is one of "xp", "xp5" and defaults to "xp5"
__
    );
    exit(1);
  }
  
  $jndi= $p->value(2);
  try {
    $remote= Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6449).'/');
    $description= $remote->lookup('Services:'.$jndi);
  } catch (Throwable $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  if (!$description) {
    Console::writeLine('Bean '.$p->value(2).' not found');
    exit(1);
  }

  $path= $p->value('output', 'o', SKELETON_PATH);
  $showXml= $p->exists('xml');
  $language= $p->value('language', 'l', 'xp5');
  
  // If class names are passed, process them
  if ($p->exists('classes')) {
    foreach (explode(',', $p->value('classes', 'c', '')) as $name) {
      try {
        $class= $remote->lookup('Class:'.$jndi.':'.$name);
        writeTo(
          $path, 
          $class->getName(), 
          processClass($class, $language, $showXml)
        );
      } catch (Throwable $e) {
        $e->printStackTrace();
        continue;
      }
    }
    exit(0);
  }
  
  // Otherwise, create all classes
  foreach (classSetOf($jndi, $remote, $description->classSet()) as $classwrapper) {
    if ((
      $classwrapper->getName() == $description->interfaces->values[HOME_INTERFACE]->getClassName() ||
      $classwrapper->getName() == $description->interfaces->values[REMOTE_INTERFACE]->getClassName()
    )) continue;

    try {
      writeTo(
        $path, 
        $classwrapper->getName(), 
        processClass($classwrapper, 'xp', $showXml)
      );
    } catch (Throwable $e) {
      $e->printStackTrace();
      continue;
    }
  }

  // Write home and remote interfaces
  writeTo(
    $path, 
    $description->interfaces->values[HOME_INTERFACE]->getClassName(), 
    processInterface($description, 'home', $language, $showXml)
  );
  writeTo(
    $path, 
    $description->interfaces->values[REMOTE_INTERFACE]->getClassName(), 
    processInterface($description, 'remote', $language, $showXml)
  );
  // }}}
?>
