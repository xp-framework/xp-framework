<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id: static.xsl 7662 2006-08-13 11:47:23Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../master.xsl"/>
  <xsl:include href="../wizard.inc.xsl"/>

  <xsl:template match="/">
    <html>
      <body>
      
        <xsl:copy-of select="func:display_wizard_error('newpagehandler')"/>
        <xsl:copy-of select="func:display_wizard_success('newpagehandler')"/>
        <xsl:copy-of select="func:display_wizard_reload('newpagehandler')"/>
        <form action="{func:link('admin')}" method="POST">
          <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'newpagehandler']/@id}"/>
          
          <table border="0">
          
            <xsl:copy-of select="func:wizard_row_input('newpagehandler', 'name')"/>
            <xsl:copy-of select="func:wizard_row_fileupload('newpagehandler', 'file')"/>
            <xsl:copy-of select="func:wizard_row_textarea('newpagehandler', 'description', 80, 10)"/>
            <xsl:copy-of select="func:wizard_row_checkbox('newpagehandler', 'online')"/>
            <xsl:copy-of select="func:wizard_row_input('newpagehandler', 'tags')"/>
            <xsl:copy-of select="func:wizard_row_separator('newpagehandler', 'submit')"/>
            <xsl:copy-of select="func:wizard_row_submit('newpagehandler', 'submit')"/>
          </table>
        </form>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
