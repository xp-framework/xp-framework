<?php
  uses('io.File');
  
  /**
   * (Insert method's description here)
   *
   * @access  public
   * @see     http://wp.netscape.com/eng/mozilla/2.0/relnotes/demo/content-length.html
   * @see     http://www.qmail.org/man/man5/mbox.html
   * @see      
   */
  class MBox extends Object {
    var $_fd= NULL;
	
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function __construct($filename) {
	  $this->_fd= &new File($filename);
	  parent::__construct();
	}
	
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function __destruct($filename) {
	  $this->_fd->__destruct();
	  parent::__destruct();
	}
	
	
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function &getFirstMail() {
	  $this->mails= 0;
	  return $this->getNextMail();
	}
	
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function &getNextMail() {
	  static $body= FALSE;

      $mail= FALSE;
	  $lines= 0;
	  while (!$this->_fd->eof() && FALSE !== ($line= $this->_fd->readLine())) {
	    if ('From ' == substr($line, 0, 5)) {
		  if ($body == TRUE) {
 		    $body= FALSE;
		    $this->_fd->seek(-1* (strlen($line)+ 1), SEEK_CUR);
		    break;
		  }
		  
		  $mail= &new stdClass();
		  $mail->msgNumber= $this->mails+ 1;
		  $mail->header= array();
		  continue;
		}
		if ($body == TRUE) {
		  $lines++;
		  continue;
        }
		
		if ('' == $line) {
		  $mail->bodyStart= $this->_fd->tell();
		  $body= TRUE;
		  continue;
		}

		if ("\t" != $line{0}) {
		  list($header, $value)= explode(': ', $line, 2);
		  $mail->header[$header]= '';
		} else {
		  $value= "\n".$line;
		}
		$mail->header[$header].= $value;
	  }
	  
	  // We have a mail
      if (FALSE !== ($mail)) {
		$mail->bodyEnd= $this->_fd->tell()- 1;
		$mail->lines= $lines- 1;
		$mail->bytes= $mail->bodyEnd- $mail->bodyStart;
		$this->mails++;
      }
	  return $mail;
    }
	
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function getBody(&$mail) {
	
	  // Remember file pointer's offset
	  $offset= $this->_fd->tell();
	  
	  $this->_fd->seek($mail->bodyStart, SEEK_SET);
	  $contents= $this->_fd->read($mail->bodyEnd- $mail->bodyStart);
	  
	  // Restore previous position
	  $this->_fd->seek($offset, SEEK_SET);
	  
	  return $contents;
	}

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function open($mode= FILE_MODE_READ) {
	  if (!$this->_fd->isOpen()) {
	    $this->_fd->open($mode);
      } else {
  	    $this->_fd->seek(0);
	  }
	}
	
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
	function close() {
	  $this->_fd->close();
	}
  }
  
  /*
  require('lang.base.php');
  $mbox= &new MBox('/var/mail/thekid');
  try(); {
	$mbox->open();
	$mail= &$mbox->getFirstMail();
	do {
      printf(
		"%2d %2s %s %-30s (%4d) %s\n",
		$mail->msgNumber,
		@$mail->header['Status'],
		date('M d H:i', strtotime(@$mail->header['Date'])),
		@$mail->header['To'],
		@$mail->header['Lines'],
		@$mail->header['Subject']
	  );

	  // Show mail #7
	  if (7 == $mail->msgNumber) {
		echo substr(preg_replace('#^|\n#', "\n      |", $mbox->getBody($mail)), 1)."\n";
	  }
	} while (FALSE !== ($mail= $mbox->getNextMail()));
	$mbox->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
  }
  
  echo "\n";
  */
?>
