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
  <xsl:include href="../layout.inc.xsl"/>
  <xsl:include href="../../wizard.inc.xsl"/>

  <xsl:template name="page-body">
    <script type="text/javascript">
      <![CDATA[
      function addTag(tag) {
        value= document.getElementById("tagtarget").value + " " + tag + " ";
        value= value.replace(/\s+/g, " ");
        value= value.replace(/^\s*/g, "");
        document.getElementById("tagtarget").value= value;
      }
      ]]>
    </script>
    <xsl:copy-of select="func:display_wizard_error('newpagehandler')"/>
    <xsl:copy-of select="func:display_wizard_success('newpagehandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('newpagehandler')"/>
    
    <xsl:variable name="state" select="/formresult/handlers/handler[@name= 'newpagehandler']/@status"/>
    
    <xsl:if test="$state= 'setup' or $state= 'initialized' or $state= 'errors'">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'newpagehandler']/@id}"/>

        <table class="form">
          <xsl:copy-of select="func:wizard_row_input('newpagehandler', 'name')"/>
          <xsl:copy-of select="func:wizard_row_fileupload('newpagehandler', 'file')"/>
          <xsl:copy-of select="func:wizard_row_textarea('newpagehandler', 'description', 80, 10)"/>
          <xsl:copy-of select="func:wizard_row_input('newpagehandler', 'published')"/>
          
          <tr>
            <xsl:copy-of select="func:_wizard_row_start('newpagehandler', 'tags')"/>
            <td>
              <input type="text" name="tags" id="tagtarget" value="{/formresult/formvalues/param[@name= 'tags']}" size="40"/>
              &#160;
              <xsl:for-each select="/formresult/handlers/handler[@name= 'newpagehandler']/values/tags/tag">
                <a href="javascript:addTag('{normalize-space(.)}');"><xsl:value-of select="."/></a>
                <xsl:if test="position() != last()">, </xsl:if>
              </xsl:for-each>
            </td>
          </tr>
          
          <xsl:copy-of select="func:wizard_row_separator('newpagehandler', 'submit')"/>
          <xsl:copy-of select="func:wizard_row_submit('newpagehandler', 'submit')"/>
        </table>
      </form>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>
