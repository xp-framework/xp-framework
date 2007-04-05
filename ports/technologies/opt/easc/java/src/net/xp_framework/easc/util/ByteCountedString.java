/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.util;

import java.io.DataOutput;
import java.io.DataInput;
import java.io.IOException;
import java.io.ByteArrayOutputStream;

import java.nio.charset.Charset;
import java.nio.CharBuffer;
import java.nio.ByteBuffer;
import java.nio.charset.CharacterCodingException;

import static java.lang.Math.ceil;

/**
 * Byte counted string. The layout is the following:
 *
 * <pre>
 *      1     2     3     4     5   ...
 *   +-----+-----+-----+-----+-----+...+-----+-----+
 *   |   length  | mor |  0  |  1  |...| n-1 |  n  |
 *   +-----+-----+-----+-----+-----+...+-----+-----+
 *   |<--- 3 bytes --->|<-------- n bytes -------->|
 * </pre>
 *
 * The first three bytes are "control bytes":
 * <ul>
 *   <li>The first two bytes contain the chunk's length</li>
 *   <li>The third byte contains whether there are more chunks</li>
 * </ul>
 *
 * The rest of the bytes contains the string.
 */
public class ByteCountedString {
    protected ByteBuffer buffer;
    protected int length;
    protected final static int DEFAULT_CHUNK_SIZE= 0xFFFF;

    /**
     * No-arg constructor
     *
     * @access  public
     */
    public ByteCountedString() {
        this.buffer= ByteBuffer.allocate(0);
        this.length= 0;
    }

    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.String string
     */
    public ByteCountedString(String string) {
      try {
          this.buffer= Charset.forName("UTF-8").newEncoder().encode(CharBuffer.wrap(string));
          this.buffer.rewind();
      } catch (CharacterCodingException ignored) {
      }
      this.length= this.buffer.remaining();
    }

    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.StringBuffer buffer
     */
    public ByteCountedString(StringBuffer buffer) {
        this(buffer.toString());
    }
    
    /**
     * Return length of encoded string based on specified chunksize
     *
     * @access  public
     * @param   int chunkSize
     * @return  int
     */
    public int length(int chunkSize) {
        return this.length + 3 * (int)ceil((double)this.length / (double)chunkSize);
    }

    /**
     * Return length of encoded string based on the default chunksize
     *
     * @access  public
     * @return  int
     */
    public int length() {
        return this.length(DEFAULT_CHUNK_SIZE);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        return "[BCS(" + this.length + ")= '" + this.buffer + "']";
    }

    /**
     * Writes this string to a specified DataOutput instance using 
     * a specified chunk size
     *
     * @access  public
     * @param   java.io.DataOutput out
     */
    public void writeTo(DataOutput out, int chunkSize) throws IOException {
        do {
            int length= this.buffer.remaining();
            int chunk= length > chunkSize ? chunkSize : length;

            out.writeByte((int)((chunk >>> 8) & 0xFF));
            out.writeByte((int)((chunk >>> 0) & 0xFF));
            out.writeByte(length- chunk > 0 ? 1 : 0);
            
            byte[] dst= new byte[chunk];
            this.buffer.get(dst, 0, chunk);
            out.write(dst);
        } while (this.buffer.hasRemaining());
    }

    /**
     * Writes this string to a specified DataOutput instance using 
     * the DEFAULT_CHUNK_SIZE
     *
     * @access  public
     * @param   java.io.DataOutput out
     */
    public void writeTo(DataOutput out) throws IOException {
        this.writeTo(out, DEFAULT_CHUNK_SIZE);
    }
    
    /**
     * Reads from a specfied DataInput source
     *
     * @access  public
     * @param   java.io.DataInput in
     * @return  java.lang.String
     */
    public static String readFrom(DataInput in) throws IOException {
        int length;
        boolean next;
        
        ByteArrayOutputStream out= new ByteArrayOutputStream();
        do {
            length= in.readUnsignedShort();
            next= (1 == in.readUnsignedByte());
            
            byte[] buffer= new byte[length];
            in.readFully(buffer);
            
            out.write(buffer);
        } while (next);
        
        // See http://java.sun.com/j2se/1.5.0/docs/guide/intl/encoding.doc.html
        return out.toString("UTF8");
    }
}
