<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState', 
    'util.PropertyManager',
    'io.FileUtil',
    'io.File',
    'io.Folder',
    'text.doclet.markup.MarkupBuilder',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NegationOfFilter',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.NameMatchesFilter',
    'util.MimeType'
  );

  /**
   * Handles /xml/browse
   *
   * @purpose  State
   */
  class BrowseState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      $path= $request->getQueryString();

      $prop= PropertyManager::getInstance()->getProperties('storage');
      $base= new Folder($prop->readString('storage', 'base').DIRECTORY_SEPARATOR.strtr($path, array(
        ','   => DIRECTORY_SEPARATOR, 
        '..'  => ''
      )));
      if (!$base->exists()) {
        throw new HttpScriptletException('Path "'.$path.'" does not exist', HTTP_NOT_FOUND);
      }

      $excludes= array();
      foreach ($prop->readArray('storage', 'excludes') as $pattern) {
        $excludes[]= new NameMatchesFilter('/'.$pattern.'/i');
      }
      
      $i= new FilteredIOCollectionIterator(new FileCollection($base->getUri()), new NegationOfFilter(new AnyOfFilter($excludes)));
      $n= $response->addFormResult(new Node('list', NULL, array(
        'path' => $path
      )));

      // Add list of elements
      foreach ($i as $element) {
        $name= basename($element->getUri());

        if ($element instanceof IOCollection) {
          $e= $n->addChild(new Node('collection'));
        } else {
          $e= $n->addChild(new Node('element'));
          $e->addChild(new Node('mime', MimeType::getByFilename($element->getUri())));

          if ('README' === $name) {
            $builder= new MarkupBuilder();
            $response->addFormresult(new Node('readme', new PCData(
              '<p>'.$builder->markupFor(FileUtil::getContents(new File($element->getUri()))).'</p>'
            )));
          }
        }

        $e->addChild(new Node('name', basename($element->getUri())));
        $e->addChild(Node::fromObject($element->lastModified(), 'modified'));
      }
    }
  }
?>
