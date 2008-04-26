<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'net.xp_framework.db.caffeine.Rfc',
    'text.StreamTokenizer',
    'io.File',
    'io.streams.FileInputStream',
    'io.streams.FileInputStream',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NameMatchesFilter'
  );

  /**
   * Generate RFCs
   *
   * @purpose  Command
   */
  class GenerateRfcs extends Command {
    protected
      $origin = NULL,
      $filter = NULL;
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->filter= new NameMatchesFilter('/[0-9]{4}\.rfc/');
    }

    /**
     * Set directories to scan for RFCs
     *
     * @param   string dir
     */
    #[@arg]
    public function setOrigin($dir) {
      $this->origin= new FileCollection($dir);
    }
    
    /**
     * Set whether to filter
     *
     * @param   string pattern
     */
    #[@arg]
    public function setFilter($pattern= NULL) {
      $this->filter= new NameMatchesFilter('/'.$pattern.'/');
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      static $bz= array(
        'draft'       => 500,
        'discussion'  => 10000,
        'implemented' => 20000,
        'rejected'    => 30000,
        'obsoleted'   => 30001
      );

      $it= new FilteredIOCollectionIterator($this->origin, $this->filter);
      foreach ($it as $element) {
        with ($s= new FileInputStream(new File($element->getURI()))); {

          // Parse header
          $st= new StreamTokenizer($s, "\n");
          $caption= $st->nextToken();
          sscanf($caption, "RFC %04d: %[^\r]", $number, $title);
          
          $rfc= Rfc::getByRfc_id($number);
          $rfc || $rfc= new Rfc();
          $rfc->setRfc_id($number);
          $rfc->setTitle($title);

          $st->nextToken('@');
          while ($st->hasMoreTokens()) {
            if ('' === ($t= ltrim($st->nextToken(), '@'))) break;   // End of attributes

            sscanf($t, "%[^:]: %[^\r]", $key, $value);
            
            switch ($key) {
              case 'status':
                $rfc->setBz_id($bz[strtok($value, ',( ')]);
                $rfc->setStatus(strtok("\0"));
                break;
              
              case 'category':
                foreach (explode(', ', $value) as $category) {
                  if ('<' === $category{0}) {
                    // ...
                  } else {
                    // ...
                  }
                }
                break;
              
              case 'authors':
                $authors= explode(', ', $value);
                $rfc->setAuthor($authors[0]);
                foreach (array_slice($authors, 1) as $author) {
                  // $c->addChild(new Node('author', NULL, array('id' => $author)));
                }
                break;
              
              case 'created':
                $rfc->setCreated_at(Date::fromString($value));
                break;
            }
          }
          
          // Rest of text (use a token that is not very likely to appear so
          // scanning will happen in large chunks).
          $text= "\n";
          while ($st->hasMoreTokens("\0")) {
            $text.= $st->nextToken("\0");
          }
          $rfc->setContent($text);
          
          // Save
          $this->out->writef(
            '---> %s RFC #%04d: ',
            $rfc->isNew() ? 'Adding' : 'Updating',
            $rfc->getRfc_id()
          );
          $rfc->save();
          $this->out->writeLine('OK');

          $s->close();
        }
      }
    }
  }
?>
