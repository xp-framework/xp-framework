<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/collection/view
   *
   * @purpose  State
   */
  class ViewCollectionState extends AbstractDialogState {
    public
      $nodeHandlers = array();
  
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      for ($c= &$this->getClass(), $m= $c->getMethods(), $i= 0, $s= sizeof($m); $i < $s; $i++) {
        $m[$i]->hasAnnotation('handles') && (
          $this->nodeHandlers[$m[$i]->getAnnotation('handles')]= &$m[$i]
        );
      }
    }
    
    /**
     * Handler for albums
     *
     * @access  public
     * @param   &de.thekid.dialog.Album album
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.Album')]
    public function &albumNode(&$album) {
      $child= new Node('entry', NULL, array(
        'name'          => $album->getName(),
        'title'         => $album->getTitle(),
        'num_images'    => $album->numImages(),
        'num_chapters'  => $album->numChapters()
      ));
      $child->addChild(new Node('description', new PCData($album->getDescription())));
      $child->addChild(Node::fromObject($album->createdAt, 'created'));
      $child->addChild(Node::fromArray($album->highlights, 'highlights'));
      
      return $child;
    }

    /**
     * Handler for updates
     *
     * @access  public
     * @param   &de.thekid.dialog.Update update
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.Update')]
    public function &updateNode(&$update) {
      $child= new Node('entry', NULL, array(
        'album'         => $update->getAlbumName(),
        'title'         => $update->getTitle()
      ));
      $child->addChild(new Node('description', new PCData($update->getDescription())));
      $child->addChild(Node::fromObject($update->date, 'date'));
      
      return $child;
    }

    /**
     * Handler for single shots
     *
     * @access  public
     * @param   &de.thekid.dialog.SingleShot shot
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.SingleShot')]
    public function &shotNode(&$shot) {
      $child= new Node('entry', NULL, array(
        'name'      => $shot->getName(),
        'filename'  => $shot->getFileName(),
        'title'     => $shot->getTitle()
      ));
      $child->addChild(new Node('description', new PCData($shot->getDescription())));
      $child->addChild(Node::fromObject($shot->date, 'date'));
      
      return $child;
    }

    /**
     * Handler for entry collections
     *
     * @access  public
     * @param   &de.thekid.dialog.EntryCollection collection
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.EntryCollection')]
    public function &collectionNode(&$collection) {
      $numEntries= $collection->numEntries();
      $node= new Node('entry', NULL, array(
        'name'          => $collection->getName(),
        'title'         => $collection->getTitle(),
        'num_entries'   => $numEntries
      ));
      $node->addChild(new Node('description', new PCData($collection->getDescription())));
      $node->addChild(Node::fromObject($collection->createdAt, 'created'));
      
      for ($i= 0; $i < $numEntries; $i++) {
        $entry= &$collection->entryAt($i);
        if (!isset($this->nodeHandlers[$entry->getClassName()])) {
          throw(new FormatException('Index contains unknown element "'.$entry->getClassName().'"'));
        }
        
        $child= &$node->addChild($this->nodeHandlers[$entry->getClassName()]->invoke($this, array($entry)));
        $child->setAttribute('type', $entry->getClassName());
      }
      
      return $node;
    }

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    public function process(&$request, &$response, &$context) {
      $name= $request->getQueryString();

      if ($collection= &$this->getEntryFor($name)) {
        $child= &$response->addFormResult(new Node('collection', NULL, array(
          'name'         => $collection->getName(),
          'title'        => $collection->getTitle(),
          'page'         => $this->getDisplayPageFor($name)
        )));
        $child->addChild(new Node('description', new PCData($collection->getDescription())));
        $child->addChild(Node::fromObject($collection->createdAt, 'created'));
      	
        // Add entries from collection 
        $node= &$response->addFormResult(new Node('entries'));
        foreach ($collection->entries as $entry) {
          if (!isset($this->nodeHandlers[$entry->getClassName()])) {
            throw(new FormatException('Index contains unknown element "'.$entry->getClassName().'"'));
          }

          $child= &$node->addChild($this->nodeHandlers[$entry->getClassName()]->invoke($this, array($entry)));
          $child->setAttribute('type', $entry->getClassName());
        }
      }
    }
  }
?>
