/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.util;

import java.io.DataOutput;
import java.io.DataInput;
import java.io.IOException;
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
    protected String string;
    protected final static int DEFAULT_CHUNK_SIZE= 0xFFFF;

    /**
     * No-arg constructor
     *
     * @access  public
     */
    public ByteCountedString() {
        this.string= new String();
    }

    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.String string
     */
    public ByteCountedString(String string) {
        this.string= string;
    }

    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.StringBuffer buffer
     */
    public ByteCountedString(StringBuffer buffer) {
        this.string= buffer.toString();
    }
    
    /**
     * Return length of encoded string based on specified chunksize
     *
     * @access  public
     * @param   int chunkSize
     * @return  int
     */
    public int length(int chunkSize) {
        return this.string.length() + 3 * (int)ceil((double)this.string.length() / (double)chunkSize);
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
     * Writes this string to a specified DataOutput instance using 
     * a specified chunk size
     *
     * @access  public
     * @param   java.io.DataOutput out
     */
    public void writeTo(DataOutput out, int chunkSize) throws IOException {
        int length= this.string.length();
        int offset= 0;

        do {
            int chunk= length > chunkSize ? chunkSize : length;

            out.writeByte((int)((chunk >>> 8) & 0xFF));
            out.writeByte((int)((chunk >>> 0) & 0xFF));
            out.writeByte(length- chunk > 0 ? 1 : 0);
            out.writeBytes(this.string.substring(offset, offset+ chunk));

            offset+= chunk;
            length-= chunk;
        } while (length > 0);
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
     * Retrieve bytes
     *
     * @access  public
     * @param   int chunkSize
     * @return  java.lang.String
     */
    @Deprecated public String getBytes(int chunkSize) {
        int length= this.string.length();
        int offset= 0;

        StringBuffer buf= new StringBuffer(this.length(chunkSize));
        do {
            int chunk= length > chunkSize ? chunkSize : length;

            buf.append((char)((chunk >>> 8) & 0xFF));
            buf.append((char)((chunk >>> 0) & 0xFF));
            buf.append((char)(length- chunk > 0 ? 1 : 0));
            buf.append(this.string.substring(offset, offset+ chunk));

            offset+= chunk;
            length-= chunk;
        } while (length > 0);
        
        return buf.toString();
    }

    /**
     * Retrieve chunks using the DEFAULT_CHUNK_SIZE
     *
     * @access  public
     * @return  java.lang.String
     */
    public String getBytes() {
        return this.getBytes(DEFAULT_CHUNK_SIZE);
    }
}
