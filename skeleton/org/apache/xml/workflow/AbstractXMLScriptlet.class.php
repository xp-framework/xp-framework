<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.XMLScriptlet',
    'org.apache.xml.workflow.Context'
  );
  
  /**
   * (Insert class' description here)
   *
   * <code>
   *   uses('org.apache.xml.workflow.AbstractXMLScriptlet');
   *   
   *   $s= &new AbstractXMLScriptlet(new ClassLoader('de.abi-time.scriptlet'), '../xsl/');
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *   
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= &$e->getResponse(); 
   *   }
   *   $response->sendHeaders();
   *   $response->sendContent();
   *   $s->finalize();
   * </code>
   *
   * @see      xp://org.apache.xml.XMLScriptlet
   * @purpose  purpose
   */
  class AbstractXMLScriptlet extends XMLScriptlet {
    var
      $needsSession = TRUE,
      $classloader  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   string xslbase
     */
    function __construct(&$classloader, $xslbase) {
      $this->classloader= &$classloader;
      parent::__construct($xslbase);
    }
    
    /**
     * Create session and set up context
     *
     * @access  protected
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @return  bool
     */
    function doCreateSession(&$request, &$response) {
      $context= &new Context();
      $context->initialize($this->classloader);
      $request->session->putValue('context', $context);
      
      return parent::doCreateSession($request, $response);
    }
  
    /**
     * Handle all requests
     *
     * @see     xp://org.apache.xml.XMLScriptlet#doGet
     * @access  protected
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @return  bool
     */
    function doGet(&$request, &$response) {
      try(); {
        do {
          if (!($context= &$request->session->getValue('context'))) break;
          $context->handleRequest($request, $response);
          $request->session->putValue('context', $context);
        } while (0);
      } if (catch('ContextFailedException', $e)) {
        return throw(new HttpScriptletException('Could not execute request: '.$e->message));
      } if (catch('Exception', $e)) {
        return throw(new HttpScriptletException('Internal error: '.$e->message));
      }

      return parent::doGet($request, $response);
    }
  }
?>
