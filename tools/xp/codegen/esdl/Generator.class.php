<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.codegen.esdl';
  
  uses(
    'xp.codegen.AbstractGenerator',
    'xml.DomXSLProcessor', 
    'xml.Node',
    'remote.Remote',
    'remote.ClassReference',
    'util.collections.HashSet'
  );

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
   *
   * @purpose  Code generator
   */
  class xp·codegen·esdl·Generator extends AbstractGenerator {
    const
      ESDL_PORT   = 6449;
      
    protected
      $uri        = '',
      $processor  = NULL,
      $package    = NULL;
    
    /**
     * Constructor
     *
     * @param   util.cmd.ParamString args
     */
    public function __construct(ParamString $args) {
    
      $url= new URL($args->value(0));

      // If protocol string does not contain port number, set default.
      if (self::ESDL_PORT === $url->getPort(self::ESDL_PORT)) $url->setPort(self::ESDL_PORT);
      
      // Check given URL to inform user if invalid port used.
      if (self::ESDL_PORT !== $url->getPort()) {
        Console::$err->writeLine('Notice: using non-standard port '.$url->getPort().', ESDL services are usually available at port 6449.');
      }
      
      $this->remote= Remote::forName($url->getURL());
      $this->jndi= $args->value(1);

      $this->processor= new DomXSLProcessor();
      $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource($args->value('lang', 'l', 'xp5').'.xsl'));
    }
    
    /**
     * Looks up service description
     *
     */
    #[@target]
    public function serviceDescription() {
      return $this->remote->lookup('Services:'.$this->jndi);
    }
    
    /**
     * Produces a set (a unique list) of classes.
     *
     */
    #[@target(input= array('serviceDescription', 'output'))]
    public function writeInterfaces($description, $output) {
      static $purposes= array(
        HOME_INTERFACE   => 'home',
        REMOTE_INTERFACE => 'remote'
      );

      foreach ($description->getInterfaces() as $type => $interface) {
        $node= Node::fromObject($interface, 'description');
        $node->setAttribute('purpose', $purposes[$type]);

        $this->processor->setXMLBuf($node->getSource(INDENT_NONE));
        $output->append(strtr($interface->getClassName(), '.', '/').xp::CLASS_FILE_EXT, $this->processor->output());
      }
    }
    
    /**
     * Produces a set (a unique list) of classes.
     *
     */
    protected function classSetOf($references) {
      $set= new HashSet();
      foreach ($references as $classref) {
        try {
          $class= $this->remote->lookup('Class:'.$this->jndi.':'.$classref->referencedName());
        } catch (Throwable $e) {
          Console::$err->writeLine('*** ', $classref->referencedName(), ' ~ ', $e->toString());
          continue;
        }

        $set->add($class);
        $set->addAll($this->classSetOf($class->classSet())->toArray());
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
        $set->remove($interface);
      }
      return $set->toArray();
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
          $node->addChild(new Node('field', NULL, array(
            'name' => $name,
            'type' => $type instanceof ClassReference ? $type->referencedName() : $type
          )));
        }

        $this->processor->setXMLBuf($node->getSource(INDENT_NONE));
        $this->processor->run();
        $output->append(strtr($wrapper->getName(), '.', '/').xp::CLASS_FILE_EXT, $this->processor->output());
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
?>
