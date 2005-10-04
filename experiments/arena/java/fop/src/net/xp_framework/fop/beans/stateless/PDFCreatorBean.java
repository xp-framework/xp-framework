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

public class PDFCreatorBean implements SessionBean {

    /**
     * Transform an XML tree to a PDF.
     *
     * @access  public
     * @return  byte[]
     */
    public byte[] xmlToPDF(String template, String xml) throws Exception {
    
        PDFCreator pdf= new PDFCreator();
        JarTemplateLoader loader= new JarTemplateLoader();
        
        String xslt= loader.templateFor(template);
        pdf.setTemplate(xslt);
        pdf.setInput(xml);
        pdf.xmlToPDF();
        
        return pdf.getOutput().getBytes();
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
