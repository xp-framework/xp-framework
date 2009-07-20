<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.XMLScriptletRequest',
    'peer.http.HttpConstants'
  );
  
  /**
   * Wraps request
   *
   * @see      xp://scriptlet.xml.XMLScriptletRequest
   * @purpose  Scriptlet request wrapper
   */
  class WorkflowScriptletRequest extends XMLScriptletRequest {
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
          $this->state= XPClass::forName($this->package.'.'.('state.'.$name.'State'))->newInstance();
        } catch (ClassNotFoundException $e) {
          throw new HttpScriptletException(
            $e->getMessage(),
            HttpConstants::STATUS_NOT_FOUND
          );
        }
      }
    }
  }
?>
