<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState', 
    'text.doclet.markup.MarkupBuilder',
    'text.doclet.markup.DelegatingProcessor',
    'io.File',
    'io.FileUtil',
    'util.PropertyManager'
  );

  /**
   * Handles /xml/home
   *
   * @purpose  State
   */
  class HomeState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_.]', $entry);
      $entry || $entry= 'home';
      
      // Read from storage (XXX: Make exchangeable)
      $text= FileUtil::getContents(new File(
        PropertyManager::getInstance()->getProperties('storage')->readString('text', 'base'),
        $entry.'.txt'
      ));
      
      $builder= new MarkupBuilder();
      
      // Add <summary>...</summary>
      $builder->registerProcessor('summary', newinstance('text.doclet.markup.DelegatingProcessor', array($builder->processors['default']), '{
        public function tag() { return "summary"; }
      }'));
      
      // Insert markup
      $response->addFormresult(new Node('documentation', new PCData(
        '<p>'.$builder->markupFor($text).'</p>'
      )));
    }
  }
?>
