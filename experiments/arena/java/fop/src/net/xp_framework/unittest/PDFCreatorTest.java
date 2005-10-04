/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.Before;
import org.junit.Ignore;
import static org.junit.Assert.*;

import net.xp_framework.fop.PDFCreator;

import org.apache.fop.apps.FOPException;

import java.io.OutputStream;
import java.io.ByteArrayOutputStream;
import java.io.ByteArrayInputStream;
import java.io.StringBufferInputStream;

public class PDFCreatorTest {
    protected PDFCreator pdf;

    @Before public void setUp() {
        this.pdf= new PDFCreator();
    }

    @Test public void generateDummyPDF() throws Exception {
        byte [] stream= new byte[5];
        
        pdf.setTemplateStream(new ByteArrayInputStream(stream));
        pdf.setInputStream(new ByteArrayInputStream(stream));
        OutputStream output= pdf.getOutputStream();
    }
    
    @Test public void simpleFO() throws Exception {
        String fo= 
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>" +
            "<fo:root xmlns:fo=\"http://www.w3.org/1999/XSL/Format\"><fo:layout-master-set><fo:simple-page-master master-name=\"simple\"	margin-top=\"75pt\" margin-bottom=\"25pt\" margin-left=\"100pt\" margin-right=\"50pt\">	<fo:region-body margin-bottom=\"50pt\"/>	<fo:region-after extent=\"25pt\"/></fo:simple-page-master></fo:layout-master-set><fo:page-sequence master-reference=\"simple\"><fo:static-content flow-name=\"xsl-region-after\">	<fo:block >		<fo:page-number/>	</fo:block></fo:static-content><fo:flow flow-name=\"xsl-region-body\"><fo:block background-color=\"#dddddd\">Simple example for background-image</fo:block></fo:flow></fo:page-sequence></fo:root>"
        ;
        
        // Transform simple FO into PDF
        pdf.setInputStream(new StringBufferInputStream(fo));
        pdf.setOutputStream(new ByteArrayOutputStream());
        pdf.foToPDF();
        
        ByteArrayOutputStream out= (ByteArrayOutputStream)pdf.getOutputStream();
        assertTrue(0 < out.size()); 
    }
    
    @Test public void simpleFoFromString() throws Exception {
        // Transform simple FO from string to PDF
        pdf.setInput(
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>" +
            "<fo:root xmlns:fo=\"http://www.w3.org/1999/XSL/Format\"><fo:layout-master-set><fo:simple-page-master master-name=\"simple\"	margin-top=\"75pt\" margin-bottom=\"25pt\" margin-left=\"100pt\" margin-right=\"50pt\">	<fo:region-body margin-bottom=\"50pt\"/>	<fo:region-after extent=\"25pt\"/></fo:simple-page-master></fo:layout-master-set><fo:page-sequence master-reference=\"simple\"><fo:static-content flow-name=\"xsl-region-after\">	<fo:block >		<fo:page-number/>	</fo:block></fo:static-content><fo:flow flow-name=\"xsl-region-body\"><fo:block background-color=\"#dddddd\">Simple example for background-image</fo:block></fo:flow></fo:page-sequence></fo:root>"
        );
        pdf.foToPDF();
    }
    
    @Test @Ignore public void simpleXMLToPDF() throws Exception {
        String xml= "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<empty-document/>";
        String xslt= "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<xsl:stylesheet  version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">" +
            "<xsl:template match=\"/\">" +
            "<fo:root xmlns:fo=\"http://www.w3.org/1999/XSL/Format\"><fo:layout-master-set><fo:simple-page-master master-name=\"simple\"	margin-top=\"75pt\" margin-bottom=\"25pt\" margin-left=\"100pt\" margin-right=\"50pt\">	<fo:region-body margin-bottom=\"50pt\"/>	<fo:region-after extent=\"25pt\"/></fo:simple-page-master></fo:layout-master-set><fo:page-sequence master-reference=\"simple\"><fo:static-content flow-name=\"xsl-region-after\">	<fo:block >		<fo:page-number/>	</fo:block></fo:static-content><fo:flow flow-name=\"xsl-region-body\"><fo:block background-color=\"#dddddd\">Simple example for background-image</fo:block></fo:flow></fo:page-sequence></fo:root>" +
            "</xsl:template></xsl:stylesheet>";
        
        System.out.println(xslt);
        pdf.setInput(xml);
        pdf.setTemplate(xslt);
        
        pdf.xmlToPDF();
        String out= pdf.getOutput();
    }
}
