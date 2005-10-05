/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.fop.beans.stateless;

import javax.ejb.EJBException;
import javax.ejb.SessionBean;
import javax.ejb.SessionContext;
import java.rmi.RemoteException;

import net.xp_framework.fop.PDFCreator;
import net.xp_framework.fop.JarTemplateLoader;

/**
 * Calculator demonstration
 *
 * @ejb.bean
 *      name="PDFCreator"
 *      type="Stateless"
 *      view-type="both"
 *      local-jndi-name="xp/test/PDFCreator"
 *      jndi-name="xp/test/PDFCreator"
 *      display-name="PDF Creator Test Bean"
 */
public class PDFCreatorBean implements SessionBean {

    /**
     * Transform an XML tree to a PDF.
     *
     * @ejb.interface-method view-type = "both"
     * @return  byte[]
     */
    public String xmlToPDF(String template, String xml) throws Exception {
        System.out.println("PDFCreatorBean::xmlToPDF(" + template + ", xml) invoked.");

        PDFCreator pdf= new PDFCreator();
        JarTemplateLoader loader= new JarTemplateLoader();
        
        String xslt= loader.templateFor(template);
        pdf.setTemplate(xslt);
        pdf.setInput(xml);
        pdf.xmlToPDF();
        
        System.out.println("Returning " + pdf.getOutput().length() + " chars");
        return pdf.getOutput();
    }
    
    /**
     * Retrieve a PDF from an source FO file.
     *
     * @ejb.interface-method view-type = "both"
     * @return  byte[]
     */
    public String foToPDF(String template) throws Exception {
        System.out.println("PDFCreatorBean::foToPDF(" + template + ") invoked.");

        PDFCreator pdf= new PDFCreator();
        JarTemplateLoader loader= new JarTemplateLoader();
        
        String xslt= loader.templateFor(template);
        pdf.setInput(xslt);
        pdf.foToPDF();
        
        System.out.println("Returning " + pdf.getOutput().length() + " chars");
        return pdf.getOutput();
    }

    /**
     * Activate method
     *
     * @access  public
     */
    public void ejbActivate() throws EJBException, RemoteException { }

    /**
     * Passivate method
     *
     * @access  public
     */
    public void ejbPassivate() throws EJBException, RemoteException { }

    /**
     * Remove method
     *
     * @access  public
     */
    public void ejbRemove() throws EJBException, RemoteException { }

    /**
     * Session context injection method
     *
     * @access  public
     */
    public void setSessionContext(SessionContext sessionContext) throws EJBException, RemoteException { }
}
