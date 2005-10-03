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
   * Mono Scriptlet.
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoScriptlet extends AbstractXMLScriptlet {

    /**
     * Create processor object
     *
     * @access  protected
     * @return  &xml.DomXSLProcessor
     */
    function &_processor() {
      $p= &new DomXSLProcessor();
      return $p;
    }
      
    /**
     * Sets the stylesheet path.
     *
     * @access  protected
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function _setStylesheet(&$request, &$response) {
      $response->setStylesheet(sprintf(
        '%s/%s.xsl',
        $request->getProduct(),
        $request->getStateName()
      ));
    }
    
    /**
     * Handles the doCreate()-request. This request will only
     * occur when the site is being opened without the rewrite
     * rule for default access.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @return  bool
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
