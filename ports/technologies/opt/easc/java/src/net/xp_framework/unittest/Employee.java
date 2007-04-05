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
public class Employee extends Person {
    public int personellNumber;

    private Employee() { }

    public Employee(int personellNumber) { 
        this.personellNumber= personellNumber;
    }
}
