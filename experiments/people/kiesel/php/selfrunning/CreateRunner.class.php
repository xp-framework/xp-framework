<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'util.cmd.Command',
    'io.File',
    'io.FileUtil',
    'io.TempFile',
    'lang.archive.Archive'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class CreateRunner extends Command {
    var
      $outputFile=  '',
      $inputFile=   '',
      $langBase=    '';

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(name= 'base')]
    public function setLangBase($b= NULL) {
      if (NULL === $b) {
        foreach (get_required_files() as $f) {
          if (substr($f, -13) == 'lang.base.php') {
            $this->langBase= $f;
            return;
          }
        }
      }
      
      $this->langBase= $b;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(name= 'output')]
    public function setOutputFile($o) {
      $this->outputFile= $o;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(name= 'xar')]
    public function setInputFile($i) {
      $this->inputFile= $i;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(name= 'main')]
    public function setMainClass($m) {
      $this->mainClass= $m;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */    
    protected function classToFilename($f) {
      return strtr($f, '.', '/').'.class.php';
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run() {
      // Open input xar
      $source= new Archive(new File($this->inputFile));
      $source->open(ARCHIVE_READ);
      
      if (!$source->contains($this->classToFilename($this->mainClass))) {
        $this->err->writeLinef('Class "%s" not found in source archive.', $this->mainClass);
        return 1;
      }
      
      // Open temporary xar
      $temp= new Archive(new TempFile());
      $temp->open(ARCHIVE_CREATE);
      $this->out->writeLine('===> Creating temporary archive:', $temp);
      
      while ($entry= $source->getEntry()) {
        $temp->addFileBytes(
          $entry,
          '',
          '',
          $source->extract($entry)
        );
      }
      
      // HACK, to be removed later
      $temp->addFileBytes(
        'lang/archive/ArchiveSelfRunner.class.php',
        'lang/archive/',
        'ArchiveSelfRunner.class.php',
        FileUtil::getContents(new File('overrides/lang/archive/ArchiveSelfRunner.class.php'))
      );
      
      // Hack, to be removed later
      $temp->addFileBytes(
        'net/xp_framework/experiments/ShowContents.class.php',
        'net/xp_framework/experiments/',
        'ShowContents.class.php',
        FileUtil::getContents(new File('cmd/ShowContents.class.php'))
      );
      
      $this->out->writeLine('===> Creating META-INF directory');
      $temp->addFileBytes(
        'META-INF/runner.ini',
        'META-INF',
        'runner.ini',
        sprintf('; Runner file, created by %s
;
; $Id$
;

[main]
class="%s"
',
        $this->getClassName(),
        $this->mainClass
      ));
      $temp->create();
      
      // Final xar must contain lang.base.php
      $this->out->writeLine('===> Creating final xar');
      $target= new File($this->outputFile);
      $target->open(FILE_MODE_WRITE);
      
      $target->writeLine(substr(
        trim(FileUtil::getContents(new File($this->langBase))),
        0,
        -2  // Remove ? >
      ));
      $target->write('__halt_compiler();');
      
      $target->write(FileUtil::getContents($temp->file));
      $target->close();
      
      $this->out->writeLine('===> Done.');
      return 0;
    }
  }
?>
