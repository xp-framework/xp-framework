<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.FileUtil',
    'xml.DomXSLProcessor',
    'de.document-root.mono.MonoCatalog',
    'scriptlet.xml.workflow.AbstractXMLScriptlet'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoScriptlet extends AbstractXMLScriptlet {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &_processor() {
      $p= &new DomXSLProcessor();
      return $p;
    }
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _setStylesheet(&$request, &$response) {
      $response->setStylesheet(sprintf(
        '%s/%s.xsl',
        $request->getProduct(),
        $request->getStateName()
      ));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function doCreate(&$request, &$response) {
    
      // Find latest shot
      try(); {
        $catalog= unserialize(FileUtil::getContents(
          new File(sprintf('%s/../data/dates.idx', 
            rtrim($_SERVER['DOCUMENT_ROOT'], '/'))
        )));
        
        if (
          !is('de.document-root.mono.MonoCatalog', $catalog) ||
          $catalog->getCurrent_id() == 0
        ) {
          return throw(new IllegalStateException(
            'This mono has not yet been set up.'
          ));
        }
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      sscanf($catalog->dateFor($catalog->getCurrent_id()), '%4d/%2d/%2d', $y, $m, $d);
      $uri= $request->getUri();
      $response->sendRedirect(sprintf('%s://%s/%04d/%02d/%02d',
        $uri['scheme'],
        $uri['host'],
        $y,
        $m,
        $d
      ));
      
      return FALSE;
    }
  }
?>
