<?php
/* This class is part of the XP framework
 *
 * $Id: StaticState.class.php 5509 2005-08-07 17:15:07Z friebe $ 
 */

  namespace de::thekid::dialog::scriptlet::state;

  ::uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/collection/view
   *
   * @purpose  State
   */
  class ViewCollectionState extends de::thekid::dialog::scriptlet::AbstractDialogState {
    public
      $nodeHandlers = array();
  
    /**
     * Constructor
     *
     */
    public function __construct() {
      for ($c= $this->getClass(), $m= $c->getMethods(), $i= 0, $s= sizeof($m); $i < $s; $i++) {
        $m[$i]->hasAnnotation('handles') && (
          $this->nodeHandlers[$m[$i]->getAnnotation('handles')]= $m[$i]
        );
      }
    }
    
    /**
     * Handler for albums
     *
     * @param   &de.thekid.dialog.Album album
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.Album')]
    public function albumNode($album) {
      $child= new ('entry', NULL, array(
        'name'          => $album->getName(),
        'title'         => $album->getTitle(),
        'num_images'    => $album->numImages(),
        'num_chapters'  => $album->numChapters()
      ));
      $child->addChild(new ('description', new ($album->getDescription())));
      $child->addChild(::fromObject($album->createdAt, 'created'));
      $child->addChild(::fromArray($album->highlights, 'highlights'));
      
      return $child;
    }

    /**
     * Handler for updates
     *
     * @param   &de.thekid.dialog.Update update
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.Update')]
    public function updateNode($update) {
      $child= new ('entry', NULL, array(
        'album'         => $update->getAlbumName(),
        'title'         => $update->getTitle()
      ));
      $child->addChild(new ('description', new ($update->getDescription())));
      $child->addChild(::fromObject($update->date, 'date'));
      
      return $child;
    }

    /**
     * Handler for single shots
     *
     * @param   &de.thekid.dialog.SingleShot shot
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.SingleShot')]
    public function shotNode($shot) {
      $child= new ('entry', NULL, array(
        'name'      => $shot->getName(),
        'filename'  => $shot->getFileName(),
        'title'     => $shot->getTitle()
      ));
      $child->addChild(new ('description', new ($shot->getDescription())));
      $child->addChild(::fromObject($shot->date, 'date'));
      
      return $child;
    }

    /**
     * Handler for entry collections
     *
     * @param   &de.thekid.dialog.EntryCollection collection
     * @return  &xml.Node node
     */
    #[@handles('de.thekid.dialog.EntryCollection')]
    public function collectionNode($collection) {
      $numEntries= $collection->numEntries();
      $node= new ('entry', NULL, array(
        'name'          => $collection->getName(),
        'title'         => $collection->getTitle(),
        'num_entries'   => $numEntries
      ));
      $node->addChild(new ('description', new ($collection->getDescription())));
      $node->addChild(::fromObject($collection->createdAt, 'created'));
      
      for ($i= 0; $i < $numEntries; $i++) {
        $entry= $collection->entryAt($i);
        if (!isset($this->nodeHandlers[$entry->getClassName()])) {
          throw(new lang::FormatException('Index contains unknown element "'.$entry->getClassName().'"'));
        }
        
        $child= $node->addChild($this->nodeHandlers[$entry->getClassName()]->invoke($this, array($entry)));
        $child->setAttribute('type', $entry->getClassName());
      }
      
      return $node;
    }

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    public function process($request, $response, $context) {
      $name= $request->getQueryString();

      if ($collection= $this->getEntryFor($name)) {
        $child= $response->addFormResult(new ('collection', NULL, array(
          'name'         => $collection->getName(),
          'title'        => $collection->getTitle(),
          'page'         => $this->getDisplayPageFor($name)
        )));
        $child->addChild(new ('description', new ($collection->getDescription())));
        $child->addChild(::fromObject($collection->createdAt, 'created'));
      	
        // Add entries from collection 
        $node= $response->addFormResult(new ('entries'));
        foreach ($collection->entries as $entry) {
          if (!isset($this->nodeHandlers[$entry->getClassName()])) {
            throw(new lang::FormatException('Index contains unknown element "'.$entry->getClassName().'"'));
          }

          $child= $node->addChild($this->nodeHandlers[$entry->getClassName()]->invoke($this, array($entry)));
          $child->setAttribute('type', $entry->getClassName());
        }
      }
    }
  }
?>
