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
  <xsl:include href="layout.inc.xsl"/>
  <xsl:include href="../wizard.inc.xsl"/>
  
  <xsl:template name="page-body">

    <xsl:copy-of select="func:display_wizard_error('loginhandler')"/>
    <xsl:copy-of select="func:display_wizard_success('loginhandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('loginhandler')"/>
    
    <form action="{func:link('login')}" method="POST">
      <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'loginhandler']/@id}"/>
      
      <div>
        You must authenticate as author before posting photos to this page. Please
        supply your credentials:
      </div>
      
      <table border="0" class="form">
        <xsl:copy-of select="func:wizard_row_input('loginhandler', 'username')"/>
        <xsl:copy-of select="func:wizard_row_password('loginhandler', 'password')"/>

        <xsl:copy-of select="func:wizard_row_submit('loginhandler', 'submit')"/>
      </table>
    </form>
  </xsl:template>
</xsl:stylesheet>
