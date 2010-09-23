<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.ast.Node', 
    'xp.compiler.ast.ParseTree', 
    'xp.compiler.optimize.Optimizations',
    'xp.compiler.checks.Checks',
    'util.log.Traceable'
  );

  /**
   * Base class for emitters
   *
   * @see      xp://xp.compiler.ast.Node
   */
  abstract class Emitter extends Object implements Traceable {
    protected $cat= NULL;
    protected $messages= array(
      'warnings' => array(),
      'errors'   => array()
    );
    protected $optimizations= NULL;
    protected $checks= NULL;

    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->optimizations= new Optimizations();
      $this->checks= new Checks();
    }

    /**
     * Adds an optimization
     *
     * @param   xp.compiler.optimize.Optimization o
     * @return  xp.compiler.optimize.Optimization
     */
    public function addOptimization(Optimization $o) {
      $this->optimizations->add($o);
      return $o;
    }
    
    /**
     * Adds an optimization
     *
     * @param   xp.compiler.optimize.Optimization o
     * @return  xp.compiler.emit.Emitter this
     */
    public function withOptimization(Optimization $o) {
      $this->optimizations->add($o);
      return $this;
    }

    /**
     * Adds a check
     *
     * @param   xp.compiler.checks.Checks c
     * @param   bool error
     * @return  xp.compiler.checks.Check
     */
    public function addCheck(Check $c, $error= FALSE) {
      $this->checks->add($c, $error);
      return $c;
    }
    
    /**
     * Adds a check
     *
     * @param   xp.compiler.checks.Checks c
     * @param   bool error
     * @return  xp.compiler.emit.Emitter this
     */
    public function withCheck(Check $c, $error= FALSE) {
      $this->checks->add($c, $error);
      return $this;
    }
    
    /**
     * Entry point
     *
     * @param   xp.compiler.ast.ParseTree tree
     * @param   xp.compiler.types.Scope scope
     * @return  xp.compiler.Result
     */
    public abstract function emit(ParseTree $tree, Scope $scope);
    
    /**
     * Format a message
     *
     * @param   string code
     * @param   string message
     * @param   xp.compiler.ast.Node context
     * @return  string
     */
    protected function format($code, $message, xp·compiler·ast·Node $context= NULL) {
      if ($context) {               // Use given context node
        $pos= $context->position;
      } else {                      // Try to determine last context node from backtrace
        $pos= array(0, 0);
        foreach (create(new Throwable(NULL))->getStackTrace() as $element) {
          if (
            'emit' == substr($element->method, 0, 4) &&
            sizeof($element->args) > 1 &&
            $element->args[1] instanceof xp·compiler·ast·Node
          ) {
            $pos= $element->args[1]->position;
            break;
          }
        }
      }
      
      return sprintf('[%4s] %s at line %d, offset %d', $code, $message, $pos[0], $pos[1]);
    }
    
    /**
     * Issue a warning
     *
     * @param   string code
     * @param   string message
     * @param   xp.compiler.ast.Node context
     */
    public function warn($code, $message, xp·compiler·ast·Node $context= NULL) {
      $message= $this->format($code, $message, $context);
      $this->cat && $this->cat->warn($message);
      $this->messages['warnings'][]= $message;
    }

    /**
     * Raise an error
     *
     * @param   string code
     * @param   string message
     * @param   xp.compiler.ast.Node context
     */
    public function error($code, $message, xp·compiler·ast·Node $context= NULL) {
      $message= $this->format($code, $message, $context);
      $this->cat && $this->cat->error($message);
      $this->messages['errors'][]= $message;
    }
    
    /**
     * Get a list of all messages
     *
     * @return  string[] messages
     */
    public function messages() {
      $r= array();
      foreach ($this->messages as $type => $messages) {
        $r+= $messages;
      }
      return $r;
    }
    
    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  }
?>
