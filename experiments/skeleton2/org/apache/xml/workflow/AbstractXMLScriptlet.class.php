<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.XMLScriptlet',
    'org.apache.xml.workflow.Context',
    'org.apache.xml.workflow.AuthContext'
  );

  /**
   * (Insert class' description here)
   *
   * <code>
   *   uses('org.apache.xml.workflow.AbstractXMLScriptlet');
   *   
   *   $s= new AbstractXMLScriptlet(new ClassLoader('de.abi-time.scriptlet'), '../xsl/');
   *   try(); {
   *     $s->init();
   *     $response= $s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *   
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= $e->getResponse(); 
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
    public
      $needsSession = TRUE,
      $classloader  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   string xslbase
     */
    public function __construct(&$classloader, $xslbase) {
      $this->classloader= $classloader;
      parent::__construct($xslbase);
    }
    
    public function createContext($class) {
      if (!is_a($class, 'XPClass')) {
        return new Context(); 
      }
      return $class->newInstance();
    }
    
    /**
     * Create session and set up context
     *
     * @access  protected
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @return  bool
     */
    protected function doCreateSession(&$request, &$response) {
      $context= new Context();
      $context->initialize($this->classloader, $request);
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
    protected function doGet(&$request, &$response) {
    
      // Map states such as "auth/welcome" to AuthContext / WelcomeState
      $cname= '';
      if (FALSE !== ($p= strpos($request->getState(), '/'))) {
        $cname= substr($request->getState(), 0, $p);
        $request->setState(substr($request->getState(), $p+ 1));
      }
      
      // Get context 
      try {
        if (!($context= $request->session->getValue($cname.'context'))) {
          $context= self::createContext(XPClass::forName(
            'org.apache.xml.workflow.'.ucfirst($cname).'Context'          
          ));
          $context->initialize($this->classloader, $request);
          $request->session->putValue($cname.'context', $context);
        }
      } catch (XPException $e) {
        throw (new HttpScriptletException('Internal error: '.$e->message));
      }
      
      try {
        do {
          if (!($context= $request->session->getValue('context'))) break;
          $context->handleRequest($request, $response);
          $request->session->putValue('context', $context);
        } while (0);
      } catch (ContextFailedException $e) {
        throw (new HttpScriptletException('Could not execute request: '.$e->message));
      } catch (XPException $e) {
        throw (new HttpScriptletException('Internal error: '.$e->message));
      }

      return parent::doGet($request, $response);
    }
  }
?>
