/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import java.sql.Timestamp;

/**
 * Item class
 *
 * @purpose Value object for SerializerTest
 */
public class Item {
    public int id= 6100;
    public Timestamp createdAt= null;
    
    /**
     * Public no-arg constructor
     *
     * @access  public
     */
    public Item() { 
        this.createdAt= new Timestamp(1122369782000L);
    }
    
    /**
     * Returns a string representation if this person object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        return (this.getClass().getName() + "(" + id + ") [ createdAt= '" + this.createdAt + "']");
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
        if (!(o instanceof Item)) return false;  // Short-cuircuit
        
        Item cmp= (Item)o;
        return this.id == cmp.id && this.createdAt.equals(cmp.createdAt);
    }
}
