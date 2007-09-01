<?php
/* This class is part of the XP framework
 *
 * $Id: WorkflowScriptletRequest.class.php 9492 2007-02-26 14:00:13Z gelli $
 */

  namespace scriptlet::xml::workflow;

  uses('scriptlet.xml.XMLScriptletRequest');
  
  /**
   * Wraps request
   *
   * @see      xp://scriptlet.xml.XMLScriptletRequest
   * @purpose  Scriptlet request wrapper
   */
  class WorkflowScriptletRequest extends scriptlet::xml::XMLScriptletRequest {
    public
      $package      = NULL,
      $state        = NULL;

    /**
     * Constructor
     *
     * @param   string package
     */
    public function __construct($package) {
      $this->package= $package;
    }

    /**
     * Initialize this request object - overridden from base class.
     *
     * @see     xp://scriptlet.xml.XMLScriptletRequest#initialize
     */
    public function initialize() {
      parent::initialize();
      if ($this->stateName) {
        $name= implode('', array_map('ucfirst', array_reverse(explode('/', $this->stateName))));
        try {
          $class= lang::XPClass::forName($this->package.'.'.('state.'.$name.'State'));
        } catch (lang::ClassNotFoundException $e) {
          throw new scriptlet::HttpScriptletException(
            $e->getMessage(),
            HTTP_NOT_FOUND
          );
        }

        $this->state= $class->newInstance();
      }
    }
  }
?>
