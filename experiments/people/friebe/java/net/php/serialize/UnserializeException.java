/* This class is part of the XP framework's Java extension
 *
 * $Id$ 
 */

  package net.php.serialize;

  import java.io.PrintStream;
  import java.io.PrintWriter;

  /**
   * Indicates an error occured during unserialization
   *
   */
  class UnserializeException extends Exception {
    public Throwable cause= null;

    /**
     * Constructor
     *
     * @access  public
     * @param   String msg
     * @param   Throwable cause
     */
    public UnserializeException(String msg, Throwable cause) {
      super(msg);
      this.cause= cause;
    }

    /**
     * Constructor
     *
     * @access  public
     * @param   Throwable cause
     */
    public UnserializeException(Throwable cause) {
      super(cause.getMessage());
      this.cause= cause;
    }
    
    /**
     * Prints stacktrace
     *
     * @access  public
     * @see     java://lang.Exception#printStackTrace
     */
    public void printStackTrace() {
      super.printStackTrace();
      cause.printStackTrace();
    }

    /**
     * Prints stacktrace
     *
     * @access  public
     * @param   PrintStream stream
     * @see     java://lang.Exception#printStackTrace
     */
    public void printStackTrace(PrintStream stream) {
      super.printStackTrace(stream);
      cause.printStackTrace(stream);
    }

    /**
     * Prints stacktrace
     *
     * @access  public
     * @param   PrintWriter writer
     * @see     java://lang.Exception#printStackTrace
     */
    public void printStackTrace(PrintWriter writer) {
      super.printStackTrace(writer);
      cause.printStackTrace(writer);
    }
  }
