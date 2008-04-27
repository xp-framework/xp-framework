<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'net.xp_framework.db.caffeine.Rfc',
    'net.xp_framework.db.caffeine.Person',
    'net.xp_framework.db.caffeine.Contributor',
    'text.StreamTokenizer',
    'io.File',
    'lang.ElementNotFoundException',
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
    public function setFilter($pattern= '[0-9]{4}\.rfc') {
      $this->filter= new NameMatchesFilter('/'.$pattern.'/');
    }
    
    /**
     * Lookup a person
     *
     * @param   string cn
     * @return  net.xp_framework.db.caffeine.Person
     */
    protected function personByCn($cn) {
      if (!($person= Person::getByCn($cn))) {
        throw new ElementNotFoundException('Cannot find person with cn "'.$cn.'"');
      }
      return $person;
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
                $rfc->setCategories($value);
                break;
              
              case 'authors':
                $authors= explode(', ', $value);
                $rfc->setAuthor_id($this->personByCn($authors[0])->getPerson_id());
                
                $contributors= array();
                foreach (array_slice($authors, 1) as $contributor) {
                  $contributors[]= $this->personByCn($contributor)->getPerson_id();
                }
                break;
              
              case 'created':
                $rfc->setCreated_at(Date::fromString($value));
                break;
            }
          }
          
          // Scope of change
          $text= '';
          while ($st->hasMoreTokens("\n")) {
            $token= $st->nextToken("\n");
            if (0 === strncmp('Rationale', $token, 9)) break;
            $text.= $token."\n";
          }
          $rfc->setScope(trim($text));
          
          // Rest of text (use a token that is not very likely to appear so
          // scanning will happen in large chunks).
          $text= $token."\n";
          while ($st->hasMoreTokens("\0")) {
            $text.= $st->nextToken("\0");
          }
          $rfc->setContent(trim($text));
          
          // Save
          $this->out->writef(
            '---> %s RFC #%04d: ',
            $rfc->isNew() ? 'Adding' : 'Updating',
            $rfc->getRfc_id()
          );
          
          try {
            $tran= Rfc::getPeer()->begin(new Transaction());
            $rfc->save();

            $current= array();
            foreach (Contributor::getPeer()->doSelect(create(new Criteria())->add('rfc_id', $rfc->getRfc_id(), EQUAL)) as $c) {
              $current[]= $c->getPerson_id();
            }
            foreach (array_diff($contributors, $current) as $add) {
              $contributor= new Contributor();
              $contributor->setRfc_id($rfc->getRfc_id());
              $contributor->setPerson_id($add);
              $contributor->insert();
            }
            foreach (array_diff($current, $contributors) as $delete) {
              Contributor::getByPerson_id($delete)->delete();
            }
            $tran->commit();
            $this->out->writeLine('OK');
          } catch (SQLException $e) {
            $this->err->writeLine($e->compoundMessage());
            $tran && $tran->rollback();
          }

          $s->close();
        }
      }
    }
  }
?>
