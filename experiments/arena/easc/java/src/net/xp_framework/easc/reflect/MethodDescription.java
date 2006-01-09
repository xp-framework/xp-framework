/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.easc.reflect;

import net.xp_framework.easc.reflect.TransactionTypeDescription;

import java.io.Serializable;
import java.util.ArrayList;
import java.security.Principal;

/**
 * Describes an EJB method
 *
 */
public class MethodDescription implements Serializable {
    public String name;
    public Class returnType;
    public ArrayList<Class> parameterTypes= new ArrayList<Class>();
    public ArrayList<String> roles= new ArrayList<String>();
    public TransactionTypeDescription transactionType= null;
    
    /**
     * Get name of this method
     *
     * @access  public
     * @return  java.lang.String
     */
    public String getName() {
        return this.name;
    }
    
    /**
     * Set name of this method
     *
     * @access  public
     * @param   java.lang.String name
     */
    public void setName(String name) {
        this.name= name;
    }

    /**
     * Get transaction type of this method
     *
     * @access  public
     * @return  net.xp_framework.easc.reflect.TransactionTypeDescription
     */
    public TransactionTypeDescription getTransactionType() {
        return this.transactionType;
    }
    
    /**
     * Set transaction type of this method
     *
     * @access  public
     * @param   net.xp_framework.easc.reflect.TransactionTypeDescription transactionType
     */
    public void setTransactionType(TransactionTypeDescription transactionType) {
        this.transactionType= transactionType;
    }

    /**
     * Get return type of this method
     *
     * @access  public
     * @return  java.lang.Class
     */
    public Class getReturnType() {
        return this.returnType;
    }
    
    /**
     * Set return type of this method
     *
     * @access  public
     * @param   java.lang.Class returnType
     */
    public void setReturnType(Class returnType) {
        this.returnType= returnType;
    }
    
    /**
     * Add parameter type
     *
     * @access  public
     * @param   java.lang.Class parameterType
     */
    public void addParameter(Class parameterType) {
        this.parameterTypes.add(parameterType);
    }

    /**
     * Add role
     *
     * @access  public
     * @param   java.lang.String role
     */
    public void addRole(String role) {
        this.roles.add(role);
    }

    /**
     * Add role
     *
     * @access  public
     * @param   java.security.Principal role
     */
    public void addRole(Principal role) {
        this.roles.add(role.getName());
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        StringBuffer s= new StringBuffer(this.getClass().getName()).append("@{ ");
        
        // Prepend transaction information
        s.append("@Transaction(type= ").append(this.transactionType).append(") ");
        
        // Prepend security role information
        if (this.roles.size() > 0) {
            s.append("@Security(roles= ").append(this.roles).append(") ");
        }
        
        // Create method signature
        s.append(this.returnType.getCanonicalName()).append(' ').append(this.name).append('(');

        // Append parameter types separated by commas
        for (Class parameter: this.parameterTypes) {
            s.append(parameter.getCanonicalName()).append(", ");
        }
        if (this.parameterTypes.size() > 0) s.delete(s.length() - 2, s.length());
        
        return s.append(") }").toString();
    }
}
