<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'name.kiesel.pxl.scriptlet.wrapper.NewPageWrapper',
    'xml.parser.XMLParser',
    'text.parser.DateParser',
    'io.File',
    'io.Folder'
  );

  /**
   * Handler. <Add description>
   *
   * @purpose  <Add purpose>
   */
  class NewPageHandler extends Handler {

    /**
     * Constructor
     *
     */
    public function __construct() {
      parent::__construct();
      $this->setWrapper(new NewPageWrapper());
    }
    
    /**
     * Get identifier. 
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.Context context
     * @return  string
     */
    public function identifierFor($request, $context) {
      return $this->name.'#'.$request->getParam('page', 'new');
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function permaLink($page_id, $date, $title) {
      return sprintf('/story/%d/%04d/%02d/%02d/%s',
        $page_id,
        $date->toString('Y'),
        $date->toString('m'),
        $date->toString('d'),
        preg_replace('#[^a-zA-Z0-9\.\-\_]#', '_', $title)
      );
    }

    /**
     * Setup handler.
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function setup($request, $context) {
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      
      // Set old values if page parameter has been passed
      if ($request->hasParam('page')) {
        $page= $db->select('
            page_id,
            title,
            description,
            cast(published, "date") published
          from 
            page 
          where page_id= %d
          ',
          $request->getParam('page')
        );
        $tags= $db->select('tag from tag where page_id= %d', $request->getParam('page'));
        
        if (!sizeof($page)) throw new IllegalArgumentException('Given page not found.');
        $page= current($page);
        
        $this->setFormValue('name', $page['title']);
        $this->setFormvalue('description', $page['description']);
        if ($page['published'] instanceof Date) {
          $this->setFormValue('published', $page['published']->format('%Y-%m-%d'));
        }
        
        $tagstring= '';
        foreach ($tags as $t) { $tagstring.= $t['tag'].' '; }
        $this->setFormValue('tags', trim($tagstring));
        
        $this->setValue('mode', 'update');
        $this->setValue('page', $page);
      } else {
        $this->setValue('mode', 'create');

        // Find next "free" publishing date
        $lastdate= $db->select('
          cast(datetime(max(published), "+1 day"), "date") as published from page
        ');

        if (sizeof($lastdate) && is('util.Date', $lastdate[0]['published'])) {
          $this->setFormValue('published', $lastdate[0]);
        } else {
          $this->setFormvalue('published', Date::now()->format('%Y-%m-%d'));
        }
      }
      
      // Load tags
      $this->setValue('tags', $db->select('tag, count(*) as cnt from tag group by tag'));
      return TRUE;
    }
    
    /**
     * Handle submitted data.
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function handleSubmittedData($request, $context) {
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      $transaction= $db->begin(new Transaction('inspix'));

      try {
        $parser= new XMLParser();
        $parser->parse('<?xml version="1.0" encoding="iso-8859-15"?><document>'.$this->wrapper->getDescription().'</document>');
      } catch (XMLFormatException $e) {
        $this->addError('no-xml', 'description');
        return FALSE;
      }
      
      try {
        if ('create' == $this->getValue('mode')) {
          $seq= $db->select('max(sequence) as seq from page');
          $db->insert('
            into page (
              title,
              description,
              author_id,
              lastchange,
              changedby,
              published,
              sequence
            ) values (
              %s,
              %s,
              %d,
              %s,
              %s,
              %s,
              %d
            )',
            $this->wrapper->getName(),
            $this->wrapper->getDescription(),
            $context->user['author_id'],
            Date::now(),
            $context->user['username'],
            ($this->wrapper->getPublished() instanceof Date ? $this->wrapper->getPublished() : NULL),
            (int)$seq[0]['seq']+ 1
          );

          $page_id= $db->identity();
          
          // Calculate permalink
          if ($this->wrapper->getPublished() instanceof Date) {
            $db->update('
              page
              set
                permalink= %s
              where page_id= %d
              ',
              $this->permalink($page_id, $this->wrapper->getPublished(), $this->wrapper->getName()),
              $page_id
            );
          }

          // Create the new folder
          $folder= new Folder($request->getEnvValue('DOCUMENT_ROOT').DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.intval($page_id));
          $folder->create(0755);

          // Copy image file to new destination
          $this->wrapper->getFile()->getFile()->move($folder->getUri().'/'.$this->wrapper->getFile()->getName());

          $db->insert('
            into picture (
              page_id,
              filename,
              author_id
            ) values (
              %d,
              %s,
              %d
            )',
            $page_id,
            $this->wrapper->getFile()->getName(),
            $context->user['author_id']
          );
        } else {
          $page= $this->getValue('page');
          $page_id= $page['page_id'];
          
          // Update mode
          $db->update('
            page
            set
              title= %s,
              description= %s,
              lastchange= %s,
              changedby= %s,
              published= %s,
              permalink= %s
            where page_id= %d
            ',
            $this->wrapper->getName(),
            $this->wrapper->getDescription(),
            Date::now(),
            $context->user['username'],
            ($this->wrapper->getPublished() instanceof Date ? $this->wrapper->getPublished() : NULL),
            ($this->wrapper->getPublished() instanceof Date ? $this->permalink($page_id, $this->wrapper->getPublished(), $this->wrapper->getName()) : NULL),
            $page['page_id']
          );
          
          // Remove all tags, so we can re-insert them...
          $db->delete('from tag where page_id= %d', $page['page_id']);
        }

        foreach (array_unique(explode(' ', $this->wrapper->getTags())) as $tag) {
          strlen($tag) && $db->insert('into tag (page_id, tag) values (%d, %s)', $page_id, $tag);
        }
      } catch(SQLException $e) {
        Logger::getInstance()->getCategory()->error($e);
        $this->addError('database');
        $transaction->rollback();
        return FALSE;
      } catch(IOException $e) {
        Logger::getInstance()->getCategory()->error($e);
        $this->addError('permissions');
        $transaction->rollback();
        return FALSE;
      } catch(XPException $e) {
        $transaction->rollback();
        throw $e;
      }
      
      $transaction->commit();
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function finalize($request, $response, $context) {
      $response->forwardTo('admin/listpage?edit.success=1');
    }
  }
?>
