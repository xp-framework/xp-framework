<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('OCCURRENCE_UNDEFINED',    0x0000);
  define('OCCURRENCE_OPTIONAL',     0x0001);
  define('OCCURRENCE_MULTIPLE',     0x0002);
  define('OCCURRENCE_PASSBEHIND',   0x0004);

  /**
   * Wrapper
   *
   * @see      xp://scriptlet.xml.workflow.Handler#setWrapper
   * @purpose  Base class
   */
  class Wrapper extends Object {
    var
      $paraminfo    = array(),
      $values       = array();

    /**
     * Set up this handler. Called when the handler has not yet been
     * registered to the session.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.workflow.Handler handler
     */
    function setup(&$request, &$handler) {
      foreach ($this->paraminfo as $name => $definitions) {
        
        // Pre-fill form value if a default is defined and the request
        // does not define such a parameter.
        //
        // Note: This will only happen when the handler itself is set up.
        if (isset($definitions['default']) && '' == $request->getParam($name, '')) {
          $request->params[$name]= $definitions['default'];
        }
        
        // If this is a pass-behind value, register it to the handler's 
        // values. "Pass-behind" means this value is retrieved from the 
        // session (where it has been registered to during this call)
        // rather than from the request data (GET / POST / COOKIE).
        if ($definitions['occurrence'] & OCCURRENCE_PASSBEHIND) {
          $handler->setValue($name, $request->params[$name]);
        }
      } 
    }
    
    /**
     * Retrieve a checker instance
     *
     * @access  protected
     * @param   array defines
     * @return  &lang.Object
     */
    function &checkerInstanceFor($defines) {
      static $class= array();

      if (!$defines) return NULL;

      $name= array_shift($defines);
      try(); {
        if (!isset($class[$name])) $class[$name]= &XPClass::forName($name);
      } if (catch('ClassNotFoundException', $e)) {
        unset($class[$name]);
        return NULL;
      }

      return call_user_func_array(array(&$class[$name], 'newInstance'), $defines);
    }
    
    /**
     * Register definitions for a specified parameter
     *
     * Examples:
     * <code>
     *   // Order date, optional, retrieve as date object, defaulting to today
     *   $this->registerParamInfo(
     *     'orderdate',
     *     OCCURRENCE_OPTIONAL,
     *     Date::now(),
     *     array('scriptlet.xml.workflow.casters.ToDate')
     *   );
     *
     *   // T-Shirt size, may be either S, M, L or XL
     *   $this->registerParamInfo(
     *     'tshirt.size',
     *     OCCURRENCE_UNDEFINED,
     *     NULL,                // No default, required attribute
     *     NULL,                // No cast necessary
     *     NULL,                // No precheck necessary, non-empty suffices
     *     array('scriptlet.xml.workflow.checkers.OptionChecker', array('S', 'M', 'L', 'XL'))
     *   );
     *
     *   // Quantity check, must be numeric, must be in range 1 to 10
     *   $this->registerParamInfo(
     *     'tshirt.quantity',
     *     OCCURRENCE_UNDEFINED,
     *     NULL,                // No default, required attribute
     *     array('scriptlet.xml.workflow.casters.ToInteger'),
     *     array('scriptlet.xml.workflow.checkers.NumericChecker'),
     *     array('scriptlet.xml.workflow.checkers.IntegerRangeChecker', 1, 10)
     *   );
     * </code>
     *
     * @access  protected
     * @param   string name
     * @param   int occurrence default OCCURRENCE_UNDEFINED
     * @param   mixed default default NULL
     * @param   string[] caster default NULL
     * @param   string[] precheck default NULL
     * @param   string[] postcheck default NULL
     * @param   string type default 'core:string'
     * @param   array values default array()
     */
    function registerParamInfo(
      $name, 
      $occurrence= OCCURRENCE_UNDEFINED, 
      $default= NULL,
      $caster= NULL, 
      $precheck= NULL, 
      $postcheck= NULL,
      $type= 'core:string',
      $values= array()
    ) {
      $this->paraminfo[$name]= array(
        'occurrence' => $occurrence,
        'default'    => $default,
        'precheck'   => $this->checkerInstanceFor($precheck),
        'caster'     => $this->checkerInstanceFor($caster),
        'postcheck'  => $this->checkerInstanceFor($postcheck),
        'type'       => $type,
        'values'     => $values
      );
    }

    /**
     * Retrieve parameter names
     *
     * @access  public
     * @return  string[]
     */
    function getParamNames() {
      return array_keys($this->paraminfo);
    }
    
    /**
     * Retrieve a value by its name
     *
     * @access  public
     * @param   string name
     * @param   mixed default default NULL
     * @return  mixed value
     */
    function getValue($name, $default= NULL) {
      return isset($this->values[$name]) ? $this->values[$name] : $default;
    }
    
    /**
     * Load request values from request data
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.workflow.Handler handler
     */
    function load(&$request, &$handler) { 
      foreach ($this->paraminfo as $name => $definitions) {

        // Retrieve the parameter's value from the request (or from the
        // handler's values, if it is passed behind the scenes).
        if ($definitions['occurrence'] & OCCURRENCE_PASSBEHIND) {
          $value= (array)$handler->getValue($name, '');
        } else {
          $value= (array)$request->getParam($name, '');
        }
      
        // Check if the parameter is present (and evaluates to a non-empty
        // string). In case the definitions state this parameter is optional, 
        // it may be omitted and no further checks will be imposed on it.
        //
        // We use the "trick" of casting it to an array because request 
        // parameters might also come as an array. This way, we always get
        // an array, as casting an array to an array simply results in the
        // same array (breaking nothing) and casting scalars will end up in
        // an array with the scalar as the first element.
        //
        // If the value is validated and the occurrence definition contains
        // the string "multiple", the array will be preserved. Otherwise, the
        // first element will be copied to the values hash, thus making 
        // accessibility easy.
        if (0 == strlen($value[key($value)])) {
          if (!($definitions['occurrence'] & OCCURRENCE_OPTIONAL)) {
            $handler->addError('missing', $name);
            continue;
          }
          
          // Set it to the default value
          if ($definitions['default']) $value[key($value)]= $definitions['default'];
        } else {
 
          // Run the precheck. This can be utilized for assertion-style checks
          // and to prevent casting (which may be expensive). For example, we 
          // needn't try "casting" the string "foo" to a peer.mail.InternetAddress
          // object as it doesn't even contain the "@".
          //
          // Pre- and postchecks return an error code or NULL if they are content
          if ($definitions['precheck']) {
            if (NULL !== ($code= call_user_func(array(&$definitions['precheck'], 'check'), $value))) {
              $handler->addError($definitions['precheck']->getClassName().'.'.$code, $name);
              continue;
            }
          }

          // Cast the parameter if requested before doing any other checks 
          // on it. The casters return an array in case it succeeds. Any other
          // type indicates an error and will be used as informational data
          // for the form error (an exception message, for instance).
          if ($definitions['caster']) {
            if (!is_array($value= call_user_func(array(&$definitions['caster'], 'castValue'), $value))) {
              $handler->addError($definitions['caster']->getClassName().'.invalidcast', $name, $value);
              continue;
            }
          }

          // Now, run the postcheck. The postcheck receives the already casted
          // values.
          if ($definitions['postcheck']) {
            if (NULL !== ($code= call_user_func(array(&$definitions['postcheck'], 'check'), $value))) {
              $handler->addError($definitions['postcheck']->getClassName().'.'.$code, $name);
              continue;
            }
          }
        }
        
        // If we get here, the parameter is validated. Copy the value into
        // the values hash which is publicly accessible.
        if ($definitions['occurrence'] & OCCURRENCE_MULTIPLE) {
          $this->values[$name]= $value;
        } elseif (isset($value[key($value)])) {
          $this->values[$name]= $value[key($value)];
        } else {
          $this->values[$name]= NULL;
        }
      }
    }
  }
?>
