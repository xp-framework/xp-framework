<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState'
  );

  /**
   * Handles /xml/lookup
   *
   * @purpose  State
   */
  class LookupState extends AbstractState {

    /**
     * Process this state. Redirects to known targets or invokes the 
     * search if the lookup fails.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
      with ($query= $request->getParam('q', $request->getData()), $uri= $request->getURI()); {
        $target= 'search?q='.$query;
        
        // HACK until build system infrastructure provides a better method
        if (file_exists('../build/cache/class.php/'.basename($query))) {
          $target= 'documentation/class?core/'.$query;
        }

        $response->sendRedirect(sprintf(
          '%s://%s/xml/%s.%s%s/%s', 
          $uri['scheme'],
          $uri['host'],          
          $request->getProduct(),
          $request->getLanguage(),
          $request->session ? '.psessionid='.$request->session->getId() : '',
          $target
        ));
        return FALSE; // Indicate no further processing is to be done
      }
    }
  }
?>
