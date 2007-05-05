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
     * Setup handler.
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function setup($request, $context) {
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      
      // Find next "free" publishing date
      $lastdate= $db->select('
        cast(datetime(max(published), "+1 day"), "date") as published from page
      ');
      
      if (sizeof($lastdate) && is('util.Date', $lastdate[0]['published'])) {
        $this->setFormValue('published', $lastdate[0]['published']->format('%Y-%m-%d'));
      } else {
        $this->setFormvalue('published', Date::now()->format('%Y-%m-%d'));
      }
      
      // Load tags
      $this->setValue('tags', $db->select('distinct tag from tag'));
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
        $seq= $db->select('max(sequence) as seq from page');
        $db->insert('
          into page (
            title,
            description,
            author_id,
            lastchange,
            published,
            sequence
          ) values (
            %s,
            %s,
            %d,
            %s,
            %s,
            %d
          )',
          $this->wrapper->getName(),
          $this->wrapper->getDescription(),
          $context->user['author_id'],
          Date::now(),
          (is('util.Date', $this->wrapper->getPublished()) ? $this->wrapper->getPublished() : NULL),
          (int)$seq[0]['seq']+ 1
        );

        $page= $db->identity();

        // Create the new folder
        $folder= new Folder($request->getEnvValue('DOCUMENT_ROOT').DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.intval($page));
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
          $page,
          $this->wrapper->getFile()->getName(),
          $context->user['author_id']
        );

        foreach (array_unique(explode(' ', $this->wrapper->getTags())) as $tag) {
          strlen($tag) && $db->insert('into tag (page_id, tag) values (%d, %s)', $page, $tag);
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
  }
?>
