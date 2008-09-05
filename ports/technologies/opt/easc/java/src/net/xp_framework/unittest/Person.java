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
public class Person implements java.io.Serializable {
    public int id = 1549;
    public String name = "Timm Friebe";
    public String[] responsibilities = new String[] { "Leader", "Programmer" };
    
    /**
     * Returns a string representation if this person object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        StringBuilder s= new StringBuilder()
            .append(this.getClass().getName())
            .append('(')
            .append(id)
            .append(") [ name = '")
            .append(this.name)
            .append(", responsibilities= ")
        ;
        if (null == this.responsibilities) {
            s.append("(null)");
        } else {
            s.append("[ ");
            for (String responsibility: this.responsibilities) {
                s.append("'").append(responsibility).append("' ");
            }
            s.append(" ]");
        }
        return s.append(" ]").toString();
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
        return (
            this.id == cmp.id && 
            this.name.equals(cmp.name) && 
            java.util.Arrays.equals(this.responsibilities, cmp.responsibilities)
        );
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
