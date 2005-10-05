/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.fop;

import java.io.InputStream;
import java.io.OutputStream;
import java.io.StringBufferInputStream;
import java.io.ByteArrayOutputStream;

import org.xml.sax.InputSource;

import org.apache.fop.apps.Driver;
import org.apache.fop.apps.FOPException;

// Avalon imports
import org.apache.avalon.framework.ExceptionUtil;
import org.apache.avalon.framework.logger.Logger;
import org.apache.avalon.framework.logger.ConsoleLogger;
import org.apache.fop.messaging.MessageHandler;

// JAXP imports
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.TransformerException;
import javax.xml.transform.Source;
import javax.xml.transform.Result;
import javax.xml.transform.stream.StreamSource;
import javax.xml.transform.sax.SAXResult;

public class PDFCreator {
    protected InputStream input;
    protected InputStream xslt;
    protected OutputStream output;
    
    private Driver driver;

    public PDFCreator() {
        this.driver= new Driver();
        
        // Setup logger
        Logger logger= new ConsoleLogger(ConsoleLogger.LEVEL_ERROR);
        this.driver.setLogger(logger);
        MessageHandler.setScreenLogger(logger);

        this.driver.setRenderer(Driver.RENDER_PDF);
        
        this.xslt= null;
        this.input= null;
    }

    public void setInputStream(InputStream input) {
        this.input= input;
    }
    
    public void setInput(String s) {
        this.input= new StringBufferInputStream(s);
    }
    
    public void setTemplateStream(InputStream template) {
        this.xslt= template;
    }
    
    public void setTemplate(String t) {
        this.setTemplateStream(new StringBufferInputStream(t));
    }
    
    public void foToPDF() throws java.io.IOException, FOPException {
        // Set a default output if none set, yet.
        if (null == this.output) {
            this.output= new ByteArrayOutputStream(2048);
        }
    
        this.driver.setOutputStream(this.output);
        this.driver.setInputSource(new InputSource(this.input));
        this.driver.run();
    }
    
    public void xmlToPDF() throws Exception {
        // Set a default output if none set, yet.
        if (null == this.output) {
            this.output= new ByteArrayOutputStream(2048);
        }
        
        // Setup XSLT
        Transformer transformer= TransformerFactory.newInstance().newTransformer(new StreamSource(this.xslt));
        
        // Setup input for XSLT
        Source src= new StreamSource(this.input);
        
        // Perform transformation
        Result res= new SAXResult(driver.getContentHandler());
        
        // Transform XML with XSLT to PDF
        transformer.transform(src, res);
    }
    
    public void setOutputStream(OutputStream output) {
        this.output= output;
    }
    
    public OutputStream getOutputStream() {
        return this.output;
    }
    
    public String getOutput() {
        return this.output.toString();
    }
}
