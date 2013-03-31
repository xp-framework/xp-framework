<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('SCRIPTLET_SHOW_STACKTRACE',     0x0001);
  define('SCRIPTLET_SHOW_XML',            0x0002);
  define('SCRIPTLET_SHOW_ERRORS',         0x0004);

  /**
   * Utility class to easily run HttpScriptlet derived scriptlets.
   * Automatically handles development/production environments,
   * prints stacktraces, debug XML and/or error messages.
   *
   * Use with SAPIs scriptlet.development or scriptlet.production
   *
   * @purpose  Run scriptlets
   */
  class ScriptletRunner extends Object {
    public
      $flags    = 0x0000;
      
    /**
     * Constructor
     *
     * @param   int flags default 0
     */
    public function __construct($flags= 0x0000) {
      $this->flags= $flags;
    }
    
    /**
     * Handle exception from scriptlet
     *
     * @param   scriptlet.xml.XMLScriptletResponse response
     * @param   lang.XPException e
     */
    public function except($response, $e) {
      $class= $this->getClass();
      $loader= $class->getClassLoader();
      
      $response->setContent(str_replace(
        '<xp:value-of select="reason"/>',
        (($this->flags & SCRIPTLET_SHOW_STACKTRACE) 
          ? $e->toString() 
          : $e->getMessage()
        ),
        $loader->getResource('sapi/scriptlet/error'.$response->statusCode.'.html')
      ));
    }
  
    /**
     * Run the given scriptlet
     *
     * @param   scriptlet.HttpScriptlet scriptlet
     */
    public function run($scriptlet) {
      try {
        $scriptlet->init();
        $response= $scriptlet->process();
      } catch (HttpScriptletException $e) {
        $response= $e->getResponse();
        $this->except($response, $e);
      }

      // Send output
      // Send headers if they are not already send by the scriptlet
      if (!$response->isCommitted()) {
        $response->sendHeaders();
      }
      $response->sendContent();
      flush();

      // Call scriptlet's finalizer
      $scriptlet->finalize();
      
      if (
        ($this->flags & SCRIPTLET_SHOW_XML) &&
        ($response && isset($response->document))
      ) {
        echo '<xmp>', $response->document->getDeclaration()."\n".$response->document->getSource(0), '</xmp>';
      }
      
      if (($this->flags & SCRIPTLET_SHOW_ERRORS)) {
        echo '<xmp>', var_export(xp::$errors, 1), '</xmp>';
      } 
    }  
  }
?>
