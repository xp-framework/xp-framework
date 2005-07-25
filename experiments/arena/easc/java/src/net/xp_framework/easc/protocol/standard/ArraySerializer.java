/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.protocol.standard;

/**
 * Helper class for Serializer
 *
 * @see net.xp_framework.easc.protocol.standard.Serializer
 */
public abstract class ArraySerializer {
    protected StringBuffer buffer = null;
    
    public String run(int length) {
        this.buffer= new StringBuffer("a:" + length + ":{");
        for (int i= 0; i < length; i++) {
            this.buffer.append("i:" + i + ";");
            this.yield(i);
        }
        this.buffer.append("}");
        return this.buffer.toString();
    }
    
    abstract public void yield(int i);
}
