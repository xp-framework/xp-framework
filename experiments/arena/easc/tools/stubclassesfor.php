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
  
  // {{{ string process(remote.reflect.BeanDescription description, string purpose [, string language= 'xp'])
  //     Transforms a bean description and returns the sourcecode
  function process(&$description, $purpose, $language= 'xp') {
    $node= &Node::fromObject($description, 'description');
    $node->setAttribute('purpose', $purpose);

    $proc= &new DomXSLProcessor();
    $proc->setXSLFile(dirname(__FILE__).DIRECTORY_SEPARATOR.$language.'.xsl');
    $proc->setXMLBuf($node->getSource(INDENT_NONE));

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
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLine(<<<__
Generates stub classes by using the ESDL service

Usage
-----
$ php stubclassesfor.php <hostname> <jndiname> [-p <port>] [-o <outputpath>]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * jndiname is the name of the bean.

  * port is the port the ESDL-MBean is listening on. It defaults to 6449.
  
  * outputpath is the path the files should be written to. It defaults
    to SKELETON_PATH, that is, where lang.base.php resides.
__
    );
    exit(1);
  }
  
  try(); {
    $remote= &Remote::forName('xp://'.$p->value(1).':'.$p->value('port', 'p', 6449).'/');
    $remote && $services= &$remote->lookup('Services');
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  if (!($description= &$services->bean($p->value(2)))) {
    Console::writeLine('Bean '.$p->value(2).' not found');
    exit(1);
  }
  
  $path= $p->value('output', 'o', SKELETON_PATH);
  writeTo($path, $description->interfaces[HOME_INTERFACE]->getClassName(), process($description, 'home'));
  writeTo($path, $description->interfaces[REMOTE_INTERFACE]->getClassName(), process($description, 'remote'));
  // }}}
?>
