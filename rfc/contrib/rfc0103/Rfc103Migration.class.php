<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'io.File',
    'io.FileUtil',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );

  /**
   * RFC #0103 Migration.
   *
   * <ul>
   *   <li>Removes all reference operators</li>
   *   <li>Removes apidoc tags "access" and "model"</li>
   * </ul>
   *
   * @purpose  RFC #0103 automatic migration
   */
  class Rfc103Migration extends Command {
    var 
      $iterator= NULL;
    
    /**
     * Set collection
     *
     * @param  string name
     */
    #[@arg(position= 0)]
    public function setCollection($name) {
      $this->iterator= new FilteredIOCollectionIterator(
        new FileCollection($name),
        new ExtensionEqualsFilter('.php'),
        TRUE
      );
    }

    /**
     * Runs this command
     *
     * @return  string
     */
    public function run() {
      while ($this->iterator->hasNext()) {
        $file= new File($this->iterator->next()->getURI());
        $this->out->writeLine('- ', $file->toString());
        
        // Extract tokens
        $tokens= token_get_all(FileUtil::getContents($file));
        
        // Write back all tokens, omitting reference operators
        $file->open(FILE_MODE_WRITE);
        foreach ($tokens as $i => $token) {
          if (T_DOC_COMMENT == $token[0]) {
            $file->write(preg_replace(array(
              '/ +\* @access[^\r\n]+[\r\n]/', '/ +\* @model[^\r\n]+[\r\n]/',
            ), array(
              '', '',
            ), $token[1]));
          } else if (is_array($token)) {
            $file->write($token[1]);
          } else if ('&' == $token) {
            if (T_WHITESPACE == $tokens[$i+ 1][0]) $file->write($token);
          } else {
            $file->write($token);
          }
        }
        $file->close();
      }
    }
  }
?>
