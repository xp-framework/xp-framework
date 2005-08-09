/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import net.xp_framework.easc.protocol.standard.MessageType;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;

/**
 * Message header
 *
 */
public class Header {
    private int magicNumber;
    private byte versionMajor;
    private byte versionMinor;
    private MessageType messageType;
    private boolean compressed;
    private int dataLength;

    /**
     * Constructor
     *
     * @access  public
     * @param   int magic number
     * @param   byte version major
     * @param   byte version minor
     * @param   net.xp_framework.easc.protocol.standard.MessageType message type
     * @param   boolean compressed
     * @param   int data length
     */
    public Header(int magicNumber, byte versionMajor, byte versionMinor, MessageType messageType, boolean compressed, int dataLength) {
        this.magicNumber= magicNumber;
        this.versionMajor= versionMajor;
        this.versionMinor= versionMinor;
        this.messageType= messageType;
        this.compressed= compressed;
        this.dataLength= dataLength;
    }
    
    /**
     * Reads and constructs a header object from a given input stream
     *
     * @static
     * @access  public
     * @param   java.io.DataInputStream in
     * @return  net.xp_framework.easc.protocol.standard.Header
     * @throws  java.io.IOException
     */
    public static Header readFrom(DataInputStream in) throws IOException {
        return new Header(
            in.readInt(),
            in.readByte(),
            in.readByte(),
            MessageType.valueOf(in.readByte()),
            in.readBoolean(),
            in.readInt()
        );
    }
    
    /**
     * Writes this header object to a given output stream
     *
     * @access  public
     * @param   java.io.DataOutputStream out 
     * @return  int number of bytes written
     * @throws  java.io.IOException
     */
    public int writeTo(DataOutputStream out) throws IOException {
        int written= out.size();

	    out.writeInt(this.magicNumber);
	    out.writeByte(this.versionMajor);
	    out.writeByte(this.versionMinor);
	    out.writeByte(this.messageType.ordinal());
	    out.writeBoolean(this.compressed);
	    out.writeInt(this.dataLength);

        return out.size() - written;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  String
     */
    @Override public String toString() {
        return (
            this.getClass().getName() + "[" + this.magicNumber + "]@{\n" +
            "  [Version.Major   ] " + this.versionMajor + "\n" +
            "  [Version.Minor   ] " + this.versionMinor + "\n" +
            "  [Message.Type    ] " + this.messageType + "\n" +
            "  [Data.Compressed ] " + this.compressed + "\n" +
            "  [Data.Length     ] " + this.dataLength + "\n" +
            "}"
        );
    }
    
    /**
     * Tests whether a given object is equal to this object. Two headers 
     * are considered equal when all their members are equal.
     *
     * @access  public
     * @param   lang.Object cmp
     * @return  boolean
     */
    @Override public boolean equals(Object cmp) {
        if (!(cmp instanceof Header)) return false;     // Short-cuircuit this
        
        Header h= (Header)cmp;
        return (
            (this.magicNumber == h.magicNumber) &&
            (this.versionMajor == h.versionMajor) &&
            (this.versionMinor == h.versionMinor) &&
            (this.messageType.equals(h.messageType)) &&
            (this.compressed == h.compressed) &&
            (this.dataLength == h.dataLength)
        );
    }
    
    /**
     * Set magic number
     *
     * @access  public
     * @param   int number
     */
    public void setMagicNumber(int number) {
        this.magicNumber= number;
    }

    /**
     * Set version major number
     *
     * @access  public
     * @param   byte version major number
     */
    public void setVersionMajor(byte versionMajor) {
        this.versionMajor= versionMajor;
    }

    /**
     * Set version minor number
     *
     * @access  public
     * @param   byte version minor number
     */
    public void setVersionMinor(byte versionMinor) {
        this.versionMinor= versionMinor;
    }

    /**
     * Set type of message
     *
     * @access  public
     * @param   net.xp_framework.easc.protocol.standard.MessageType messageType
     */
    public void setMessageType(MessageType messageType) {
        this.messageType= messageType;
    }

    /**
     * Set compressed flag
     *
     * @access  public
     * @param   boolean compressed
     */
    public void setCompressed(boolean compressed) {
        this.compressed= compressed;
    }

    /**
     * Set data length
     *
     * @access  public
     * @param   int data length
     */
    public void setDataLength(int dataLength) {
        this.dataLength= dataLength;
    }

    /**
     * Set magic number
     *
     * @access  public
     * @return  int
     */
    public int getMagicNumber() {
        return this.magicNumber;
    }

    /**
     * Return version major number
     *
     * @access  public
     * @return  byte version major
     */
    public byte getVersionMajor() {
        return this.versionMajor;
    }

    /**
     * Return version minor number
     *
     * @access  public
     * @return  byte version minor
     */
    public byte getVersionMinor() {
        return this.versionMinor;
    }

    /**
     * Return type of message
     *
     * @access  public
     * @return  net.xp_framework.easc.protocol.standard.MessageType messageType
     */    
    public MessageType getMessageType() {
        return this.messageType;
    }

    /**
     * Return data length
     *
     * @access  public
     * @return  int data length
     */
    public int getDataLength() {
        return this.dataLength;
    }

    /**
     * Return compressed flag
     *
     * @access  public
     * @return  boolean compressed
     */
    public boolean getCompressed() {
        return this.compressed;
    }
}
