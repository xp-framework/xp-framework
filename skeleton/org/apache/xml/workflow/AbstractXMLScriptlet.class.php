<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.XMLScriptlet',
    'org.apache.xml.workflow.Context'
  );
  
  uses(
    'util.log.Logger',
    'util.log.FileAppender'
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
   *     // Retreive standard "Internal Server Error"-Document
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
      $needsSession = TRUE;
      
    var 
      $cat          = NULL,
      $classloader  = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @param   
     */
    function __construct(&$classloader, $xslbase) {
      $l= &Logger::getInstance();
      $this->cat= &$l->getCategory($this->getClassName());
      $this->cat->addAppender(new FileAppender('/tmp/scriptlet.log'));
      
      $this->classloader= &$classloader;
      parent::__construct($xslbase);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function doCreateSession(&$request, &$response) {
      $this->cat->info('------ in doCreateSession(', $request, $request->getSession(), ')');
      
      // Set up context
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
        $context= &$request->session->getValue('context');
      } if (catch('Exception', $e)) {
        $this->cat->error('Could not find context', $e->getStackTrace());
        return throw(new HttpScriptletException($e->message));
      }
      
      $this->cat->info('------ in doGet(', $request->getSessionId(), $context, ')');
      
      try(); {
        $context->handleRequest($request, $response);
      } if (catch('HttpScriptletException', $e)) {
        $this->cat->error('Could not handle request', $e->getStackTrace());
        return throw($e);
      }
      
      return parent::doGet($request, $response);
    }
  }
?>
