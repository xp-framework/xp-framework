/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

/**
 * Person class
 *
 * @purpose Value object for SerializerTest
 */
public class Person {
    public int id = 1549;
    public String name = "Timm Friebe";
    
    /**
     * Returns a string representation if this person object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        return (this.getClass().getName() + "(" + id + ") [ name= '" + this.name + "']");
    }
    
    /**
     * Checks for equality of two person objects. Returns true if id and name
     * members are equal.
     *
     * @access  public
     * @param   java.lang.Object o
     * @return  boolean
     */
    @Override public boolean equals(Object o) {
        if (!(o instanceof Person)) return false;  // Short-cuircuit
        
        Person cmp= (Person)o;
        return this.id == cmp.id && this.name.equals(cmp.name);
    }
    
    /**
     * Retrieve name
     *
     * @access  public
     * @return  java.lang.String
     */
    public String getName() {
        return this.name;
    }
    
    /**
     * Set name
     *
     * @access  public
     * @param   java.lang.String name
     */
    public void setName(String name) {
        this.name= name;
    }
    
    /**
     * Retrieve id
     *
     * @access  public
     * @return  int
     */
    public int getId() {
        return this.id;
    }

    /**
     * Set id
     *
     * @access  public
     * @param   int id
     */
    public void setId(int i) {
        this.id= id;
    }
}
