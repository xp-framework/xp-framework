<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'remote.Remote'
  );

  /**
   * Handles /xml/search
   *
   * @purpose  State
   */
  class SearchState extends AbstractState {
    const
      PAGER_LENGTH      = 10;

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      $query= $request->getParam('query');
      $offset= $request->getParam('offset', 0);
      
      $s= Remote::forName('xp://planet-xp.net:14446')->lookup('lucene/Search');

      try {
        $id= $s->search('all', $query);
        $iterator= $s->getHitsIterator($id);
        $list= $iterator->getRange((int)$offset, self::PAGER_LENGTH);
      } catch (RemoteException $e) {

        // Fall through
      } finally(); {
        isset($id) && $s->removeIterator($id);
        if (isset($e)) throw new HttpScriptletException('Search failed', HTTP_SERVICE_TEMPORARILY_UNAVAILABLE, $e);
      }
      
      $n= $response->addFormResult(new Node('searchresult', NULL, array(
        'count' => $iterator->length()
      )));
      foreach ($list->values as $item) {
        $i= $n->addChild(new Node('item', NULL, array(
          'id'      => $item->getIdentifier(),
          'type'    => $item->getType()
        )));
        foreach ($item->fields as $name => $field) {
          $i->addChild(new Node($name, $field['value']));
        }
      }
      
      // Add pager element
      $pager= array(
        'offset'  => $offset,
        'prev'    => max(0, $offset- self::PAGER_LENGTH)
      );
      if ($list->length >= self::PAGER_LENGTH) {
        $pager['next']= $offset+ self::PAGER_LENGTH;
      }
      $response->addFormResult(new Node('pager', NULL, $pager));
    }
  }
?>
