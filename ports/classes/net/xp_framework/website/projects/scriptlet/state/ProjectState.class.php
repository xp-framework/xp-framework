<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState', 
    'io.Folder', 
    'io.File',
    'io.FileUtil',
    'xml.Tree',
    'text.doclet.markup.MarkupBuilder',
    'text.doclet.markup.DelegatingProcessor',
    'util.PropertyManager'
  );

  /**
   * Handles /xml/project
   *
   * @purpose  State
   */
  class ProjectState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_]/%[a-zA-Z_]', $category, $project);
      $projectId= $category.'/'.$project;

      // Read from storage (XXX: Make exchangeable)
      $base= PropertyManager::getInstance()->getProperties('storage')->readString('projects', 'base');
      $projectDir= new Folder($base.DIRECTORY_SEPARATOR.$category.DIRECTORY_SEPARATOR.$project.DIRECTORY_SEPARATOR);
      if (!$projectDir->exists()) {
        throw new HttpScriptletException('Project "'.$projectId.'" not found', HTTP_NOT_FOUND);
      }

      // Project information      
      with ($project= $response->addFormResult(new Node('project', NULL, array('id' => $projectId)))); {
        foreach (Tree::fromFile(new File($projectDir, 'META-INF/project.xml'))->root->children as $child) {
          $project->addChild($child);
        }
      }
      
      // Site
      try {
        $text= FileUtil::getContents(new File($projectDir, 'WEB-INF/overview.txt'));
      } catch (FileNotFoundException $e) {
        throw new HttpScriptletException('Entry "'.$entry.'" not found', HTTP_NOT_FOUND, $e);
      }

      
      $builder= new MarkupBuilder();

      // Add <summary>...</summary>
      $builder->registerProcessor('summary', newinstance('text.doclet.markup.DelegatingProcessor', array($builder->processors['default']), '{
        public function tag() { return "summary"; }
      }'));
      $builder->registerProcessor('item', newinstance('text.doclet.markup.DelegatingProcessor', array($builder->processors['default']), '{
        public function tag() { return "item"; }
      }'));
      
      $response->addFormResult(new Node('content', new PCData(
        '<p>'.$builder->markupFor($text).'</p>'
      )));
    }
  }
?>
