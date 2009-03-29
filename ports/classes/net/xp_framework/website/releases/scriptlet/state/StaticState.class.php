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
   * Static state
   *
   * @purpose  State
   */
  class StaticState extends AbstractState {
  
    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_]/%[a-zA-Z0-9._]', $base, $topic);
      $entry= $topic ? $base.DIRECTORY_SEPARATOR.$topic : $base;

      // Read from storage (XXX: Make exchangeable)
      try {
        $text= FileUtil::getContents(new File(
          PropertyManager::getInstance()->getProperties('storage')->readString('text', 'base'),
          $entry.'.txt'
        ));
      } catch (FileNotFoundException $e) {
        throw new HttpScriptletException('Entry "'.$entry.'" not found', HTTP_NOT_FOUND, $e);
      }
      
      $builder= new MarkupBuilder();
      
      // Add <summary>...</summary>
      $builder->registerProcessor('summary', newinstance('text.doclet.markup.DelegatingProcessor', array($builder->processors['default']), '{
        public function tag() { return "summary"; }
      }'));
      
      // Insert markup
      $n= $response->addFormresult(new Node('content', new PCData(
        '<p>'.$builder->markupFor($text).'</p>'
      )));
      $n->setAttribute('base', $base);
      $topic && $n->setAttribute('topic', $topic);
    }
  }
?>
