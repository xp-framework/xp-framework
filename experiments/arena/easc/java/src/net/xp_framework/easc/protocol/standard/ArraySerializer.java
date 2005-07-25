/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.protocol.standard;

/**
 * Helper class for Serializer. Is expected to be used in an anonymous
 * class instance creation expression.
 *
 * Usage example:
 * <code>
 *   return new ArraySerializer() {
 *       public void yield(int i) {
 *           this.buffer.append(serialize(array[i]));
 *       }
 *   }.run(array.length);
 * </code>
 *
 * @see net.xp_framework.easc.protocol.standard.Serializer
 */
abstract public class ArraySerializer {
    protected StringBuffer buffer = null;
    
    /**
     * Run this serializer.
     *
     * @access  public
     * @param   int length
     * @return  java.lang.String
     */
    public String run(int length) {
        this.buffer= new StringBuffer("a:" + length + ":{");
        for (int i= 0; i < length; i++) {
            this.buffer.append("i:" + i + ";");
            this.yield(i);
        }
        this.buffer.append("}");
        return this.buffer.toString();
    }
    
    /**
     * This method is executed for each element in the array and should
     * append to the buffer the serialized version of the element at the
     * passed offset i of the array.
     *
     * @model   abstract
     * @access  public
     * @param   int i
     */
    abstract public void yield(int i);
}
