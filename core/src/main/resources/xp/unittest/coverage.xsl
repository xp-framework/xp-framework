<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:exslt="http://exslt.org/common"
  xmlns:func="http://exslt.org/functions"
  xmlns:string="http://exslt.org/strings"
  extension-element-prefixes="func exslt string"
>
  <xsl:strip-space elements="*"/>

  <xsl:template match="/">
    <html>
      <head>
        <title><xsl:value-of select="'Code Coverage Report'" /></title>
        <style>
        <![CDATA[
          body {
            padding: 10px;
            color: #373737;
            font-size: 12px;
            font-family: Arial, Verdana, Tahoma, sans-serif;
          }

          body > h1 {
            font-size: 18px;
            font-weight: normal;
            text-transform: uppercase;
          }

          body > h1 > span.title {
            padding: 5px 10px 5px 0;
            color: #006AB3;
            font-weight: bold;
          }

          body > h1 > span.date {
            padding: 5px 0 5px 10px;
            border-left: 1px solid #CBCBCB;
          }

          body > div {
            background-color: #E2E2E2;
            border: 1px solid #CBCBCB;
            padding: 20px;
            margin-bottom: 20px;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
          }

          body > div > h2 {
            font-size: 16px;
            margin-bottom: 20px;
          }

          body > div > div {
            padding: 5px 0;
            position: relative;
            border-bottom: 1px solid #CBCBCB;
            background-color: rgba(255, 255, 255, 0.5)
          }

          body > div > div:nth-child(2n) {
            background-color: rgba(255, 255, 255, 1.0)
          }

          body > div > div > label {
            font-weight: bold;
          }

          body > div > div > span.locinfo {
            float: right;
            width: 15%;
            margin-right: 5;
            padding: 1px 5px;
            font-weight: bold;
            text-align: right;
            background-color: #FF9999;
            position: relative;
          }

          span.locinfo div.locinfoText {
            position: relative;
          }

          div.code {
            border: 1px solid #CBCBCB;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.5)
          }
          pre.line {
            margin:0px;
            padding: 2px 0;
            height:1.0em;
          }
          pre.line[checked] {
            background-color: 99ff66;
          }
          pre.line[unchecked] {
            background-color: ff9999;
          }
          div.statusBar {
            background-color: #99FF66;
            display: block;
            height: 100%;
            top: 0;
            right: 0;
            position: absolute;
            width: 0%;
          }
        ]]>
        </style>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" />
        <script type="text/javascript">
        <![CDATA[
          $(document).ready(function() {
            $('.folder').hide();
            $('.file').hide();

            $('input[name=folder]').change(function(event) {
              $('.folder, .file').slideUp(300);
              $(document.getElementById('fs' + $(event.target).attr('id').substr(2))).slideDown(300);
              $('.folder input').each(function(index, elem) {
                $(elem).removeAttr('checked');
              });
            });

            $('input[name=file]').change(function(event) {
              $('.file').slideUp(300);
              $(document.getElementById('fs' + $(event.target).attr('id').substr(2))).slideDown(300);
            });
          });
        ]]>
        </script>
      </head>
      <body>
        <h1>
          <span class="title"><xsl:value-of select="'Code Coverage Report'" /></span>
          <span class="date"><xsl:value-of select="/paths/@time" /></span>
        </h1>

        <div>
          <h2>Folder</h2>
          <xsl:for-each select="/paths/path">
            <xsl:sort select="@name" />

            <xsl:variable name="clocs" select="count(file/line[@checked])" />
            <xsl:variable name="ulocs" select="count(file/line[@unchecked])" />
            <xsl:variable name="alocs" select="$clocs + $ulocs" />
            <xsl:variable name="locsRate" select="round($clocs div $alocs * 100)" />

            <div>
              <input type="radio" id="rb{string:replace(@name, '/', '_')}" name="folder" />
              <label for="rb{string:replace(@name, '/', '_')}"><xsl:value-of select="@name"/></label>

              <span class="locinfo" title="{$locsRate}%">
                <div class="statusBar" style="width:{$locsRate}%"></div>
                <div class="locinfoText">
                  <xsl:value-of select="concat($clocs, ' of ', $alocs, ' lines checked')" />
                </div>
              </span>
            </div>
          </xsl:for-each>
        </div>

        <xsl:for-each select="/paths/path">
          <xsl:sort select="@name" />
          <xsl:apply-templates select="." />
        </xsl:for-each>

        <xsl:for-each select="/paths/path/file">
          <xsl:sort select="@name" />
          <xsl:apply-templates select="." />
        </xsl:for-each>

      </body>
    </html>
  </xsl:template>

  <xsl:template match="path">
    <div id="fs{string:replace(@name, '/', '_')}" class="folder">
      <h2><xsl:value-of select="@name" /></h2>
      <xsl:for-each select="file">
        <xsl:sort select="@name" />

        <xsl:variable name="clocs" select="count(line[@checked])" />
        <xsl:variable name="ulocs" select="count(line[@unchecked])" />
        <xsl:variable name="alocs" select="$clocs + $ulocs" />
        <xsl:variable name="locsRate" select="round($clocs div $alocs * 100)" />

        <div>
          <input type="radio" id="rb{string:replace(concat(../@name, '/', string:replace(@name, '.', '_')), '/', '_')}" name="file" />
          <label for="rb{string:replace(concat(../@name, '/', string:replace(@name, '.', '_')), '/', '_')}"><xsl:value-of select="@name" /></label>

          <span class="locinfo" title="{$locsRate}%">
            <div class="statusBar" style="width:{$locsRate}%"></div>
            <div class="locinfoText">
              <xsl:value-of select="concat($clocs, ' of ', $alocs, ' lines checked')" />
            </div>
          </span>
        </div>
      </xsl:for-each>
    </div>
  </xsl:template>

  <xsl:template match="file">
    <xsl:variable name="clocs" select="count(line[@checked])" />
    <xsl:variable name="ulocs" select="count(line[@unchecked])" />
    <xsl:variable name="alocs" select="$clocs + $ulocs" />

    <div id="fs{string:replace(concat(../@name, '/', string:replace(@name, '.', '_')), '/', '_')}" class="file">
      <h2><xsl:value-of select="@name" /></h2>
      <span>
        <xsl:value-of select="concat($clocs, ' of ', $alocs, ' lines checked')" />
      </span>
      <div class="code">
        <xsl:apply-templates select="line" />
      </div>
    </div>
  </xsl:template>

  <xsl:template match="line">
    <pre class="line">
      <xsl:if test="@checked = 'checked'">
        <xsl:attribute name="checked">
          <xsl:value-of select="'checked'" />
        </xsl:attribute>
      </xsl:if>
      <xsl:if test="@unchecked = 'unchecked'">
        <xsl:attribute name="unchecked">
          <xsl:value-of select="'unchecked'" />
        </xsl:attribute>
      </xsl:if>
      <xsl:variable name="padlen" select="string-length(last()) - string-length(position())" />
      <xsl:value-of select="concat(string:padding($padlen, ' '), position(), ' ')" />
      <xsl:value-of select="." />
    </pre>
  </xsl:template>

</xsl:stylesheet>