<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState', 
    'io.Folder', 
    'io.File',
    'xml.Tree',
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
      $metaInf= new Folder($base.DIRECTORY_SEPARATOR.$category.DIRECTORY_SEPARATOR.$project.DIRECTORY_SEPARATOR.'META-INF');
      if (!$metaInf->exists()) {
        throw new HttpScriptletException('Project "'.$projectId.'" not found', HTTP_NOT_FOUND);
      }
      
      with ($project= $response->addFormResult(new Node('project', NULL, array('id' => $projectId)))); {
        foreach (Tree::fromFile(new File($metaInf, 'project.xml'))->root->children as $child) {
          $project->addChild($child);
        }
      }
    }
  }
?>
