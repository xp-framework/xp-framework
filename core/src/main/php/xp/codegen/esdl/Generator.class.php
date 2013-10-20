<?php namespace xp\codegen\esdl;

use xp\codegen\AbstractGenerator;
use xml\DomXSLProcessor;
use xml\Node;
use remote\Remote;
use remote\ClassReference;
use remote\reflect\BeanDescription;
use util\collections\HashSet;

/**
 * ESDL
 * ====
 * This utility generates Remote interfaces and value object 
 * representations for EASC
 *
 * Usage:
 * <pre>
 *   $ cgen ... esdl {dsn} {jndi} [-l {language}]
 * </pre>
 *
 * Options
 * -------
 * <ul>
 *   <li>language: Language to generate, defaults to "xp5"</li>
 * </ul>
 *
 * Languages
 * ---------
 * The following languages are supported: xp4, xp5, xp5fq
 */
class Generator extends AbstractGenerator {
  const
    ESDL_PORT   = 6449;
    
  protected
    $remote     = null,
    $jndi       = null,
    $uri        = '',
    $processor  = null,
    $package    = null;
  
  /**
   * Constructor
   *
   * @param   util.cmd.ParamString args
   */
  public function __construct(\util\cmd\ParamString $args) {
  
    $url= new \peer\URL($args->value(0));

    // If protocol string does not contain port number, set default.
    if (self::ESDL_PORT === $url->getPort(self::ESDL_PORT)) $url->setPort(self::ESDL_PORT);
    
    // Check given URL to inform user if invalid port used.
    if (self::ESDL_PORT !== $url->getPort()) {
      \util\cmd\Console::$err->writeLine('Notice: using non-standard port '.$url->getPort().', ESDL services are usually available at port 6449.');
    }
    
    $this->remote= Remote::forName($url->getURL());
    $this->jndi= $args->value(1);

    $this->processor= new DomXSLProcessor();
    $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource($args->value('lang', 'l', 'xp5').'.xsl'));
  }
  
  /**
   * Looks up service description
   *
   * @return  lang.Object
   */
  #[@target]
  public function serviceDescription() {
    return $this->remote->lookup('Services:'.$this->jndi);
  }
  
  /**
   * Produces a set (a unique list) of classes.
   *
   */
  protected function classSetOf($references) {
    $set= array();
    foreach ($references as $classref) {
      try {
        $class= $this->remote->lookup('Class:'.$this->jndi.':'.$classref->referencedName());
      } catch (\remote\RemoteException $e) {
        \util\cmd\Console::$err->writeLine('*** unable to lookup remote class: ', $classref->referencedName());
        continue;
      } catch (\lang\Throwable $e) {
        \util\cmd\Console::$err->writeLine('*** ', $classref->referencedName(), ' ~ ', $e);
        continue;
      }
      $set[$classref->referencedName()]= $class;
      foreach ($this->classSetOf($class->classSet()) as $name => $class) $set[$name]= $class;
    }

    return $set;
  }

  /**
   * Produces a set (a unique list) of classes.
   *
   */
  #[@target(input= 'serviceDescription')]
  public function classList($description) {
    $set= $this->classSetOf($description->classSet());
    foreach ($description->getInterfaces() as $interface) {
      unset($set[$interface->getClassName()]);
    }
    return $set;
  }

  /**
   * Writes all classes
   *
   */
  #[@target(input= array('classList', 'output'))]
  public function writeClasses($classes, $output) {
    foreach ($classes as $wrapper) {
      $node= Node::fromObject($wrapper, 'class');
      $node->setAttribute('name', $wrapper->getName());
      foreach ($wrapper->fields as $name => $type) {
        $node->addChild(new Node('field', null, array(
          'name' => $name,
          'type' => $type instanceof ClassReference ? $type->referencedName() : $type
        )));
      }

      $this->processor->setXMLBuf($node->getSource(INDENT_NONE));
      $this->processor->run();
      try {
        $output->append(strtr($wrapper->getName(), '.', '/').\xp::CLASS_FILE_EXT, $this->processor->output());
      } catch (\io\IOException $e) {
        \util\cmd\Console::$err->writeLine('*** ', $wrapper->getName(), ' ("', $e->getMessage(), '")');
        continue;
      } catch (\lang\Throwable $e) {
        \util\cmd\Console::$err->writeLine('*** ', $wrapper->getName(), ' ~ ', $e->toString);
        continue;
      }
    }
  }

  /**
   * Writes all interfaces
   *
   */
  #[@target(input= array('serviceDescription', 'output'))]
  public function writeInterfaces($description, $output) {
    static $purposes= array(
      HOME_INTERFACE   => 'home',
      REMOTE_INTERFACE => 'remote'
    );

    foreach ($description->getInterfaces() as $type => $interface) {
      $node= Node::fromObject($interface, 'interface');
      $node->setAttribute('purpose', $purposes[$type]);
      $node->addChild(new Node('jndiName', $this->jndi));

      $this->processor->setXMLBuf($node->getSource(INDENT_NONE));
      $this->processor->run();
      $output->append(strtr($interface->getClassName(), '.', '/').\xp::CLASS_FILE_EXT, $this->processor->output());
    }
  }
  
  /**
   * Creates a string representation of this generator
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'['.$this->jndi.'@'.$this->remote->toString().']';
  }
}
