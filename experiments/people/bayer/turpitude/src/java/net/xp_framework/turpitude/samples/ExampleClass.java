package net.xp_framework.turpitude.samples;

import java.util.Date;

public class ExampleClass {
    public int intval = 0;
    public String stringval = "";
    private String privateString = "not to be touched!";

    /** 
     * useless static method
     */
    public static String staticMethod(int i) {
        return "Static method : " + i;
    }

   /**
    * default constructor
    */
    public ExampleClass() {
        System.out.println("ExampleClass: Constructor() called");
    }

   /**
    * int constructor
    */
    public ExampleClass(int i) {
        System.out.println("ExampleClass: Constructor(int) called: " + i);
        intval = i;
    }

   /**
    * string constructor
    */
    public ExampleClass(String s) {
        System.out.println("ExampleClass: Constructor(String) called: " + s);
        stringval = s;
    }

   /**
    * int constructor
    */
    public ExampleClass(int i, String s) {
        System.out.println("ExampleClass: Constructor(int, String) called: " + i + " " + s);
        intval = i;
        stringval = s;
    }

    /**
     * set both vals
     */
    public void setValues(int i, String s) {
        System.out.println("ExampleClass: setValues(int, String) called: " + i + " " + s);
        intval = i;
        stringval = s;
    }

    /**
     * returns a Date instance
     */
    public Date getDate() {
        System.out.println("ExampleClass: getDate()");
        return new Date();
    }

    public String toString() {
        String retval = "ExampleClass, intval: " + intval + " stringval: " + stringval;
        System.out.println("ExampleClass: toString(): " + retval);
        return retval;
    }
    

}

