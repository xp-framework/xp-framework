<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.xar.instruction.AbstractInstruction');

  /**
   * Merge Instruction
   *
   * @purpose  Merge
   */
  class MergeInstruction extends AbstractInstruction {

    /**
     * Execute action
     *
     * @return  int
     */
    public function perform() {
      $this->archive->open(ARCHIVE_CREATE);

      $args= $this->getArguments();
      foreach ($args as $arg) {
        $archive= new Archive(new File($arg));
        $archive->open(ARCHIVE_READ);

        while ($entry= $archive->getEntry()) {

          // Prevent overwriting earlier additions
          if ($this->archive->contains($entry)) {
            $this->err->writeLine('Warning: Duplicate entry "', $entry, '" from ', $archive->getURI(), ' - skipping.');
            continue;
          }

          $data= $archive->extract($entry);

          $this->options & Options::VERBOSE && $this->out->writeLinef('%10s %s', number_format(strlen($data), 0, FALSE, '.'), $entry);
          $this->archive->addBytes($entry, $data);
        }

        $archive->close();
      }

      // Create, if not in simulation mode
      if (!($this->options & Options::SIMULATE)) {
        $this->archive->create();
      } else {
        $this->archive->close();
      }
    }
  }
?>
