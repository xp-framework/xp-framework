/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

package net.xp_framework.ejb_gen;

import com.sun.javadoc.*;
import java.util.ArrayList;
import java.io.FileReader;
import java.io.BufferedReader;
import java.io.IOException;

public class Generator {

    /**
     * Check whether a given annotation is present on a given 
     * ProgramElementDoc by a specified non-qualified name
     *
     * @param   com.sun.javadoc.ProgramElementDoc doc
     * @param   java.lang.String name
     * @return  boolean TRUE when the annotation is present, FALSE otherwise
     */
    protected static boolean isAnnotationPresent(ProgramElementDoc doc, String name) {
        for (AnnotationDesc a: doc.annotations()) {
            if (a.annotationType().name().equals(name)) return true;
        }
        return false;
    }

    /**
     * Retrieve a given annotation
     *
     * @param   com.sun.javadoc.ProgramElementDoc doc
     * @param   java.lang.String name
     * @return  com.sun.javadoc.AnnotationDesc
     */
    protected static AnnotationDesc getAnnotation(ProgramElementDoc doc, String name) {
        for (AnnotationDesc a: doc.annotations()) {
            if (a.annotationType().name().equals(name)) return a;
        }
        return null;
    }

    /**
     * Retrieve a given annotation value
     *
     * @param   com.sun.javadoc.AnnotationDesc a
     * @param   java.lang.String element
     * @return  java.lang.Object
     */
    protected static Object annotationValue(AnnotationDesc a, String element) {
        for (AnnotationDesc.ElementValuePair p: a.elementValues()) {
            if (p.element().name().equals(element)) return p.value().value();
        }

        return null;
    }
    
    /**
     * Retrieves sourcecode between the start of a declaration and the 
     * end of it (delimited by curly braces)
     *
     * @param   com.sun.javadoc.Doc doc
     * @return  java.lang.String name
     */
    protected static String sourceCodeOf(Doc doc) throws IOException {
        StringBuffer source= new StringBuffer();
        SourcePosition pos= doc.position();
        BufferedReader reader= new BufferedReader(new FileReader(pos.file()));
        
        // Forward until source position has been reached
        for (int i= 0; i < pos.line()- 1; i++) {
            reader.readLine();
        }
        for (int i= 0; i < pos.column(); i++) {
            reader.read();
        }
        
        // Find opening bracket
        while ('{' != reader.read());
        
        // Find corresponding closing bracket
        int brackets= 1;
        do {
            char c= (char)reader.read();
            if ('{' == c) brackets++;
            if ('}' == c) brackets--;
            source.append(c);
        } while (brackets > 0);
        
        // Close reader
        reader.close();
        
        // Trim off the last bracket and return sourcecode
        return source.deleteCharAt(source.length() - 1).toString();
    }
    
    /**
     * Retrieve method declaration
     *
     * @param   com.sun.javadoc.MethodDoc doc
     * @param   java.lang.String[] additionalExceptions exceptions to add to throws clause
     * @return  java.lang.String
     */
    protected static String methodDeclarationOf(MethodDoc doc, String[] additionalExceptions) {
        StringBuffer decl= new StringBuffer();

        // Modifiers, return type, name
        decl.append(doc.modifiers()).append(' ').append(doc.returnType()).append(' ').append(doc.name());

        // Parameters
        decl.append('(').append(new ArrayImploder<Parameter>() {
            protected String yield(Parameter p) {
                return p.type().qualifiedTypeName() + " " + p.name();
            }
        }.implode(", ", doc.parameters())).append(')');
        
        // Thrown exceptions
        Type[] exceptions= doc.thrownExceptionTypes();
        if (exceptions.length > 0) {
            decl.append(" throws ").append(new ArrayImploder<Type>() {
                protected String yield(Type e) {
                    return e.qualifiedTypeName();
                }
            }.implode(", ", exceptions));
        }
        
        // Additional exceptions
        if (additionalExceptions.length > 0) {
            decl.append(exceptions.length > 0 ? ", " : " throws ").append(new ArrayImploder<String>() {
                protected String yield(String s) {
                    return s;
                }
            }.implode(", ", additionalExceptions));
        }
        
        return decl.toString();
    }

    /**
     * Retrieve method declaration
     *
     * @param   com.sun.javadoc.MethodDoc doc
     * @return  java.lang.String
     */
    protected static String methodDeclarationOf(MethodDoc doc) {
        return methodDeclarationOf(doc, new String[] { });
    }
    
    /**
     * Find a method by name
     *
     * @param   com.sun.javadoc.ClassDoc doc
     * @param   java.lang.String the method's name
     * @return  com.sun.javadoc.MethodDoc the found method or NULL
     */
    protected static MethodDoc findMethod(ClassDoc doc, String name) {
        for (MethodDoc m: doc.methods()) {
            if (m.name().equals(name)) return m;
        }
        return null;
    }
    
    /**
     * Main method
     *
     * @param   com.sun.javadoc.RootDoc root
     * @return  bool TRUE on success
     */
    public static boolean start(RootDoc root) throws Exception {
        ArrayFilter interfaceMethodFilter= new ArrayFilter<MethodDoc>() {
            protected boolean yield(MethodDoc element) {
                return isAnnotationPresent(element, "InterfaceMethod");
            }
        };
        ArrayFilter remoteMethodFilter= new ArrayFilter<MethodDoc>() {
            protected boolean yield(MethodDoc element) {
                for (AnnotationValue item : (AnnotationValue[])(annotationValue(getAnnotation(element, "InterfaceMethod"), "viewTypes"))) {
                    if (((FieldDoc)item.value()).name().equals("Remote")) return true;
                }
                return false;
            }
        };
        ArrayFilter localMethodFilter= new ArrayFilter<MethodDoc>() {
            protected boolean yield(MethodDoc element) {
                for (AnnotationValue item : (AnnotationValue[])(annotationValue(getAnnotation(element, "InterfaceMethod"), "viewTypes"))) {
                    if (((FieldDoc)item.value()).name().equals("Local")) return true;
                }
                return false;
            }
        };
  
        for (ClassDoc doc: root.classes()) {
            ArrayList<MethodDoc> interfaceMethods= interfaceMethodFilter.filter(doc.methods());
            String simpleName= doc.name().substring(0, doc.name().length() - "Bean".length());

            // Remote interface
            System.out.println("\n===========================================\n");

            System.out.println("package " + doc.containingPackage().name() + ";\n");
            System.out.println("/**\n * Remote interface\n */");
            System.out.println("public interface " + simpleName +  " extends javax.ejb.EJBObject {");
            for (MethodDoc m: (ArrayList<MethodDoc>)remoteMethodFilter.filter(interfaceMethods)) {
                System.out.println("    " + methodDeclarationOf(m, new String[] { "java.rmi.RemoteException" }) + ";");
            }
            System.out.println("}");

            // Local interface
            System.out.println("\n===========================================\n");

            System.out.println("package " + doc.containingPackage().name() + ";\n");
            System.out.println("/**\n * Local interface\n */");
            System.out.println("public interface " + simpleName +  "Local extends javax.ejb.EJBLocalObject {");
            for (MethodDoc m: (ArrayList<MethodDoc>)localMethodFilter.filter(interfaceMethods)) {
                System.out.println("    " + methodDeclarationOf(m) + ";");
            }
            System.out.println("}");
            
            // Session Facade
            System.out.println("\n===========================================\n");
            System.out.println("package " + doc.containingPackage().name() + ";\n");
            System.out.println("/**\n * Session facade\n */");
            System.out.println("public class " + simpleName +  "Session implements javax.ejb.SessionBean {");
            
            for (MethodDoc m: interfaceMethods) {
                System.out.println("    " + methodDeclarationOf(m) + " { " + sourceCodeOf(m) + "}");
            }
            
            // Generate ejb* methods if not present
            for (String ejbMethod: new String[] { "ejbActivate", "ejbPassivate", "ejbRemove", "ejbCreate" }) {
                MethodDoc m= null;
                if (null != (m= findMethod(doc, ejbMethod))) {
                    System.out.println("    " + methodDeclarationOf(m) + " { " + sourceCodeOf(m) + "}");
                } else {
                    System.out.println("    public void " + ejbMethod + "() throws javax.ejb.EJBException, java.rmi.RemoteException { }");
                }
            }            
            System.out.println("}");
            
        }
        return true;
    }
}
