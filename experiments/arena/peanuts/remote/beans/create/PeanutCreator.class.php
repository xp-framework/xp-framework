<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.beans.create.AbstractBeanCreator',
    'io.File'
  );

  /**
   * (Insert class' description here)
   *
   * @purpose  Abstract base class
   */
  class PeanutCreator extends AbstractBeanCreator {
    protected
      $interfaceName= '';
      
    /**
     * Returns a mapped type
     *
     * @param   string phpType in the PHP typename
     * @return  string the Java typename
     */
    protected function javaType($phpType) {
      static $map= array(
        'string' => 'String'
      );
      
      return isset($map[$phpType]) ? $map[$phpType] : $in;
    }
  
    /**
     * Creates a string from a method's argument list
     *
     * @param   lang.reflect.Argument[] arguments
     * @return  string
     */
    protected function argumentString(array $arguments) {
      $r= '';
      foreach ($arguments as $arg) {
        $r.= $this->javaType($arg->getType()).' '.$arg->getName().', ';
      }
      return substr($r, 0, -2);
    }
  
    /**
     * Creates a string from a method's argument list
     *
     * @param   lang.reflect.Argument[] arguments
     * @return  string
     */
    protected function paramString(array $arguments) {
      $r= '';
      foreach ($arguments as $arg) {
        $r.= $arg->getName().', ';
      }
      return substr($r, 0, -2);
    }

    /**
     * Creates the remote interface
     *
     */
    #[@create('remote interface')]
    public function createRemoteInterface() {
      $this->interfaceName= substr($this->className, 0, -4);

      $out= new File(
        $this->outputDir->getURI().
        strtr($this->packageName, '.', DIRECTORY_SEPARATOR).
        DIRECTORY_SEPARATOR.
        $this->interfaceName.'.java'
      );
      $this->out->writeLine('---> ', $out);
      
      $out->open(FILE_MODE_WRITE);
      $out->write("package ".$this->packageName.";\n\n");
      $out->write("import javax.ejb.Remote;\n\n");
      $out->write("@Remote\n");
      $out->write("public interface ".$this->interfaceName." {\n\n");
      
      foreach ($this->remoteMethods as $method) {
        $out->write(sprintf(
          "    public %s %s(%s) throws Exception;\n",
          $this->javaType($method->getReturnType()),
          $method->getName(),
          $this->argumentString($method->getArguments())
        ));
      }
      
      $out->write("\n}");
      $out->close();
    }

    #[@create('bean implementation')]
    public function createBeanImplementation() {
      
      // Lookup the javax.ejb annotation corresponding to our bean type
      switch ($this->beanType) {
        case STATELESS: $annotation= 'Stateless'; break;
        default: throw new IllegalArgumentException('Unknown bean type '.$this->beanType);
      }
      
      $out= new File(
        $this->outputDir->getURI().
        strtr($this->packageName, '.', DIRECTORY_SEPARATOR).
        DIRECTORY_SEPARATOR.
        $this->className.'.java'
      );
      $this->out->writeLine('---> ', $out);

      $out->open(FILE_MODE_WRITE);
      $out->write("package ".$this->packageName.";\n\n");
      $out->write("import javax.script.*;\n");
      $out->write("import javax.ejb.".$annotation.";\n\n");
      $out->write("@".$annotation."\n");
      $out->write("public class ".$this->className." implements ".$this->interfaceName." {\n\n");
      
      // The scriptInterface() method
      $out->write("    protected ".$this->interfaceName." scriptInterface() {\n");
      $out->write("        ScriptEngine e = new ScriptEngineManager().getEngineByName(\"turpitude\");\n");
      $out->write("        if (null == e) {\n");
      $out->write("             throw new ScriptException(\"Script engine not found\");\n");
      $out->write("        }\n");
      $out->write("        CompiledScript script= ((Compilable)e).compile(new StringBuffer()\n");
      
      $source= $this->beanClass->getClassLoader()->loadClassBytes($this->beanClass->getName());
      foreach (explode("\n", $source) as $chunk) {
        $out->write("           .append(\"".$chunk."\\n\")\n");
      }
      
      $out->write("           .toString()\n        );\n");
      $out->write("        return ((Invocable)script).getInterface(".$this->interfaceName.".class);\n");
      $out->write("    }\n\n");
      
      foreach ($this->remoteMethods as $method) {
        $out->write(sprintf(
          "    public %s %s(%s) throws Exception {\n",
          $this->javaType($method->getReturnType()),
          $method->getName(),
          $this->argumentString($method->getArguments())
        ));
        $out->write(sprintf(
          "        return this.scriptInterface().%s(%s);\n",
          $method->getName(),
          $this->paramString($method->getArguments())
        ));
        $out->write("    }\n");
      }

      $out->write("\n}");
      $out->close();    
    }
  }
?>
