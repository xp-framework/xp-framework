<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'io.File',
    'xml.Tree',
    'text.doclet.markup.MarkupBuilder'
  );

  /**
   * Generates change logs
   *
   * @purpose  Command
   */
  class GenerateChangeLogs extends Command {
    protected
      $changelog  = NULL;
    
    /**
     * Set changelog
     *
     * @param   string changelog file
     */
    #[@arg(position= 0)]
    public function setChangelog($changelog) {
      $this->changelog= new File($changelog);
      $this->changelog->open(FILE_MODE_READ);
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      $t= new Tree('releases');
      $builder= new MarkupBuilder();
      $line= '';
      $series= array(
        NULL => $t->addChild(new Node('future'))
      );
      while (!$this->changelog->eof()) {
        if (2 == sscanf($line, 'Version %[^,], released %[?0-9-]', $version, $date)) {
          if (strstr($version, '?')) {
            $s= NULL;
          } else {
            sscanf($version, '%d.%d.%d', $major, $minor, $patch);
            $s= $major.'.'.$minor;
          }
          if (!isset($series[$s])) {
            $series[$s]= $t->addChild(new Node('series', NULL, array('id' => $s)));
          }
          
          $r= $series[$s]->addChild(new Node('release'));
          $r->setAttribute('id', $version);
          $r->setAttribute('date', $date);

          // Skip over "----" line
          $this->changelog->readLine();

          // Extract revision
          sscanf($this->changelog->readLine(), 'SVN version: %d', $rev);
          $r->setAttribute('revision', $rev);
          $notes= '';

          // Read notes
          while (!$this->changelog->eof()) {
            $line= $this->changelog->readLine();
            if (2 == sscanf($line, 'Version %[^,], released %[?0-9-]', $version, $date)) break;
            $notes.= $line."\n";
          }

          $r->addChild(new Node('notes', new PCData(
            '<p>'.$builder->markupFor(str_replace("\n- ", "\n* ", rtrim($notes, "\r\n"))).'</p>'
          )));
          continue;
        }
        $line= $this->changelog->readLine();
      }    
      $this->changelog->close();
      $this->out->writeLine($t->getSource(INDENT_DEFAULT));
    }
  }
?>
