<?php
/* This class is part of the XP framework
 *
 * $Id: Word.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::microsoft::com;

  define('wdNoMailSystem',                                                0x00000000);
  define('wdMAPI',                                                        0x00000001);
  define('wdPowerTalk',                                                   0x00000002);
  define('wdMAPIandPowerTalk',                                            0x00000003);

  // Constants for enum WdTemplateType
  define('wdNormalTemplate',                                              0x00000000);
  define('wdGlobalTemplate',                                              0x00000001);
  define('wdAttachedTemplate',                                            0x00000002);

  // Constants for enum WdContinue
  define('wdContinueDisabled',                                            0x00000000);
  define('wdResetList',                                                   0x00000001);
  define('wdContinueList',                                                0x00000002);

  // Constants for enum WdIMEMode
  define('wdIMEModeNoControl',                                            0x00000000);
  define('wdIMEModeOn',                                                   0x00000001);
  define('wdIMEModeOff',                                                  0x00000002);
  define('wdIMEModeHiragana',                                             0x00000004);
  define('wdIMEModeKatakana',                                             0x00000005);
  define('wdIMEModeKatakanaHalf',                                         0x00000006);
  define('wdIMEModeAlphaFull',                                            0x00000007);
  define('wdIMEModeAlpha',                                                0x00000008);
  define('wdIMEModeHangulFull',                                           0x00000009);
  define('wdIMEModeHangul',                                               0x0000000A);

  // Constants for enum WdBaselineAlignment
  define('wdBaselineAlignTop',                                            0x00000000);
  define('wdBaselineAlignCenter',                                         0x00000001);
  define('wdBaselineAlignBaseline',                                       0x00000002);
  define('wdBaselineAlignFarEast50',                                      0x00000003);
  define('wdBaselineAlignAuto',                                           0x00000004);

  // Constants for enum WdIndexFilter
  define('wdIndexFilterNone',                                             0x00000000);
  define('wdIndexFilterAiueo',                                            0x00000001);
  define('wdIndexFilterAkasatana',                                        0x00000002);
  define('wdIndexFilterChosung',                                          0x00000003);
  define('wdIndexFilterLow',                                              0x00000004);
  define('wdIndexFilterMedium',                                           0x00000005);
  define('wdIndexFilterFull',                                             0x00000006);

  // Constants for enum WdIndexSortBy
  define('wdIndexSortByStroke',                                           0x00000000);
  define('wdIndexSortBySyllable',                                         0x00000001);

  // Constants for enum WdJustificationMode
  define('wdJustificationModeExpand',                                     0x00000000);
  define('wdJustificationModeCompress',                                   0x00000001);
  define('wdJustificationModeCompressKana',                               0x00000002);

  // Constants for enum WdFarEastLineBreakLevel
  define('wdFarEastLineBreakLevelNormal',                                 0x00000000);
  define('wdFarEastLineBreakLevelStrict',                                 0x00000001);
  define('wdFarEastLineBreakLevelCustom',                                 0x00000002);

  // Constants for enum WdMultipleWordConversionsMode
  define('wdHangulToHanja',                                               0x00000000);
  define('wdHanjaToHangul',                                               0x00000001);

  // Constants for enum WdColorIndex
  define('wdAuto',                                                        0x00000000);
  define('wdBlack',                                                       0x00000001);
  define('wdBlue',                                                        0x00000002);
  define('wdTurquoise',                                                   0x00000003);
  define('wdBrightGreen',                                                 0x00000004);
  define('wdPink',                                                        0x00000005);
  define('wdRed',                                                         0x00000006);
  define('wdYellow',                                                      0x00000007);
  define('wdWhite',                                                       0x00000008);
  define('wdDarkBlue',                                                    0x00000009);
  define('wdTeal',                                                        0x0000000A);
  define('wdGreen',                                                       0x0000000B);
  define('wdViolet',                                                      0x0000000C);
  define('wdDarkRed',                                                     0x0000000D);
  define('wdDarkYellow',                                                  0x0000000E);
  define('wdGray50',                                                      0x0000000F);
  define('wdGray25',                                                      0x00000010);
  define('wdByAuthor',                                                    0xFFFFFFFF);
  define('wdNoHighlight',                                                 0x00000000);

  // Constants for enum WdTextureIndex
  define('wdTextureNone',                                                 0x00000000);
  define('wdTexture2Pt5Percent',                                          0x00000019);
  define('wdTexture5Percent',                                             0x00000032);
  define('wdTexture7Pt5Percent',                                          0x0000004B);
  define('wdTexture10Percent',                                            0x00000064);
  define('wdTexture12Pt5Percent',                                         0x0000007D);
  define('wdTexture15Percent',                                            0x00000096);
  define('wdTexture17Pt5Percent',                                         0x000000AF);
  define('wdTexture20Percent',                                            0x000000C8);
  define('wdTexture22Pt5Percent',                                         0x000000E1);
  define('wdTexture25Percent',                                            0x000000FA);
  define('wdTexture27Pt5Percent',                                         0x00000113);
  define('wdTexture30Percent',                                            0x0000012C);
  define('wdTexture32Pt5Percent',                                         0x00000145);
  define('wdTexture35Percent',                                            0x0000015E);
  define('wdTexture37Pt5Percent',                                         0x00000177);
  define('wdTexture40Percent',                                            0x00000190);
  define('wdTexture42Pt5Percent',                                         0x000001A9);
  define('wdTexture45Percent',                                            0x000001C2);
  define('wdTexture47Pt5Percent',                                         0x000001DB);
  define('wdTexture50Percent',                                            0x000001F4);
  define('wdTexture52Pt5Percent',                                         0x0000020D);
  define('wdTexture55Percent',                                            0x00000226);
  define('wdTexture57Pt5Percent',                                         0x0000023F);
  define('wdTexture60Percent',                                            0x00000258);
  define('wdTexture62Pt5Percent',                                         0x00000271);
  define('wdTexture65Percent',                                            0x0000028A);
  define('wdTexture67Pt5Percent',                                         0x000002A3);
  define('wdTexture70Percent',                                            0x000002BC);
  define('wdTexture72Pt5Percent',                                         0x000002D5);
  define('wdTexture75Percent',                                            0x000002EE);
  define('wdTexture77Pt5Percent',                                         0x00000307);
  define('wdTexture80Percent',                                            0x00000320);
  define('wdTexture82Pt5Percent',                                         0x00000339);
  define('wdTexture85Percent',                                            0x00000352);
  define('wdTexture87Pt5Percent',                                         0x0000036B);
  define('wdTexture90Percent',                                            0x00000384);
  define('wdTexture92Pt5Percent',                                         0x0000039D);
  define('wdTexture95Percent',                                            0x000003B6);
  define('wdTexture97Pt5Percent',                                         0x000003CF);
  define('wdTextureSolid',                                                0x000003E8);
  define('wdTextureDarkHorizontal',                                       0xFFFFFFFF);
  define('wdTextureDarkVertical',                                         0xFFFFFFFE);
  define('wdTextureDarkDiagonalDown',                                     0xFFFFFFFD);
  define('wdTextureDarkDiagonalUp',                                       0xFFFFFFFC);
  define('wdTextureDarkCross',                                            0xFFFFFFFB);
  define('wdTextureDarkDiagonalCross',                                    0xFFFFFFFA);
  define('wdTextureHorizontal',                                           0xFFFFFFF9);
  define('wdTextureVertical',                                             0xFFFFFFF8);
  define('wdTextureDiagonalDown',                                         0xFFFFFFF7);
  define('wdTextureDiagonalUp',                                           0xFFFFFFF6);
  define('wdTextureCross',                                                0xFFFFFFF5);
  define('wdTextureDiagonalCross',                                        0xFFFFFFF4);

  // Constants for enum WdUnderline
  define('wdUnderlineNone',                                               0x00000000);
  define('wdUnderlineSingle',                                             0x00000001);
  define('wdUnderlineWords',                                              0x00000002);
  define('wdUnderlineDouble',                                             0x00000003);
  define('wdUnderlineDotted',                                             0x00000004);
  define('wdUnderlineThick',                                              0x00000006);
  define('wdUnderlineDash',                                               0x00000007);
  define('wdUnderlineDotDash',                                            0x00000009);
  define('wdUnderlineDotDotDash',                                         0x0000000A);
  define('wdUnderlineWavy',                                               0x0000000B);
  define('wdUnderlineWavyHeavy',                                          0x0000001B);
  define('wdUnderlineDottedHeavy',                                        0x00000014);
  define('wdUnderlineDashHeavy',                                          0x00000017);
  define('wdUnderlineDotDashHeavy',                                       0x00000019);
  define('wdUnderlineDotDotDashHeavy',                                    0x0000001A);
  define('wdUnderlineDashLong',                                           0x00000027);
  define('wdUnderlineDashLongHeavy',                                      0x00000037);
  define('wdUnderlineWavyDouble',                                         0x0000002B);

  // Constants for enum WdEmphasisMark
  define('wdEmphasisMarkNone',                                            0x00000000);
  define('wdEmphasisMarkOverSolidCircle',                                 0x00000001);
  define('wdEmphasisMarkOverComma',                                       0x00000002);
  define('wdEmphasisMarkOverWhiteCircle',                                 0x00000003);
  define('wdEmphasisMarkUnderSolidCircle',                                0x00000004);

  // Constants for enum WdInternationalIndex
  define('wdListSeparator',                                               0x00000011);
  define('wdDecimalSeparator',                                            0x00000012);
  define('wdThousandsSeparator',                                          0x00000013);
  define('wdCurrencyCode',                                                0x00000014);
  define('wd24HourClock',                                                 0x00000015);
  define('wdInternationalAM',                                             0x00000016);
  define('wdInternationalPM',                                             0x00000017);
  define('wdTimeSeparator',                                               0x00000018);
  define('wdDateSeparator',                                               0x00000019);
  define('wdProductLanguageID',                                           0x0000001A);

  // Constants for enum WdAutoMacros
  define('wdAutoExec',                                                    0x00000000);
  define('wdAutoNew',                                                     0x00000001);
  define('wdAutoOpen',                                                    0x00000002);
  define('wdAutoClose',                                                   0x00000003);
  define('wdAutoExit',                                                    0x00000004);

  // Constants for enum WdCaptionPosition
  define('wdCaptionPositionAbove',                                        0x00000000);
  define('wdCaptionPositionBelow',                                        0x00000001);

  // Constants for enum WdCountry
  define('wdUS',                                                          0x00000001);
  define('wdCanada',                                                      0x00000002);
  define('wdLatinAmerica',                                                0x00000003);
  define('wdNetherlands',                                                 0x0000001F);
  define('wdFrance',                                                      0x00000021);
  define('wdSpain',                                                       0x00000022);
  define('wdItaly',                                                       0x00000027);
  define('wdUK',                                                          0x0000002C);
  define('wdDenmark',                                                     0x0000002D);
  define('wdSweden',                                                      0x0000002E);
  define('wdNorway',                                                      0x0000002F);
  define('wdGermany',                                                     0x00000031);
  define('wdPeru',                                                        0x00000033);
  define('wdMexico',                                                      0x00000034);
  define('wdArgentina',                                                   0x00000036);
  define('wdBrazil',                                                      0x00000037);
  define('wdChile',                                                       0x00000038);
  define('wdVenezuela',                                                   0x0000003A);
  define('wdJapan',                                                       0x00000051);
  define('wdTaiwan',                                                      0x00000376);
  define('wdChina',                                                       0x00000056);
  define('wdKorea',                                                       0x00000052);
  define('wdFinland',                                                     0x00000166);
  define('wdIceland',                                                     0x00000162);

  // Constants for enum WdHeadingSeparator
  define('wdHeadingSeparatorNone',                                        0x00000000);
  define('wdHeadingSeparatorBlankLine',                                   0x00000001);
  define('wdHeadingSeparatorLetter',                                      0x00000002);
  define('wdHeadingSeparatorLetterLow',                                   0x00000003);
  define('wdHeadingSeparatorLetterFull',                                  0x00000004);

  // Constants for enum WdSeparatorType
  define('wdSeparatorHyphen',                                             0x00000000);
  define('wdSeparatorPeriod',                                             0x00000001);
  define('wdSeparatorColon',                                              0x00000002);
  define('wdSeparatorEmDash',                                             0x00000003);
  define('wdSeparatorEnDash',                                             0x00000004);

  // Constants for enum WdPageNumberAlignment
  define('wdAlignPageNumberLeft',                                         0x00000000);
  define('wdAlignPageNumberCenter',                                       0x00000001);
  define('wdAlignPageNumberRight',                                        0x00000002);
  define('wdAlignPageNumberInside',                                       0x00000003);
  define('wdAlignPageNumberOutside',                                      0x00000004);

  // Constants for enum WdBorderType
  define('wdBorderTop',                                                   0xFFFFFFFF);
  define('wdBorderLeft',                                                  0xFFFFFFFE);
  define('wdBorderBottom',                                                0xFFFFFFFD);
  define('wdBorderRight',                                                 0xFFFFFFFC);
  define('wdBorderHorizontal',                                            0xFFFFFFFB);
  define('wdBorderVertical',                                              0xFFFFFFFA);
  define('wdBorderDiagonalDown',                                          0xFFFFFFF9);
  define('wdBorderDiagonalUp',                                            0xFFFFFFF8);

  // Constants for enum WdBorderTypeHID

  // Constants for enum WdFramePosition
  define('wdFrameTop',                                                    0xFFF0BDC1);
  define('wdFrameLeft',                                                   0xFFF0BDC2);
  define('wdFrameBottom',                                                 0xFFF0BDC3);
  define('wdFrameRight',                                                  0xFFF0BDC4);
  define('wdFrameCenter',                                                 0xFFF0BDC5);
  define('wdFrameInside',                                                 0xFFF0BDC6);
  define('wdFrameOutside',                                                0xFFF0BDC7);

  // Constants for enum WdAnimation
  define('wdAnimationNone',                                               0x00000000);
  define('wdAnimationLasVegasLights',                                     0x00000001);
  define('wdAnimationBlinkingBackground',                                 0x00000002);
  define('wdAnimationSparkleText',                                        0x00000003);
  define('wdAnimationMarchingBlackAnts',                                  0x00000004);
  define('wdAnimationMarchingRedAnts',                                    0x00000005);
  define('wdAnimationShimmer',                                            0x00000006);

  // Constants for enum WdCharacterCase
  define('wdNextCase',                                                    0xFFFFFFFF);
  define('wdLowerCase',                                                   0x00000000);
  define('wdUpperCase',                                                   0x00000001);
  define('wdTitleWord',                                                   0x00000002);
  define('wdTitleSentence',                                               0x00000004);
  define('wdToggleCase',                                                  0x00000005);
  define('wdHalfWidth',                                                   0x00000006);
  define('wdFullWidth',                                                   0x00000007);
  define('wdKatakana',                                                    0x00000008);
  define('wdHiragana',                                                    0x00000009);

  // Constants for enum WdCharacterCaseHID

  // Constants for enum WdSummaryMode
  define('wdSummaryModeHighlight',                                        0x00000000);
  define('wdSummaryModeHideAllButSummary',                                0x00000001);
  define('wdSummaryModeInsert',                                           0x00000002);
  define('wdSummaryModeCreateNew',                                        0x00000003);

  // Constants for enum WdSummaryLength
  define('wd10Sentences',                                                 0xFFFFFFFE);
  define('wd20Sentences',                                                 0xFFFFFFFD);
  define('wd100Words',                                                    0xFFFFFFFC);
  define('wd500Words',                                                    0xFFFFFFFB);
  define('wd10Percent',                                                   0xFFFFFFFA);
  define('wd25Percent',                                                   0xFFFFFFF9);
  define('wd50Percent',                                                   0xFFFFFFF8);
  define('wd75Percent',                                                   0xFFFFFFF7);

  // Constants for enum WdStyleType
  define('wdStyleTypeParagraph',                                          0x00000001);
  define('wdStyleTypeCharacter',                                          0x00000002);

  // Constants for enum WdUnits
  define('wdCharacter',                                                   0x00000001);
  define('wdWord',                                                        0x00000002);
  define('wdSentence',                                                    0x00000003);
  define('wdParagraph',                                                   0x00000004);
  define('wdLine',                                                        0x00000005);
  define('wdStory',                                                       0x00000006);
  define('wdScreen',                                                      0x00000007);
  define('wdSection',                                                     0x00000008);
  define('wdColumn',                                                      0x00000009);
  define('wdRow',                                                         0x0000000A);
  define('wdWindow',                                                      0x0000000B);
  define('wdCell',                                                        0x0000000C);
  define('wdCharacterFormatting',                                         0x0000000D);
  define('wdParagraphFormatting',                                         0x0000000E);
  define('wdTable',                                                       0x0000000F);
  define('wdItem',                                                        0x00000010);

  // Constants for enum WdGoToItem
  define('wdGoToBookmark',                                                0xFFFFFFFF);
  define('wdGoToSection',                                                 0x00000000);
  define('wdGoToPage',                                                    0x00000001);
  define('wdGoToTable',                                                   0x00000002);
  define('wdGoToLine',                                                    0x00000003);
  define('wdGoToFootnote',                                                0x00000004);
  define('wdGoToEndnote',                                                 0x00000005);
  define('wdGoToComment',                                                 0x00000006);
  define('wdGoToField',                                                   0x00000007);
  define('wdGoToGraphic',                                                 0x00000008);
  define('wdGoToObject',                                                  0x00000009);
  define('wdGoToEquation',                                                0x0000000A);
  define('wdGoToHeading',                                                 0x0000000B);
  define('wdGoToPercent',                                                 0x0000000C);
  define('wdGoToSpellingError',                                           0x0000000D);
  define('wdGoToGrammaticalError',                                        0x0000000E);
  define('wdGoToProofreadingError',                                       0x0000000F);

  // Constants for enum WdGoToDirection
  define('wdGoToFirst',                                                   0x00000001);
  define('wdGoToLast',                                                    0xFFFFFFFF);
  define('wdGoToNext',                                                    0x00000002);
  define('wdGoToRelative',                                                0x00000002);
  define('wdGoToPrevious',                                                0x00000003);
  define('wdGoToAbsolute',                                                0x00000001);

  // Constants for enum WdCollapseDirection
  define('wdCollapseStart',                                               0x00000001);
  define('wdCollapseEnd',                                                 0x00000000);

  // Constants for enum WdRowHeightRule
  define('wdRowHeightAuto',                                               0x00000000);
  define('wdRowHeightAtLeast',                                            0x00000001);
  define('wdRowHeightExactly',                                            0x00000002);

  // Constants for enum WdFrameSizeRule
  define('wdFrameAuto',                                                   0x00000000);
  define('wdFrameAtLeast',                                                0x00000001);
  define('wdFrameExact',                                                  0x00000002);

  // Constants for enum WdInsertCells
  define('wdInsertCellsShiftRight',                                       0x00000000);
  define('wdInsertCellsShiftDown',                                        0x00000001);
  define('wdInsertCellsEntireRow',                                        0x00000002);
  define('wdInsertCellsEntireColumn',                                     0x00000003);

  // Constants for enum WdDeleteCells
  define('wdDeleteCellsShiftLeft',                                        0x00000000);
  define('wdDeleteCellsShiftUp',                                          0x00000001);
  define('wdDeleteCellsEntireRow',                                        0x00000002);
  define('wdDeleteCellsEntireColumn',                                     0x00000003);

  // Constants for enum WdListApplyTo
  define('wdListApplyToWholeList',                                        0x00000000);
  define('wdListApplyToThisPointForward',                                 0x00000001);
  define('wdListApplyToSelection',                                        0x00000002);

  // Constants for enum WdAlertLevel
  define('wdAlertsNone',                                                  0x00000000);
  define('wdAlertsMessageBox',                                            0xFFFFFFFE);
  define('wdAlertsAll',                                                   0xFFFFFFFF);

  // Constants for enum WdCursorType
  define('wdCursorWait',                                                  0x00000000);
  define('wdCursorIBeam',                                                 0x00000001);
  define('wdCursorNormal',                                                0x00000002);
  define('wdCursorNorthwestArrow',                                        0x00000003);

  // Constants for enum WdEnableCancelKey
  define('wdCancelDisabled',                                              0x00000000);
  define('wdCancelInterrupt',                                             0x00000001);

  // Constants for enum WdRulerStyle
  define('wdAdjustNone',                                                  0x00000000);
  define('wdAdjustProportional',                                          0x00000001);
  define('wdAdjustFirstColumn',                                           0x00000002);
  define('wdAdjustSameWidth',                                             0x00000003);

  // Constants for enum WdParagraphAlignment
  define('wdAlignParagraphLeft',                                          0x00000000);
  define('wdAlignParagraphCenter',                                        0x00000001);
  define('wdAlignParagraphRight',                                         0x00000002);
  define('wdAlignParagraphJustify',                                       0x00000003);
  define('wdAlignParagraphDistribute',                                    0x00000004);
  define('wdAlignParagraphJustifyMed',                                    0x00000005);
  define('wdAlignParagraphJustifyHi',                                     0x00000007);
  define('wdAlignParagraphJustifyLow',                                    0x00000008);

  // Constants for enum WdParagraphAlignmentHID

  // Constants for enum WdListLevelAlignment
  define('wdListLevelAlignLeft',                                          0x00000000);
  define('wdListLevelAlignCenter',                                        0x00000001);
  define('wdListLevelAlignRight',                                         0x00000002);

  // Constants for enum WdRowAlignment
  define('wdAlignRowLeft',                                                0x00000000);
  define('wdAlignRowCenter',                                              0x00000001);
  define('wdAlignRowRight',                                               0x00000002);

  // Constants for enum WdTabAlignment
  define('wdAlignTabLeft',                                                0x00000000);
  define('wdAlignTabCenter',                                              0x00000001);
  define('wdAlignTabRight',                                               0x00000002);
  define('wdAlignTabDecimal',                                             0x00000003);
  define('wdAlignTabBar',                                                 0x00000004);
  define('wdAlignTabList',                                                0x00000006);

  // Constants for enum WdVerticalAlignment
  define('wdAlignVerticalTop',                                            0x00000000);
  define('wdAlignVerticalCenter',                                         0x00000001);
  define('wdAlignVerticalJustify',                                        0x00000002);
  define('wdAlignVerticalBottom',                                         0x00000003);

  // Constants for enum WdCellVerticalAlignment
  define('wdCellAlignVerticalTop',                                        0x00000000);
  define('wdCellAlignVerticalCenter',                                     0x00000001);
  define('wdCellAlignVerticalBottom',                                     0x00000003);

  // Constants for enum WdTrailingCharacter
  define('wdTrailingTab',                                                 0x00000000);
  define('wdTrailingSpace',                                               0x00000001);
  define('wdTrailingNone',                                                0x00000002);

  // Constants for enum WdListGalleryType
  define('wdBulletGallery',                                               0x00000001);
  define('wdNumberGallery',                                               0x00000002);
  define('wdOutlineNumberGallery',                                        0x00000003);

  // Constants for enum WdListNumberStyle
  define('wdListNumberStyleArabic',                                       0x00000000);
  define('wdListNumberStyleUppercaseRoman',                               0x00000001);
  define('wdListNumberStyleLowercaseRoman',                               0x00000002);
  define('wdListNumberStyleUppercaseLetter',                              0x00000003);
  define('wdListNumberStyleLowercaseLetter',                              0x00000004);
  define('wdListNumberStyleOrdinal',                                      0x00000005);
  define('wdListNumberStyleCardinalText',                                 0x00000006);
  define('wdListNumberStyleOrdinalText',                                  0x00000007);
  define('wdListNumberStyleKanji',                                        0x0000000A);
  define('wdListNumberStyleKanjiDigit',                                   0x0000000B);
  define('wdListNumberStyleAiueoHalfWidth',                               0x0000000C);
  define('wdListNumberStyleIrohaHalfWidth',                               0x0000000D);
  define('wdListNumberStyleArabicFullWidth',                              0x0000000E);
  define('wdListNumberStyleKanjiTraditional',                             0x00000010);
  define('wdListNumberStyleKanjiTraditional2',                            0x00000011);
  define('wdListNumberStyleNumberInCircle',                               0x00000012);
  define('wdListNumberStyleAiueo',                                        0x00000014);
  define('wdListNumberStyleIroha',                                        0x00000015);
  define('wdListNumberStyleArabicLZ',                                     0x00000016);
  define('wdListNumberStyleBullet',                                       0x00000017);
  define('wdListNumberStyleGanada',                                       0x00000018);
  define('wdListNumberStyleChosung',                                      0x00000019);
  define('wdListNumberStyleGBNum1',                                       0x0000001A);
  define('wdListNumberStyleGBNum2',                                       0x0000001B);
  define('wdListNumberStyleGBNum3',                                       0x0000001C);
  define('wdListNumberStyleGBNum4',                                       0x0000001D);
  define('wdListNumberStyleZodiac1',                                      0x0000001E);
  define('wdListNumberStyleZodiac2',                                      0x0000001F);
  define('wdListNumberStyleZodiac3',                                      0x00000020);
  define('wdListNumberStyleTradChinNum1',                                 0x00000021);
  define('wdListNumberStyleTradChinNum2',                                 0x00000022);
  define('wdListNumberStyleTradChinNum3',                                 0x00000023);
  define('wdListNumberStyleTradChinNum4',                                 0x00000024);
  define('wdListNumberStyleSimpChinNum1',                                 0x00000025);
  define('wdListNumberStyleSimpChinNum2',                                 0x00000026);
  define('wdListNumberStyleSimpChinNum3',                                 0x00000027);
  define('wdListNumberStyleSimpChinNum4',                                 0x00000028);
  define('wdListNumberStyleHanjaRead',                                    0x00000029);
  define('wdListNumberStyleHanjaReadDigit',                               0x0000002A);
  define('wdListNumberStyleHangul',                                       0x0000002B);
  define('wdListNumberStyleHanja',                                        0x0000002C);
  define('wdListNumberStyleHebrew1',                                      0x0000002D);
  define('wdListNumberStyleArabic1',                                      0x0000002E);
  define('wdListNumberStyleHebrew2',                                      0x0000002F);
  define('wdListNumberStyleArabic2',                                      0x00000030);
  define('wdListNumberStyleLegal',                                        0x000000FD);
  define('wdListNumberStyleLegalLZ',                                      0x000000FE);
  define('wdListNumberStyleNone',                                         0x000000FF);

  // Constants for enum WdListNumberStyleHID

  // Constants for enum WdNoteNumberStyle
  define('wdNoteNumberStyleArabic',                                       0x00000000);
  define('wdNoteNumberStyleUppercaseRoman',                               0x00000001);
  define('wdNoteNumberStyleLowercaseRoman',                               0x00000002);
  define('wdNoteNumberStyleUppercaseLetter',                              0x00000003);
  define('wdNoteNumberStyleLowercaseLetter',                              0x00000004);
  define('wdNoteNumberStyleSymbol',                                       0x00000009);
  define('wdNoteNumberStyleArabicFullWidth',                              0x0000000E);
  define('wdNoteNumberStyleKanji',                                        0x0000000A);
  define('wdNoteNumberStyleKanjiDigit',                                   0x0000000B);
  define('wdNoteNumberStyleKanjiTraditional',                             0x00000010);
  define('wdNoteNumberStyleNumberInCircle',                               0x00000012);
  define('wdNoteNumberStyleHanjaRead',                                    0x00000029);
  define('wdNoteNumberStyleHanjaReadDigit',                               0x0000002A);
  define('wdNoteNumberStyleTradChinNum1',                                 0x00000021);
  define('wdNoteNumberStyleTradChinNum2',                                 0x00000022);
  define('wdNoteNumberStyleSimpChinNum1',                                 0x00000025);
  define('wdNoteNumberStyleSimpChinNum2',                                 0x00000026);
  define('wdNoteNumberStyleHebrewLetter1',                                0x0000002D);
  define('wdNoteNumberStyleArabicLetter1',                                0x0000002E);
  define('wdNoteNumberStyleHebrewLetter2',                                0x0000002F);
  define('wdNoteNumberStyleArabicLetter2',                                0x00000030);

  // Constants for enum WdNoteNumberStyleHID

  // Constants for enum WdCaptionNumberStyle
  define('wdCaptionNumberStyleArabic',                                    0x00000000);
  define('wdCaptionNumberStyleUppercaseRoman',                            0x00000001);
  define('wdCaptionNumberStyleLowercaseRoman',                            0x00000002);
  define('wdCaptionNumberStyleUppercaseLetter',                           0x00000003);
  define('wdCaptionNumberStyleLowercaseLetter',                           0x00000004);
  define('wdCaptionNumberStyleArabicFullWidth',                           0x0000000E);
  define('wdCaptionNumberStyleKanji',                                     0x0000000A);
  define('wdCaptionNumberStyleKanjiDigit',                                0x0000000B);
  define('wdCaptionNumberStyleKanjiTraditional',                          0x00000010);
  define('wdCaptionNumberStyleNumberInCircle',                            0x00000012);
  define('wdCaptionNumberStyleGanada',                                    0x00000018);
  define('wdCaptionNumberStyleChosung',                                   0x00000019);
  define('wdCaptionNumberStyleZodiac1',                                   0x0000001E);
  define('wdCaptionNumberStyleZodiac2',                                   0x0000001F);
  define('wdCaptionNumberStyleHanjaRead',                                 0x00000029);
  define('wdCaptionNumberStyleHanjaReadDigit',                            0x0000002A);
  define('wdCaptionNumberStyleTradChinNum2',                              0x00000022);
  define('wdCaptionNumberStyleTradChinNum3',                              0x00000023);
  define('wdCaptionNumberStyleSimpChinNum2',                              0x00000026);
  define('wdCaptionNumberStyleSimpChinNum3',                              0x00000027);
  define('wdCaptionNumberStyleHebrewLetter1',                             0x0000002D);
  define('wdCaptionNumberStyleArabicLetter1',                             0x0000002E);
  define('wdCaptionNumberStyleHebrewLetter2',                             0x0000002F);
  define('wdCaptionNumberStyleArabicLetter2',                             0x00000030);

  // Constants for enum WdCaptionNumberStyleHID

  // Constants for enum WdPageNumberStyle
  define('wdPageNumberStyleArabic',                                       0x00000000);
  define('wdPageNumberStyleUppercaseRoman',                               0x00000001);
  define('wdPageNumberStyleLowercaseRoman',                               0x00000002);
  define('wdPageNumberStyleUppercaseLetter',                              0x00000003);
  define('wdPageNumberStyleLowercaseLetter',                              0x00000004);
  define('wdPageNumberStyleArabicFullWidth',                              0x0000000E);
  define('wdPageNumberStyleKanji',                                        0x0000000A);
  define('wdPageNumberStyleKanjiDigit',                                   0x0000000B);
  define('wdPageNumberStyleKanjiTraditional',                             0x00000010);
  define('wdPageNumberStyleNumberInCircle',                               0x00000012);
  define('wdPageNumberStyleHanjaRead',                                    0x00000029);
  define('wdPageNumberStyleHanjaReadDigit',                               0x0000002A);
  define('wdPageNumberStyleTradChinNum1',                                 0x00000021);
  define('wdPageNumberStyleTradChinNum2',                                 0x00000022);
  define('wdPageNumberStyleSimpChinNum1',                                 0x00000025);
  define('wdPageNumberStyleSimpChinNum2',                                 0x00000026);
  define('wdPageNumberStyleHebrewLetter1',                                0x0000002D);
  define('wdPageNumberStyleArabicLetter1',                                0x0000002E);
  define('wdPageNumberStyleHebrewLetter2',                                0x0000002F);
  define('wdPageNumberStyleArabicLetter2',                                0x00000030);

  // Constants for enum WdPageNumberStyleHID

  // Constants for enum WdStatistic
  define('wdStatisticWords',                                              0x00000000);
  define('wdStatisticLines',                                              0x00000001);
  define('wdStatisticPages',                                              0x00000002);
  define('wdStatisticCharacters',                                         0x00000003);
  define('wdStatisticParagraphs',                                         0x00000004);
  define('wdStatisticCharactersWithSpaces',                               0x00000005);
  define('wdStatisticFarEastCharacters',                                  0x00000006);

  // Constants for enum WdStatisticHID

  // Constants for enum WdBuiltInProperty
  define('wdPropertyTitle',                                               0x00000001);
  define('wdPropertySubject',                                             0x00000002);
  define('wdPropertyAuthor',                                              0x00000003);
  define('wdPropertyKeywords',                                            0x00000004);
  define('wdPropertyComments',                                            0x00000005);
  define('wdPropertyTemplate',                                            0x00000006);
  define('wdPropertyLastAuthor',                                          0x00000007);
  define('wdPropertyRevision',                                            0x00000008);
  define('wdPropertyAppName',                                             0x00000009);
  define('wdPropertyTimeLastPrinted',                                     0x0000000A);
  define('wdPropertyTimeCreated',                                         0x0000000B);
  define('wdPropertyTimeLastSaved',                                       0x0000000C);
  define('wdPropertyVBATotalEdit',                                        0x0000000D);
  define('wdPropertyPages',                                               0x0000000E);
  define('wdPropertyWords',                                               0x0000000F);
  define('wdPropertyCharacters',                                          0x00000010);
  define('wdPropertySecurity',                                            0x00000011);
  define('wdPropertyCategory',                                            0x00000012);
  define('wdPropertyFormat',                                              0x00000013);
  define('wdPropertyManager',                                             0x00000014);
  define('wdPropertyCompany',                                             0x00000015);
  define('wdPropertyBytes',                                               0x00000016);
  define('wdPropertyLines',                                               0x00000017);
  define('wdPropertyParas',                                               0x00000018);
  define('wdPropertySlides',                                              0x00000019);
  define('wdPropertyNotes',                                               0x0000001A);
  define('wdPropertyHiddenSlides',                                        0x0000001B);
  define('wdPropertyMMClips',                                             0x0000001C);
  define('wdPropertyHyperlinkBase',                                       0x0000001D);
  define('wdPropertyCharsWSpaces',                                        0x0000001E);

  // Constants for enum WdLineSpacing
  define('wdLineSpaceSingle',                                             0x00000000);
  define('wdLineSpace1pt5',                                               0x00000001);
  define('wdLineSpaceDouble',                                             0x00000002);
  define('wdLineSpaceAtLeast',                                            0x00000003);
  define('wdLineSpaceExactly',                                            0x00000004);
  define('wdLineSpaceMultiple',                                           0x00000005);

  // Constants for enum WdNumberType
  define('wdNumberParagraph',                                             0x00000001);
  define('wdNumberListNum',                                               0x00000002);
  define('wdNumberAllNumbers',                                            0x00000003);

  // Constants for enum WdListType
  define('wdListNoNumbering',                                             0x00000000);
  define('wdListListNumOnly',                                             0x00000001);
  define('wdListBullet',                                                  0x00000002);
  define('wdListSimpleNumbering',                                         0x00000003);
  define('wdListOutlineNumbering',                                        0x00000004);
  define('wdListMixedNumbering',                                          0x00000005);

  // Constants for enum WdStoryType
  define('wdMainTextStory',                                               0x00000001);
  define('wdFootnotesStory',                                              0x00000002);
  define('wdEndnotesStory',                                               0x00000003);
  define('wdCommentsStory',                                               0x00000004);
  define('wdTextFrameStory',                                              0x00000005);
  define('wdEvenPagesHeaderStory',                                        0x00000006);
  define('wdPrimaryHeaderStory',                                          0x00000007);
  define('wdEvenPagesFooterStory',                                        0x00000008);
  define('wdPrimaryFooterStory',                                          0x00000009);
  define('wdFirstPageHeaderStory',                                        0x0000000A);
  define('wdFirstPageFooterStory',                                        0x0000000B);

  // Constants for enum WdSaveFormat
  define('wdFormatDocument',                                              0x00000000);
  define('wdFormatTemplate',                                              0x00000001);
  define('wdFormatText',                                                  0x00000002);
  define('wdFormatTextLineBreaks',                                        0x00000003);
  define('wdFormatDOSText',                                               0x00000004);
  define('wdFormatDOSTextLineBreaks',                                     0x00000005);
  define('wdFormatRTF',                                                   0x00000006);
  define('wdFormatUnicodeText',                                           0x00000007);
  define('wdFormatEncodedText',                                           0x00000007);
  define('wdFormatHTML',                                                  0x00000008);

  // Constants for enum WdOpenFormat
  define('wdOpenFormatAuto',                                              0x00000000);
  define('wdOpenFormatDocument',                                          0x00000001);
  define('wdOpenFormatTemplate',                                          0x00000002);
  define('wdOpenFormatRTF',                                               0x00000003);
  define('wdOpenFormatText',                                              0x00000004);
  define('wdOpenFormatUnicodeText',                                       0x00000005);
  define('wdOpenFormatEncodedText',                                       0x00000005);
  define('wdOpenFormatAllWord',                                           0x00000006);
  define('wdOpenFormatWebPages',                                          0x00000007);

  // Constants for enum WdHeaderFooterIndex
  define('wdHeaderFooterPrimary',                                         0x00000001);
  define('wdHeaderFooterFirstPage',                                       0x00000002);
  define('wdHeaderFooterEvenPages',                                       0x00000003);

  // Constants for enum WdTocFormat
  define('wdTOCTemplate',                                                 0x00000000);
  define('wdTOCClassic',                                                  0x00000001);
  define('wdTOCDistinctive',                                              0x00000002);
  define('wdTOCFancy',                                                    0x00000003);
  define('wdTOCModern',                                                   0x00000004);
  define('wdTOCFormal',                                                   0x00000005);
  define('wdTOCSimple',                                                   0x00000006);

  // Constants for enum WdTofFormat
  define('wdTOFTemplate',                                                 0x00000000);
  define('wdTOFClassic',                                                  0x00000001);
  define('wdTOFDistinctive',                                              0x00000002);
  define('wdTOFCentered',                                                 0x00000003);
  define('wdTOFFormal',                                                   0x00000004);
  define('wdTOFSimple',                                                   0x00000005);

  // Constants for enum WdToaFormat
  define('wdTOATemplate',                                                 0x00000000);
  define('wdTOAClassic',                                                  0x00000001);
  define('wdTOADistinctive',                                              0x00000002);
  define('wdTOAFormal',                                                   0x00000003);
  define('wdTOASimple',                                                   0x00000004);

  // Constants for enum WdLineStyle
  define('wdLineStyleNone',                                               0x00000000);
  define('wdLineStyleSingle',                                             0x00000001);
  define('wdLineStyleDot',                                                0x00000002);
  define('wdLineStyleDashSmallGap',                                       0x00000003);
  define('wdLineStyleDashLargeGap',                                       0x00000004);
  define('wdLineStyleDashDot',                                            0x00000005);
  define('wdLineStyleDashDotDot',                                         0x00000006);
  define('wdLineStyleDouble',                                             0x00000007);
  define('wdLineStyleTriple',                                             0x00000008);
  define('wdLineStyleThinThickSmallGap',                                  0x00000009);
  define('wdLineStyleThickThinSmallGap',                                  0x0000000A);
  define('wdLineStyleThinThickThinSmallGap',                              0x0000000B);
  define('wdLineStyleThinThickMedGap',                                    0x0000000C);
  define('wdLineStyleThickThinMedGap',                                    0x0000000D);
  define('wdLineStyleThinThickThinMedGap',                                0x0000000E);
  define('wdLineStyleThinThickLargeGap',                                  0x0000000F);
  define('wdLineStyleThickThinLargeGap',                                  0x00000010);
  define('wdLineStyleThinThickThinLargeGap',                              0x00000011);
  define('wdLineStyleSingleWavy',                                         0x00000012);
  define('wdLineStyleDoubleWavy',                                         0x00000013);
  define('wdLineStyleDashDotStroked',                                     0x00000014);
  define('wdLineStyleEmboss3D',                                           0x00000015);
  define('wdLineStyleEngrave3D',                                          0x00000016);
  define('wdLineStyleOutset',                                             0x00000017);
  define('wdLineStyleInset',                                              0x00000018);

  // Constants for enum WdLineWidth
  define('wdLineWidth025pt',                                              0x00000002);
  define('wdLineWidth050pt',                                              0x00000004);
  define('wdLineWidth075pt',                                              0x00000006);
  define('wdLineWidth100pt',                                              0x00000008);
  define('wdLineWidth150pt',                                              0x0000000C);
  define('wdLineWidth225pt',                                              0x00000012);
  define('wdLineWidth300pt',                                              0x00000018);
  define('wdLineWidth450pt',                                              0x00000024);
  define('wdLineWidth600pt',                                              0x00000030);

  // Constants for enum WdBreakType
  define('wdSectionBreakNextPage',                                        0x00000002);
  define('wdSectionBreakContinuous',                                      0x00000003);
  define('wdSectionBreakEvenPage',                                        0x00000004);
  define('wdSectionBreakOddPage',                                         0x00000005);
  define('wdLineBreak',                                                   0x00000006);
  define('wdPageBreak',                                                   0x00000007);
  define('wdColumnBreak',                                                 0x00000008);
  define('wdLineBreakClearLeft',                                          0x00000009);
  define('wdLineBreakClearRight',                                         0x0000000A);
  define('wdTextWrappingBreak',                                           0x0000000B);

  // Constants for enum WdTabLeader
  define('wdTabLeaderSpaces',                                             0x00000000);
  define('wdTabLeaderDots',                                               0x00000001);
  define('wdTabLeaderDashes',                                             0x00000002);
  define('wdTabLeaderLines',                                              0x00000003);
  define('wdTabLeaderHeavy',                                              0x00000004);
  define('wdTabLeaderMiddleDot',                                          0x00000005);

  // Constants for enum WdTabLeaderHID

  // Constants for enum WdMeasurementUnits
  define('wdInches',                                                      0x00000000);
  define('wdCentimeters',                                                 0x00000001);
  define('wdMillimeters',                                                 0x00000002);
  define('wdPoints',                                                      0x00000003);
  define('wdPicas',                                                       0x00000004);

  // Constants for enum WdMeasurementUnitsHID

  // Constants for enum WdDropPosition
  define('wdDropNone',                                                    0x00000000);
  define('wdDropNormal',                                                  0x00000001);
  define('wdDropMargin',                                                  0x00000002);

  // Constants for enum WdNumberingRule
  define('wdRestartContinuous',                                           0x00000000);
  define('wdRestartSection',                                              0x00000001);
  define('wdRestartPage',                                                 0x00000002);

  // Constants for enum WdFootnoteLocation
  define('wdBottomOfPage',                                                0x00000000);
  define('wdBeneathText',                                                 0x00000001);

  // Constants for enum WdEndnoteLocation
  define('wdEndOfSection',                                                0x00000000);
  define('wdEndOfDocument',                                               0x00000001);

  // Constants for enum WdSortSeparator
  define('wdSortSeparateByTabs',                                          0x00000000);
  define('wdSortSeparateByCommas',                                        0x00000001);
  define('wdSortSeparateByDefaultTableSeparator',                         0x00000002);

  // Constants for enum WdTableFieldSeparator
  define('wdSeparateByParagraphs',                                        0x00000000);
  define('wdSeparateByTabs',                                              0x00000001);
  define('wdSeparateByCommas',                                            0x00000002);
  define('wdSeparateByDefaultListSeparator',                              0x00000003);

  // Constants for enum WdSortFieldType
  define('wdSortFieldAlphanumeric',                                       0x00000000);
  define('wdSortFieldNumeric',                                            0x00000001);
  define('wdSortFieldDate',                                               0x00000002);
  define('wdSortFieldSyllable',                                           0x00000003);
  define('wdSortFieldJapanJIS',                                           0x00000004);
  define('wdSortFieldStroke',                                             0x00000005);
  define('wdSortFieldKoreaKS',                                            0x00000006);

  // Constants for enum WdSortFieldTypeHID

  // Constants for enum WdSortOrder
  define('wdSortOrderAscending',                                          0x00000000);
  define('wdSortOrderDescending',                                         0x00000001);

  // Constants for enum WdTableFormat
  define('wdTableFormatNone',                                             0x00000000);
  define('wdTableFormatSimple1',                                          0x00000001);
  define('wdTableFormatSimple2',                                          0x00000002);
  define('wdTableFormatSimple3',                                          0x00000003);
  define('wdTableFormatClassic1',                                         0x00000004);
  define('wdTableFormatClassic2',                                         0x00000005);
  define('wdTableFormatClassic3',                                         0x00000006);
  define('wdTableFormatClassic4',                                         0x00000007);
  define('wdTableFormatColorful1',                                        0x00000008);
  define('wdTableFormatColorful2',                                        0x00000009);
  define('wdTableFormatColorful3',                                        0x0000000A);
  define('wdTableFormatColumns1',                                         0x0000000B);
  define('wdTableFormatColumns2',                                         0x0000000C);
  define('wdTableFormatColumns3',                                         0x0000000D);
  define('wdTableFormatColumns4',                                         0x0000000E);
  define('wdTableFormatColumns5',                                         0x0000000F);
  define('wdTableFormatGrid1',                                            0x00000010);
  define('wdTableFormatGrid2',                                            0x00000011);
  define('wdTableFormatGrid3',                                            0x00000012);
  define('wdTableFormatGrid4',                                            0x00000013);
  define('wdTableFormatGrid5',                                            0x00000014);
  define('wdTableFormatGrid6',                                            0x00000015);
  define('wdTableFormatGrid7',                                            0x00000016);
  define('wdTableFormatGrid8',                                            0x00000017);
  define('wdTableFormatList1',                                            0x00000018);
  define('wdTableFormatList2',                                            0x00000019);
  define('wdTableFormatList3',                                            0x0000001A);
  define('wdTableFormatList4',                                            0x0000001B);
  define('wdTableFormatList5',                                            0x0000001C);
  define('wdTableFormatList6',                                            0x0000001D);
  define('wdTableFormatList7',                                            0x0000001E);
  define('wdTableFormatList8',                                            0x0000001F);
  define('wdTableFormat3DEffects1',                                       0x00000020);
  define('wdTableFormat3DEffects2',                                       0x00000021);
  define('wdTableFormat3DEffects3',                                       0x00000022);
  define('wdTableFormatContemporary',                                     0x00000023);
  define('wdTableFormatElegant',                                          0x00000024);
  define('wdTableFormatProfessional',                                     0x00000025);
  define('wdTableFormatSubtle1',                                          0x00000026);
  define('wdTableFormatSubtle2',                                          0x00000027);
  define('wdTableFormatWeb1',                                             0x00000028);
  define('wdTableFormatWeb2',                                             0x00000029);
  define('wdTableFormatWeb3',                                             0x0000002A);

  // Constants for enum WdTableFormatApply
  define('wdTableFormatApplyBorders',                                     0x00000001);
  define('wdTableFormatApplyShading',                                     0x00000002);
  define('wdTableFormatApplyFont',                                        0x00000004);
  define('wdTableFormatApplyColor',                                       0x00000008);
  define('wdTableFormatApplyAutoFit',                                     0x00000010);
  define('wdTableFormatApplyHeadingRows',                                 0x00000020);
  define('wdTableFormatApplyLastRow',                                     0x00000040);
  define('wdTableFormatApplyFirstColumn',                                 0x00000080);
  define('wdTableFormatApplyLastColumn',                                  0x00000100);

  // Constants for enum WdLanguageID
  define('wdLanguageNone',                                                0x00000000);
  define('wdNoProofing',                                                  0x00000400);
  define('wdAfrikaans',                                                   0x00000436);
  define('wdAlbanian',                                                    0x0000041C);
  define('wdArabicAlgeria',                                               0x00001401);
  define('wdArabicBahrain',                                               0x00003C01);
  define('wdArabicEgypt',                                                 0x00000C01);
  define('wdArabicIraq',                                                  0x00000801);
  define('wdArabicJordan',                                                0x00002C01);
  define('wdArabicKuwait',                                                0x00003401);
  define('wdArabicLebanon',                                               0x00003001);
  define('wdArabicLibya',                                                 0x00001001);
  define('wdArabicMorocco',                                               0x00001801);
  define('wdArabicOman',                                                  0x00002001);
  define('wdArabicQatar',                                                 0x00004001);
  define('wdArabic',                                                      0x00000401);
  define('wdArabicSyria',                                                 0x00002801);
  define('wdArabicTunisia',                                               0x00001C01);
  define('wdArabicUAE',                                                   0x00003801);
  define('wdArabicYemen',                                                 0x00002401);
  define('wdArmenian',                                                    0x0000042B);
  define('wdAssamese',                                                    0x0000044D);
  define('wdAzeriCyrillic',                                               0x0000082C);
  define('wdAzeriLatin',                                                  0x0000042C);
  define('wdBasque',                                                      0x0000042D);
  define('wdByelorussian',                                                0x00000423);
  define('wdBengali',                                                     0x00000445);
  define('wdBulgarian',                                                   0x00000402);
  define('wdBurmese',                                                     0x00000455);
  define('wdCatalan',                                                     0x00000403);
  define('wdChineseHongKong',                                             0x00000C04);
  define('wdChineseMacao',                                                0x00001404);
  define('wdSimplifiedChinese',                                           0x00000804);
  define('wdChineseSingapore',                                            0x00001004);
  define('wdTraditionalChinese',                                          0x00000404);
  define('wdCroatian',                                                    0x0000041A);
  define('wdCzech',                                                       0x00000405);
  define('wdDanish',                                                      0x00000406);
  define('wdBelgianDutch',                                                0x00000813);
  define('wdDutch',                                                       0x00000413);
  define('wdEnglishAUS',                                                  0x00000C09);
  define('wdEnglishBelize',                                               0x00002809);
  define('wdEnglishCanadian',                                             0x00001009);
  define('wdEnglishCaribbean',                                            0x00002409);
  define('wdEnglishIreland',                                              0x00001809);
  define('wdEnglishJamaica',                                              0x00002009);
  define('wdEnglishNewZealand',                                           0x00001409);
  define('wdEnglishPhilippines',                                          0x00003409);
  define('wdEnglishSouthAfrica',                                          0x00001C09);
  define('wdEnglishTrinidad',                                             0x00002C09);
  define('wdEnglishUK',                                                   0x00000809);
  define('wdEnglishUS',                                                   0x00000409);
  define('wdEnglishZimbabwe',                                             0x00003009);
  define('wdEstonian',                                                    0x00000425);
  define('wdFaeroese',                                                    0x00000438);
  define('wdFarsi',                                                       0x00000429);
  define('wdFinnish',                                                     0x0000040B);
  define('wdBelgianFrench',                                               0x0000080C);
  define('wdFrenchCameroon',                                              0x00002C0C);
  define('wdFrenchCanadian',                                              0x00000C0C);
  define('wdFrenchCotedIvoire',                                           0x0000300C);
  define('wdFrench',                                                      0x0000040C);
  define('wdFrenchLuxembourg',                                            0x0000140C);
  define('wdFrenchMali',                                                  0x0000340C);
  define('wdFrenchMonaco',                                                0x0000180C);
  define('wdFrenchReunion',                                               0x0000200C);
  define('wdFrenchSenegal',                                               0x0000280C);
  define('wdSwissFrench',                                                 0x0000100C);
  define('wdFrenchWestIndies',                                            0x00001C0C);
  define('wdFrenchZaire',                                                 0x0000240C);
  define('wdFrisianNetherlands',                                          0x00000462);
  define('wdGaelicIreland',                                               0x0000083C);
  define('wdGaelicScotland',                                              0x0000043C);
  define('wdGalician',                                                    0x00000456);
  define('wdGeorgian',                                                    0x00000437);
  define('wdGermanAustria',                                               0x00000C07);
  define('wdGerman',                                                      0x00000407);
  define('wdGermanLiechtenstein',                                         0x00001407);
  define('wdGermanLuxembourg',                                            0x00001007);
  define('wdSwissGerman',                                                 0x00000807);
  define('wdGreek',                                                       0x00000408);
  define('wdGujarati',                                                    0x00000447);
  define('wdHebrew',                                                      0x0000040D);
  define('wdHindi',                                                       0x00000439);
  define('wdHungarian',                                                   0x0000040E);
  define('wdIcelandic',                                                   0x0000040F);
  define('wdIndonesian',                                                  0x00000421);
  define('wdItalian',                                                     0x00000410);
  define('wdSwissItalian',                                                0x00000810);
  define('wdJapanese',                                                    0x00000411);
  define('wdKannada',                                                     0x0000044B);
  define('wdKashmiri',                                                    0x00000460);
  define('wdKazakh',                                                      0x0000043F);
  define('wdKhmer',                                                       0x00000453);
  define('wdKirghiz',                                                     0x00000440);
  define('wdKonkani',                                                     0x00000457);
  define('wdKorean',                                                      0x00000412);
  define('wdLao',                                                         0x00000454);
  define('wdLatvian',                                                     0x00000426);
  define('wdLithuanian',                                                  0x00000427);
  define('wdMacedonian',                                                  0x0000042F);
  define('wdMalaysian',                                                   0x0000043E);
  define('wdMalayBruneiDarussalam',                                       0x0000083E);
  define('wdMalayalam',                                                   0x0000044C);
  define('wdMaltese',                                                     0x0000043A);
  define('wdManipuri',                                                    0x00000458);
  define('wdMarathi',                                                     0x0000044E);
  define('wdMongolian',                                                   0x00000450);
  define('wdNepali',                                                      0x00000461);
  define('wdNorwegianBokmol',                                             0x00000414);
  define('wdNorwegianNynorsk',                                            0x00000814);
  define('wdOriya',                                                       0x00000448);
  define('wdPolish',                                                      0x00000415);
  define('wdBrazilianPortuguese',                                         0x00000416);
  define('wdPortuguese',                                                  0x00000816);
  define('wdPunjabi',                                                     0x00000446);
  define('wdRhaetoRomanic',                                               0x00000417);
  define('wdRomanianMoldova',                                             0x00000818);
  define('wdRomanian',                                                    0x00000418);
  define('wdRussianMoldova',                                              0x00000819);
  define('wdRussian',                                                     0x00000419);
  define('wdSamiLappish',                                                 0x0000043B);
  define('wdSanskrit',                                                    0x0000044F);
  define('wdSerbianCyrillic',                                             0x00000C1A);
  define('wdSerbianLatin',                                                0x0000081A);
  define('wdSindhi',                                                      0x00000459);
  define('wdSlovak',                                                      0x0000041B);
  define('wdSlovenian',                                                   0x00000424);
  define('wdSorbian',                                                     0x0000042E);
  define('wdSpanishArgentina',                                            0x00002C0A);
  define('wdSpanishBolivia',                                              0x0000400A);
  define('wdSpanishChile',                                                0x0000340A);
  define('wdSpanishColombia',                                             0x0000240A);
  define('wdSpanishCostaRica',                                            0x0000140A);
  define('wdSpanishDominicanRepublic',                                    0x00001C0A);
  define('wdSpanishEcuador',                                              0x0000300A);
  define('wdSpanishElSalvador',                                           0x0000440A);
  define('wdSpanishGuatemala',                                            0x0000100A);
  define('wdSpanishHonduras',                                             0x0000480A);
  define('wdMexicanSpanish',                                              0x0000080A);
  define('wdSpanishNicaragua',                                            0x00004C0A);
  define('wdSpanishPanama',                                               0x0000180A);
  define('wdSpanishParaguay',                                             0x00003C0A);
  define('wdSpanishPeru',                                                 0x0000280A);
  define('wdSpanishPuertoRico',                                           0x0000500A);
  define('wdSpanishModernSort',                                           0x00000C0A);
  define('wdSpanish',                                                     0x0000040A);
  define('wdSpanishUruguay',                                              0x0000380A);
  define('wdSpanishVenezuela',                                            0x0000200A);
  define('wdSesotho',                                                     0x00000430);
  define('wdSutu',                                                        0x00000430);
  define('wdSwahili',                                                     0x00000441);
  define('wdSwedishFinland',                                              0x0000081D);
  define('wdSwedish',                                                     0x0000041D);
  define('wdTajik',                                                       0x00000428);
  define('wdTamil',                                                       0x00000449);
  define('wdTatar',                                                       0x00000444);
  define('wdTelugu',                                                      0x0000044A);
  define('wdThai',                                                        0x0000041E);
  define('wdTibetan',                                                     0x00000451);
  define('wdTsonga',                                                      0x00000431);
  define('wdTswana',                                                      0x00000432);
  define('wdTurkish',                                                     0x0000041F);
  define('wdTurkmen',                                                     0x00000442);
  define('wdUkrainian',                                                   0x00000422);
  define('wdUrdu',                                                        0x00000420);
  define('wdUzbekCyrillic',                                               0x00000843);
  define('wdUzbekLatin',                                                  0x00000443);
  define('wdVenda',                                                       0x00000433);
  define('wdVietnamese',                                                  0x0000042A);
  define('wdWelsh',                                                       0x00000452);
  define('wdXhosa',                                                       0x00000434);
  define('wdZulu',                                                        0x00000435);

  // Constants for enum WdFieldType
  define('wdFieldEmpty',                                                  0xFFFFFFFF);
  define('wdFieldRef',                                                    0x00000003);
  define('wdFieldIndexEntry',                                             0x00000004);
  define('wdFieldFootnoteRef',                                            0x00000005);
  define('wdFieldSet',                                                    0x00000006);
  define('wdFieldIf',                                                     0x00000007);
  define('wdFieldIndex',                                                  0x00000008);
  define('wdFieldTOCEntry',                                               0x00000009);
  define('wdFieldStyleRef',                                               0x0000000A);
  define('wdFieldRefDoc',                                                 0x0000000B);
  define('wdFieldSequence',                                               0x0000000C);
  define('wdFieldTOC',                                                    0x0000000D);
  define('wdFieldInfo',                                                   0x0000000E);
  define('wdFieldTitle',                                                  0x0000000F);
  define('wdFieldSubject',                                                0x00000010);
  define('wdFieldAuthor',                                                 0x00000011);
  define('wdFieldKeyWord',                                                0x00000012);
  define('wdFieldComments',                                               0x00000013);
  define('wdFieldLastSavedBy',                                            0x00000014);
  define('wdFieldCreateDate',                                             0x00000015);
  define('wdFieldSaveDate',                                               0x00000016);
  define('wdFieldPrintDate',                                              0x00000017);
  define('wdFieldRevisionNum',                                            0x00000018);
  define('wdFieldEditTime',                                               0x00000019);
  define('wdFieldNumPages',                                               0x0000001A);
  define('wdFieldNumWords',                                               0x0000001B);
  define('wdFieldNumChars',                                               0x0000001C);
  define('wdFieldFileName',                                               0x0000001D);
  define('wdFieldTemplate',                                               0x0000001E);
  define('wdFieldDate',                                                   0x0000001F);
  define('wdFieldTime',                                                   0x00000020);
  define('wdFieldPage',                                                   0x00000021);
  define('wdFieldExpression',                                             0x00000022);
  define('wdFieldQuote',                                                  0x00000023);
  define('wdFieldInclude',                                                0x00000024);
  define('wdFieldPageRef',                                                0x00000025);
  define('wdFieldAsk',                                                    0x00000026);
  define('wdFieldFillIn',                                                 0x00000027);
  define('wdFieldData',                                                   0x00000028);
  define('wdFieldNext',                                                   0x00000029);
  define('wdFieldNextIf',                                                 0x0000002A);
  define('wdFieldSkipIf',                                                 0x0000002B);
  define('wdFieldMergeRec',                                               0x0000002C);
  define('wdFieldDDE',                                                    0x0000002D);
  define('wdFieldDDEAuto',                                                0x0000002E);
  define('wdFieldGlossary',                                               0x0000002F);
  define('wdFieldPrint',                                                  0x00000030);
  define('wdFieldFormula',                                                0x00000031);
  define('wdFieldGoToButton',                                             0x00000032);
  define('wdFieldMacroButton',                                            0x00000033);
  define('wdFieldAutoNumOutline',                                         0x00000034);
  define('wdFieldAutoNumLegal',                                           0x00000035);
  define('wdFieldAutoNum',                                                0x00000036);
  define('wdFieldImport',                                                 0x00000037);
  define('wdFieldLink',                                                   0x00000038);
  define('wdFieldSymbol',                                                 0x00000039);
  define('wdFieldEmbed',                                                  0x0000003A);
  define('wdFieldMergeField',                                             0x0000003B);
  define('wdFieldUserName',                                               0x0000003C);
  define('wdFieldUserInitials',                                           0x0000003D);
  define('wdFieldUserAddress',                                            0x0000003E);
  define('wdFieldBarCode',                                                0x0000003F);
  define('wdFieldDocVariable',                                            0x00000040);
  define('wdFieldSection',                                                0x00000041);
  define('wdFieldSectionPages',                                           0x00000042);
  define('wdFieldIncludePicture',                                         0x00000043);
  define('wdFieldIncludeText',                                            0x00000044);
  define('wdFieldFileSize',                                               0x00000045);
  define('wdFieldFormTextInput',                                          0x00000046);
  define('wdFieldFormCheckBox',                                           0x00000047);
  define('wdFieldNoteRef',                                                0x00000048);
  define('wdFieldTOA',                                                    0x00000049);
  define('wdFieldTOAEntry',                                               0x0000004A);
  define('wdFieldMergeSeq',                                               0x0000004B);
  define('wdFieldPrivate',                                                0x0000004D);
  define('wdFieldDatabase',                                               0x0000004E);
  define('wdFieldAutoText',                                               0x0000004F);
  define('wdFieldCompare',                                                0x00000050);
  define('wdFieldAddin',                                                  0x00000051);
  define('wdFieldSubscriber',                                             0x00000052);
  define('wdFieldFormDropDown',                                           0x00000053);
  define('wdFieldAdvance',                                                0x00000054);
  define('wdFieldDocProperty',                                            0x00000055);
  define('wdFieldOCX',                                                    0x00000057);
  define('wdFieldHyperlink',                                              0x00000058);
  define('wdFieldAutoTextList',                                           0x00000059);
  define('wdFieldListNum',                                                0x0000005A);
  define('wdFieldHTMLActiveX',                                            0x0000005B);

  // Constants for enum WdBuiltinStyle
  define('wdStyleNormal',                                                 0xFFFFFFFF);
  define('wdStyleEnvelopeAddress',                                        0xFFFFFFDB);
  define('wdStyleEnvelopeReturn',                                         0xFFFFFFDA);
  define('wdStyleBodyText',                                               0xFFFFFFBD);
  define('wdStyleHeading1',                                               0xFFFFFFFE);
  define('wdStyleHeading2',                                               0xFFFFFFFD);
  define('wdStyleHeading3',                                               0xFFFFFFFC);
  define('wdStyleHeading4',                                               0xFFFFFFFB);
  define('wdStyleHeading5',                                               0xFFFFFFFA);
  define('wdStyleHeading6',                                               0xFFFFFFF9);
  define('wdStyleHeading7',                                               0xFFFFFFF8);
  define('wdStyleHeading8',                                               0xFFFFFFF7);
  define('wdStyleHeading9',                                               0xFFFFFFF6);
  define('wdStyleIndex1',                                                 0xFFFFFFF5);
  define('wdStyleIndex2',                                                 0xFFFFFFF4);
  define('wdStyleIndex3',                                                 0xFFFFFFF3);
  define('wdStyleIndex4',                                                 0xFFFFFFF2);
  define('wdStyleIndex5',                                                 0xFFFFFFF1);
  define('wdStyleIndex6',                                                 0xFFFFFFF0);
  define('wdStyleIndex7',                                                 0xFFFFFFEF);
  define('wdStyleIndex8',                                                 0xFFFFFFEE);
  define('wdStyleIndex9',                                                 0xFFFFFFED);
  define('wdStyleTOC1',                                                   0xFFFFFFEC);
  define('wdStyleTOC2',                                                   0xFFFFFFEB);
  define('wdStyleTOC3',                                                   0xFFFFFFEA);
  define('wdStyleTOC4',                                                   0xFFFFFFE9);
  define('wdStyleTOC5',                                                   0xFFFFFFE8);
  define('wdStyleTOC6',                                                   0xFFFFFFE7);
  define('wdStyleTOC7',                                                   0xFFFFFFE6);
  define('wdStyleTOC8',                                                   0xFFFFFFE5);
  define('wdStyleTOC9',                                                   0xFFFFFFE4);
  define('wdStyleNormalIndent',                                           0xFFFFFFE3);
  define('wdStyleFootnoteText',                                           0xFFFFFFE2);
  define('wdStyleCommentText',                                            0xFFFFFFE1);
  define('wdStyleHeader',                                                 0xFFFFFFE0);
  define('wdStyleFooter',                                                 0xFFFFFFDF);
  define('wdStyleIndexHeading',                                           0xFFFFFFDE);
  define('wdStyleCaption',                                                0xFFFFFFDD);
  define('wdStyleTableOfFigures',                                         0xFFFFFFDC);
  define('wdStyleFootnoteReference',                                      0xFFFFFFD9);
  define('wdStyleCommentReference',                                       0xFFFFFFD8);
  define('wdStyleLineNumber',                                             0xFFFFFFD7);
  define('wdStylePageNumber',                                             0xFFFFFFD6);
  define('wdStyleEndnoteReference',                                       0xFFFFFFD5);
  define('wdStyleEndnoteText',                                            0xFFFFFFD4);
  define('wdStyleTableOfAuthorities',                                     0xFFFFFFD3);
  define('wdStyleMacroText',                                              0xFFFFFFD2);
  define('wdStyleTOAHeading',                                             0xFFFFFFD1);
  define('wdStyleList',                                                   0xFFFFFFD0);
  define('wdStyleListBullet',                                             0xFFFFFFCF);
  define('wdStyleListNumber',                                             0xFFFFFFCE);
  define('wdStyleList2',                                                  0xFFFFFFCD);
  define('wdStyleList3',                                                  0xFFFFFFCC);
  define('wdStyleList4',                                                  0xFFFFFFCB);
  define('wdStyleList5',                                                  0xFFFFFFCA);
  define('wdStyleListBullet2',                                            0xFFFFFFC9);
  define('wdStyleListBullet3',                                            0xFFFFFFC8);
  define('wdStyleListBullet4',                                            0xFFFFFFC7);
  define('wdStyleListBullet5',                                            0xFFFFFFC6);
  define('wdStyleListNumber2',                                            0xFFFFFFC5);
  define('wdStyleListNumber3',                                            0xFFFFFFC4);
  define('wdStyleListNumber4',                                            0xFFFFFFC3);
  define('wdStyleListNumber5',                                            0xFFFFFFC2);
  define('wdStyleTitle',                                                  0xFFFFFFC1);
  define('wdStyleClosing',                                                0xFFFFFFC0);
  define('wdStyleSignature',                                              0xFFFFFFBF);
  define('wdStyleDefaultParagraphFont',                                   0xFFFFFFBE);
  define('wdStyleBodyTextIndent',                                         0xFFFFFFBC);
  define('wdStyleListContinue',                                           0xFFFFFFBB);
  define('wdStyleListContinue2',                                          0xFFFFFFBA);
  define('wdStyleListContinue3',                                          0xFFFFFFB9);
  define('wdStyleListContinue4',                                          0xFFFFFFB8);
  define('wdStyleListContinue5',                                          0xFFFFFFB7);
  define('wdStyleMessageHeader',                                          0xFFFFFFB6);
  define('wdStyleSubtitle',                                               0xFFFFFFB5);
  define('wdStyleSalutation',                                             0xFFFFFFB4);
  define('wdStyleDate',                                                   0xFFFFFFB3);
  define('wdStyleBodyTextFirstIndent',                                    0xFFFFFFB2);
  define('wdStyleBodyTextFirstIndent2',                                   0xFFFFFFB1);
  define('wdStyleNoteHeading',                                            0xFFFFFFB0);
  define('wdStyleBodyText2',                                              0xFFFFFFAF);
  define('wdStyleBodyText3',                                              0xFFFFFFAE);
  define('wdStyleBodyTextIndent2',                                        0xFFFFFFAD);
  define('wdStyleBodyTextIndent3',                                        0xFFFFFFAC);
  define('wdStyleBlockQuotation',                                         0xFFFFFFAB);
  define('wdStyleHyperlink',                                              0xFFFFFFAA);
  define('wdStyleHyperlinkFollowed',                                      0xFFFFFFA9);
  define('wdStyleStrong',                                                 0xFFFFFFA8);
  define('wdStyleEmphasis',                                               0xFFFFFFA7);
  define('wdStyleNavPane',                                                0xFFFFFFA6);
  define('wdStylePlainText',                                              0xFFFFFFA5);
  define('wdStyleHtmlNormal',                                             0xFFFFFFA1);
  define('wdStyleHtmlAcronym',                                            0xFFFFFFA0);
  define('wdStyleHtmlAddress',                                            0xFFFFFF9F);
  define('wdStyleHtmlCite',                                               0xFFFFFF9E);
  define('wdStyleHtmlCode',                                               0xFFFFFF9D);
  define('wdStyleHtmlDfn',                                                0xFFFFFF9C);
  define('wdStyleHtmlKbd',                                                0xFFFFFF9B);
  define('wdStyleHtmlPre',                                                0xFFFFFF9A);
  define('wdStyleHtmlSamp',                                               0xFFFFFF99);
  define('wdStyleHtmlTt',                                                 0xFFFFFF98);
  define('wdStyleHtmlVar',                                                0xFFFFFF97);

  // Constants for enum WdWordDialogTab
  define('wdDialogToolsOptionsTabView',                                   0x000000CC);
  define('wdDialogToolsOptionsTabGeneral',                                0x000000CB);
  define('wdDialogToolsOptionsTabEdit',                                   0x000000E0);
  define('wdDialogToolsOptionsTabPrint',                                  0x000000D0);
  define('wdDialogToolsOptionsTabSave',                                   0x000000D1);
  define('wdDialogToolsOptionsTabProofread',                              0x000000D3);
  define('wdDialogToolsOptionsTabTrackChanges',                           0x00000182);
  define('wdDialogToolsOptionsTabUserInfo',                               0x000000D5);
  define('wdDialogToolsOptionsTabCompatibility',                          0x0000020D);
  define('wdDialogToolsOptionsTabTypography',                             0x000002E3);
  define('wdDialogToolsOptionsTabFileLocations',                          0x000000E1);
  define('wdDialogToolsOptionsTabFuzzy',                                  0x00000316);
  define('wdDialogToolsOptionsTabHangulHanjaConversion',                  0x00000312);
  define('wdDialogToolsOptionsTabBidi',                                   0x00000405);
  define('wdDialogFilePageSetupTabMargins',                               0x000249F0);
  define('wdDialogFilePageSetupTabPaperSize',                             0x000249F1);
  define('wdDialogFilePageSetupTabPaperSource',                           0x000249F2);
  define('wdDialogFilePageSetupTabLayout',                                0x000249F3);
  define('wdDialogFilePageSetupTabCharsLines',                            0x000249F4);
  define('wdDialogInsertSymbolTabSymbols',                                0x00030D40);
  define('wdDialogInsertSymbolTabSpecialCharacters',                      0x00030D41);
  define('wdDialogNoteOptionsTabAllFootnotes',                            0x000493E0);
  define('wdDialogNoteOptionsTabAllEndnotes',                             0x000493E1);
  define('wdDialogInsertIndexAndTablesTabIndex',                          0x00061A80);
  define('wdDialogInsertIndexAndTablesTabTableOfContents',                0x00061A81);
  define('wdDialogInsertIndexAndTablesTabTableOfFigures',                 0x00061A82);
  define('wdDialogInsertIndexAndTablesTabTableOfAuthorities',             0x00061A83);
  define('wdDialogOrganizerTabStyles',                                    0x0007A120);
  define('wdDialogOrganizerTabAutoText',                                  0x0007A121);
  define('wdDialogOrganizerTabCommandBars',                               0x0007A122);
  define('wdDialogOrganizerTabMacros',                                    0x0007A123);
  define('wdDialogFormatFontTabFont',                                     0x000927C0);
  define('wdDialogFormatFontTabCharacterSpacing',                         0x000927C1);
  define('wdDialogFormatFontTabAnimation',                                0x000927C2);
  define('wdDialogFormatBordersAndShadingTabBorders',                     0x000AAE60);
  define('wdDialogFormatBordersAndShadingTabPageBorder',                  0x000AAE61);
  define('wdDialogFormatBordersAndShadingTabShading',                     0x000AAE62);
  define('wdDialogToolsEnvelopesAndLabelsTabEnvelopes',                   0x000C3500);
  define('wdDialogToolsEnvelopesAndLabelsTabLabels',                      0x000C3501);
  define('wdDialogFormatParagraphTabIndentsAndSpacing',                   0x000F4240);
  define('wdDialogFormatParagraphTabTextFlow',                            0x000F4241);
  define('wdDialogFormatParagraphTabTeisai',                              0x000F4242);
  define('wdDialogFormatDrawingObjectTabColorsAndLines',                  0x00124F80);
  define('wdDialogFormatDrawingObjectTabSize',                            0x00124F81);
  define('wdDialogFormatDrawingObjectTabPosition',                        0x00124F82);
  define('wdDialogFormatDrawingObjectTabWrapping',                        0x00124F83);
  define('wdDialogFormatDrawingObjectTabPicture',                         0x00124F84);
  define('wdDialogFormatDrawingObjectTabTextbox',                         0x00124F85);
  define('wdDialogFormatDrawingObjectTabWeb',                             0x00124F86);
  define('wdDialogFormatDrawingObjectTabHR',                              0x00124F87);
  define('wdDialogToolsAutoCorrectExceptionsTabFirstLetter',              0x00155CC0);
  define('wdDialogToolsAutoCorrectExceptionsTabInitialCaps',              0x00155CC1);
  define('wdDialogToolsAutoCorrectExceptionsTabHangulAndAlphabet',        0x00155CC2);
  define('wdDialogToolsAutoCorrectExceptionsTabIac',                      0x00155CC3);
  define('wdDialogFormatBulletsAndNumberingTabBulleted',                  0x0016E360);
  define('wdDialogFormatBulletsAndNumberingTabNumbered',                  0x0016E361);
  define('wdDialogFormatBulletsAndNumberingTabOutlineNumbered',           0x0016E362);
  define('wdDialogLetterWizardTabLetterFormat',                           0x00186A00);
  define('wdDialogLetterWizardTabRecipientInfo',                          0x00186A01);
  define('wdDialogLetterWizardTabOtherElements',                          0x00186A02);
  define('wdDialogLetterWizardTabSenderInfo',                             0x00186A03);
  define('wdDialogToolsAutoManagerTabAutoCorrect',                        0x0019F0A0);
  define('wdDialogToolsAutoManagerTabAutoFormatAsYouType',                0x0019F0A1);
  define('wdDialogToolsAutoManagerTabAutoText',                           0x0019F0A2);
  define('wdDialogToolsAutoManagerTabAutoFormat',                         0x0019F0A3);
  define('wdDialogEmailOptionsTabSignature',                              0x001CFDE0);
  define('wdDialogEmailOptionsTabStationary',                             0x001CFDE1);
  define('wdDialogEmailOptionsTabQuoting',                                0x001CFDE2);
  define('wdDialogWebOptionsGeneral',                                     0x001E8480);
  define('wdDialogWebOptionsFiles',                                       0x001E8481);
  define('wdDialogWebOptionsPictures',                                    0x001E8482);
  define('wdDialogWebOptionsEncoding',                                    0x001E8483);
  define('wdDialogWebOptionsFonts',                                       0x001E8484);

  // Constants for enum WdWordDialogTabHID

  // Constants for enum WdWordDialog
  define('wdDialogHelpAbout',                                             0x00000009);
  define('wdDialogHelpWordPerfectHelp',                                   0x0000000A);
  define('wdDialogHelpWordPerfectHelpOptions',                            0x000001FF);
  define('wdDialogFormatChangeCase',                                      0x00000142);
  define('wdDialogToolsOptionsFuzzy',                                     0x00000316);
  define('wdDialogToolsWordCount',                                        0x000000E4);
  define('wdDialogDocumentStatistics',                                    0x0000004E);
  define('wdDialogFileNew',                                               0x0000004F);
  define('wdDialogFileOpen',                                              0x00000050);
  define('wdDialogMailMergeOpenDataSource',                               0x00000051);
  define('wdDialogMailMergeOpenHeaderSource',                             0x00000052);
  define('wdDialogMailMergeUseAddressBook',                               0x0000030B);
  define('wdDialogFileSaveAs',                                            0x00000054);
  define('wdDialogFileSummaryInfo',                                       0x00000056);
  define('wdDialogToolsTemplates',                                        0x00000057);
  define('wdDialogOrganizer',                                             0x000000DE);
  define('wdDialogFilePrint',                                             0x00000058);
  define('wdDialogMailMerge',                                             0x000002A4);
  define('wdDialogMailMergeCheck',                                        0x000002A5);
  define('wdDialogMailMergeQueryOptions',                                 0x000002A9);
  define('wdDialogMailMergeFindRecord',                                   0x00000239);
  define('wdDialogMailMergeInsertIf',                                     0x00000FD1);
  define('wdDialogMailMergeInsertNextIf',                                 0x00000FD5);
  define('wdDialogMailMergeInsertSkipIf',                                 0x00000FD7);
  define('wdDialogMailMergeInsertFillIn',                                 0x00000FD0);
  define('wdDialogMailMergeInsertAsk',                                    0x00000FCF);
  define('wdDialogMailMergeInsertSet',                                    0x00000FD6);
  define('wdDialogMailMergeHelper',                                       0x000002A8);
  define('wdDialogLetterWizard',                                          0x00000335);
  define('wdDialogFilePrintSetup',                                        0x00000061);
  define('wdDialogFileFind',                                              0x00000063);
  define('wdDialogMailMergeCreateDataSource',                             0x00000282);
  define('wdDialogMailMergeCreateHeaderSource',                           0x00000283);
  define('wdDialogEditPasteSpecial',                                      0x0000006F);
  define('wdDialogEditFind',                                              0x00000070);
  define('wdDialogEditReplace',                                           0x00000075);
  define('wdDialogEditGoToOld',                                           0x0000032B);
  define('wdDialogEditGoTo',                                              0x00000380);
  define('wdDialogCreateAutoText',                                        0x00000368);
  define('wdDialogEditAutoText',                                          0x000003D9);
  define('wdDialogEditLinks',                                             0x0000007C);
  define('wdDialogEditObject',                                            0x0000007D);
  define('wdDialogConvertObject',                                         0x00000188);
  define('wdDialogTableToText',                                           0x00000080);
  define('wdDialogTextToTable',                                           0x0000007F);
  define('wdDialogTableInsertTable',                                      0x00000081);
  define('wdDialogTableInsertCells',                                      0x00000082);
  define('wdDialogTableInsertRow',                                        0x00000083);
  define('wdDialogTableDeleteCells',                                      0x00000085);
  define('wdDialogTableSplitCells',                                       0x00000089);
  define('wdDialogTableFormula',                                          0x0000015C);
  define('wdDialogTableAutoFormat',                                       0x00000233);
  define('wdDialogTableFormatCell',                                       0x00000264);
  define('wdDialogViewZoom',                                              0x00000241);
  define('wdDialogNewToolbar',                                            0x0000024A);
  define('wdDialogInsertBreak',                                           0x0000009F);
  define('wdDialogInsertFootnote',                                        0x00000172);
  define('wdDialogInsertSymbol',                                          0x000000A2);
  define('wdDialogInsertPicture',                                         0x000000A3);
  define('wdDialogInsertFile',                                            0x000000A4);
  define('wdDialogInsertDateTime',                                        0x000000A5);
  define('wdDialogInsertNumber',                                          0x0000032C);
  define('wdDialogInsertField',                                           0x000000A6);
  define('wdDialogInsertDatabase',                                        0x00000155);
  define('wdDialogInsertMergeField',                                      0x000000A7);
  define('wdDialogInsertBookmark',                                        0x000000A8);
  define('wdDialogInsertHyperlink',                                       0x0000039D);
  define('wdDialogMarkIndexEntry',                                        0x000000A9);
  define('wdDialogMarkCitation',                                          0x000001CF);
  define('wdDialogEditTOACategory',                                       0x00000271);
  define('wdDialogInsertIndexAndTables',                                  0x000001D9);
  define('wdDialogInsertIndex',                                           0x000000AA);
  define('wdDialogInsertTableOfContents',                                 0x000000AB);
  define('wdDialogMarkTableOfContentsEntry',                              0x000001BA);
  define('wdDialogInsertTableOfFigures',                                  0x000001D8);
  define('wdDialogInsertTableOfAuthorities',                              0x000001D7);
  define('wdDialogInsertObject',                                          0x000000AC);
  define('wdDialogFormatCallout',                                         0x00000262);
  define('wdDialogDrawSnapToGrid',                                        0x00000279);
  define('wdDialogDrawAlign',                                             0x0000027A);
  define('wdDialogToolsEnvelopesAndLabels',                               0x0000025F);
  define('wdDialogToolsCreateEnvelope',                                   0x000000AD);
  define('wdDialogToolsCreateLabels',                                     0x000001E9);
  define('wdDialogToolsProtectDocument',                                  0x000001F7);
  define('wdDialogToolsProtectSection',                                   0x00000242);
  define('wdDialogToolsUnprotectDocument',                                0x00000209);
  define('wdDialogFormatFont',                                            0x000000AE);
  define('wdDialogFormatParagraph',                                       0x000000AF);
  define('wdDialogFormatSectionLayout',                                   0x000000B0);
  define('wdDialogFormatColumns',                                         0x000000B1);
  define('wdDialogFileDocumentLayout',                                    0x000000B2);
  define('wdDialogFileMacPageSetup',                                      0x000002AD);
  define('wdDialogFilePrintOneCopy',                                      0x000001BD);
  define('wdDialogFileMacPageSetupGX',                                    0x000001BC);
  define('wdDialogFileMacCustomPageSetupGX',                              0x000002E1);
  define('wdDialogFilePageSetup',                                         0x000000B2);
  define('wdDialogFormatTabs',                                            0x000000B3);
  define('wdDialogFormatStyle',                                           0x000000B4);
  define('wdDialogFormatStyleGallery',                                    0x000001F9);
  define('wdDialogFormatDefineStyleFont',                                 0x000000B5);
  define('wdDialogFormatDefineStylePara',                                 0x000000B6);
  define('wdDialogFormatDefineStyleTabs',                                 0x000000B7);
  define('wdDialogFormatDefineStyleFrame',                                0x000000B8);
  define('wdDialogFormatDefineStyleBorders',                              0x000000B9);
  define('wdDialogFormatDefineStyleLang',                                 0x000000BA);
  define('wdDialogFormatPicture',                                         0x000000BB);
  define('wdDialogToolsLanguage',                                         0x000000BC);
  define('wdDialogFormatBordersAndShading',                               0x000000BD);
  define('wdDialogFormatDrawingObject',                                   0x000003C0);
  define('wdDialogFormatFrame',                                           0x000000BE);
  define('wdDialogFormatDropCap',                                         0x000001E8);
  define('wdDialogFormatBulletsAndNumbering',                             0x00000338);
  define('wdDialogToolsHyphenation',                                      0x000000C3);
  define('wdDialogToolsBulletsNumbers',                                   0x000000C4);
  define('wdDialogToolsHighlightChanges',                                 0x000000C5);
  define('wdDialogToolsAcceptRejectChanges',                              0x000001FA);
  define('wdDialogToolsMergeDocuments',                                   0x000001B3);
  define('wdDialogToolsCompareDocuments',                                 0x000000C6);
  define('wdDialogTableSort',                                             0x000000C7);
  define('wdDialogToolsCustomizeMenuBar',                                 0x00000267);
  define('wdDialogToolsCustomize',                                        0x00000098);
  define('wdDialogToolsCustomizeKeyboard',                                0x000001B0);
  define('wdDialogToolsCustomizeMenus',                                   0x000001B1);
  define('wdDialogListCommands',                                          0x000002D3);
  define('wdDialogToolsOptions',                                          0x000003CE);
  define('wdDialogToolsOptionsGeneral',                                   0x000000CB);
  define('wdDialogToolsAdvancedSettings',                                 0x000000CE);
  define('wdDialogToolsOptionsCompatibility',                             0x0000020D);
  define('wdDialogToolsOptionsPrint',                                     0x000000D0);
  define('wdDialogToolsOptionsSave',                                      0x000000D1);
  define('wdDialogToolsOptionsSpellingAndGrammar',                        0x000000D3);
  define('wdDialogToolsSpellingAndGrammar',                               0x0000033C);
  define('wdDialogToolsThesaurus',                                        0x000000C2);
  define('wdDialogToolsOptionsUserInfo',                                  0x000000D5);
  define('wdDialogToolsOptionsAutoFormat',                                0x000003BF);
  define('wdDialogToolsOptionsTrackChanges',                              0x00000182);
  define('wdDialogToolsOptionsEdit',                                      0x000000E0);
  define('wdDialogToolsMacro',                                            0x000000D7);
  define('wdDialogInsertPageNumbers',                                     0x00000126);
  define('wdDialogFormatPageNumber',                                      0x0000012A);
  define('wdDialogNoteOptions',                                           0x00000175);
  define('wdDialogCopyFile',                                              0x0000012C);
  define('wdDialogFormatAddrFonts',                                       0x00000067);
  define('wdDialogFormatRetAddrFonts',                                    0x000000DD);
  define('wdDialogToolsOptionsFileLocations',                             0x000000E1);
  define('wdDialogToolsCreateDirectory',                                  0x00000341);
  define('wdDialogUpdateTOC',                                             0x0000014B);
  define('wdDialogInsertFormField',                                       0x000001E3);
  define('wdDialogFormFieldOptions',                                      0x00000161);
  define('wdDialogInsertCaption',                                         0x00000165);
  define('wdDialogInsertAutoCaption',                                     0x00000167);
  define('wdDialogInsertAddCaption',                                      0x00000192);
  define('wdDialogInsertCaptionNumbering',                                0x00000166);
  define('wdDialogInsertCrossReference',                                  0x0000016F);
  define('wdDialogToolsManageFields',                                     0x00000277);
  define('wdDialogToolsAutoManager',                                      0x00000393);
  define('wdDialogToolsAutoCorrect',                                      0x0000017A);
  define('wdDialogToolsAutoCorrectExceptions',                            0x000002FA);
  define('wdDialogConnect',                                               0x000001A4);
  define('wdDialogToolsOptionsBidi',                                      0x00000405);
  define('wdDialogToolsOptionsView',                                      0x000000CC);
  define('wdDialogInsertSubdocument',                                     0x00000247);
  define('wdDialogFileRoutingSlip',                                       0x00000270);
  define('wdDialogFontSubstitution',                                      0x00000245);
  define('wdDialogEditCreatePublisher',                                   0x000002DC);
  define('wdDialogEditSubscribeTo',                                       0x000002DD);
  define('wdDialogEditPublishOptions',                                    0x000002DF);
  define('wdDialogEditSubscribeOptions',                                  0x000002E0);
  define('wdDialogToolsOptionsTypography',                                0x000002E3);
  define('wdDialogToolsOptionsAutoFormatAsYouType',                       0x0000030A);
  define('wdDialogControlRun',                                            0x000000EB);
  define('wdDialogFileVersions',                                          0x000003B1);
  define('wdDialogToolsAutoSummarize',                                    0x0000036A);
  define('wdDialogFileSaveVersion',                                       0x000003EF);
  define('wdDialogWindowActivate',                                        0x000000DC);
  define('wdDialogToolsMacroRecord',                                      0x000000D6);
  define('wdDialogToolsRevisions',                                        0x000000C5);
  define('wdDialogEmailOptions',                                          0x0000035F);
  define('wdDialogWebOptions',                                            0x00000382);
  define('wdDialogFitText',                                               0x000003D7);
  define('wdDialogPhoneticGuide',                                         0x000003DA);
  define('wdDialogHorizontalInVertical',                                  0x00000488);
  define('wdDialogTwoLinesInOne',                                         0x00000489);
  define('wdDialogFormatEncloseCharacters',                               0x0000048A);
  define('wdDialogFormatTheme',                                           0x00000357);
  define('wdDialogTCSCTranslator',                                        0x00000484);

  // Constants for enum WdWordDialogHID

  // Constants for enum WdFieldKind
  define('wdFieldKindNone',                                               0x00000000);
  define('wdFieldKindHot',                                                0x00000001);
  define('wdFieldKindWarm',                                               0x00000002);
  define('wdFieldKindCold',                                               0x00000003);

  // Constants for enum WdTextFormFieldType
  define('wdRegularText',                                                 0x00000000);
  define('wdNumberText',                                                  0x00000001);
  define('wdDateText',                                                    0x00000002);
  define('wdCurrentDateText',                                             0x00000003);
  define('wdCurrentTimeText',                                             0x00000004);
  define('wdCalculationText',                                             0x00000005);

  // Constants for enum WdChevronConvertRule
  define('wdNeverConvert',                                                0x00000000);
  define('wdAlwaysConvert',                                               0x00000001);
  define('wdAskToNotConvert',                                             0x00000002);
  define('wdAskToConvert',                                                0x00000003);

  // Constants for enum WdMailMergeMainDocType
  define('wdNotAMergeDocument',                                           0xFFFFFFFF);
  define('wdFormLetters',                                                 0x00000000);
  define('wdMailingLabels',                                               0x00000001);
  define('wdEnvelopes',                                                   0x00000002);
  define('wdCatalog',                                                     0x00000003);

  // Constants for enum WdMailMergeState
  define('wdNormalDocument',                                              0x00000000);
  define('wdMainDocumentOnly',                                            0x00000001);
  define('wdMainAndDataSource',                                           0x00000002);
  define('wdMainAndHeader',                                               0x00000003);
  define('wdMainAndSourceAndHeader',                                      0x00000004);
  define('wdDataSource',                                                  0x00000005);

  // Constants for enum WdMailMergeDestination
  define('wdSendToNewDocument',                                           0x00000000);
  define('wdSendToPrinter',                                               0x00000001);
  define('wdSendToEmail',                                                 0x00000002);
  define('wdSendToFax',                                                   0x00000003);

  // Constants for enum WdMailMergeActiveRecord
  define('wdNoActiveRecord',                                              0xFFFFFFFF);
  define('wdNextRecord',                                                  0xFFFFFFFE);
  define('wdPreviousRecord',                                              0xFFFFFFFD);
  define('wdFirstRecord',                                                 0xFFFFFFFC);
  define('wdLastRecord',                                                  0xFFFFFFFB);

  // Constants for enum WdMailMergeDefaultRecord
  define('wdDefaultFirstRecord',                                          0x00000001);
  define('wdDefaultLastRecord',                                           0xFFFFFFF0);

  // Constants for enum WdMailMergeDataSource
  define('wdNoMergeInfo',                                                 0xFFFFFFFF);
  define('wdMergeInfoFromWord',                                           0x00000000);
  define('wdMergeInfoFromAccessDDE',                                      0x00000001);
  define('wdMergeInfoFromExcelDDE',                                       0x00000002);
  define('wdMergeInfoFromMSQueryDDE',                                     0x00000003);
  define('wdMergeInfoFromODBC',                                           0x00000004);

  // Constants for enum WdMailMergeComparison
  define('wdMergeIfEqual',                                                0x00000000);
  define('wdMergeIfNotEqual',                                             0x00000001);
  define('wdMergeIfLessThan',                                             0x00000002);
  define('wdMergeIfGreaterThan',                                          0x00000003);
  define('wdMergeIfLessThanOrEqual',                                      0x00000004);
  define('wdMergeIfGreaterThanOrEqual',                                   0x00000005);
  define('wdMergeIfIsBlank',                                              0x00000006);
  define('wdMergeIfIsNotBlank',                                           0x00000007);

  // Constants for enum WdBookmarkSortBy
  define('wdSortByName',                                                  0x00000000);
  define('wdSortByLocation',                                              0x00000001);

  // Constants for enum WdWindowState
  define('wdWindowStateNormal',                                           0x00000000);
  define('wdWindowStateMaximize',                                         0x00000001);
  define('wdWindowStateMinimize',                                         0x00000002);

  // Constants for enum WdPictureLinkType
  define('wdLinkNone',                                                    0x00000000);
  define('wdLinkDataInDoc',                                               0x00000001);
  define('wdLinkDataOnDisk',                                              0x00000002);

  // Constants for enum WdLinkType
  define('wdLinkTypeOLE',                                                 0x00000000);
  define('wdLinkTypePicture',                                             0x00000001);
  define('wdLinkTypeText',                                                0x00000002);
  define('wdLinkTypeReference',                                           0x00000003);
  define('wdLinkTypeInclude',                                             0x00000004);
  define('wdLinkTypeImport',                                              0x00000005);
  define('wdLinkTypeDDE',                                                 0x00000006);
  define('wdLinkTypeDDEAuto',                                             0x00000007);

  // Constants for enum WdWindowType
  define('wdWindowDocument',                                              0x00000000);
  define('wdWindowTemplate',                                              0x00000001);

  // Constants for enum WdViewType
  define('wdNormalView',                                                  0x00000001);
  define('wdOutlineView',                                                 0x00000002);
  define('wdPrintView',                                                   0x00000003);
  define('wdPrintPreview',                                                0x00000004);
  define('wdMasterView',                                                  0x00000005);
  define('wdWebView',                                                     0x00000006);

  // Constants for enum WdSeekView
  define('wdSeekMainDocument',                                            0x00000000);
  define('wdSeekPrimaryHeader',                                           0x00000001);
  define('wdSeekFirstPageHeader',                                         0x00000002);
  define('wdSeekEvenPagesHeader',                                         0x00000003);
  define('wdSeekPrimaryFooter',                                           0x00000004);
  define('wdSeekFirstPageFooter',                                         0x00000005);
  define('wdSeekEvenPagesFooter',                                         0x00000006);
  define('wdSeekFootnotes',                                               0x00000007);
  define('wdSeekEndnotes',                                                0x00000008);
  define('wdSeekCurrentPageHeader',                                       0x00000009);
  define('wdSeekCurrentPageFooter',                                       0x0000000A);

  // Constants for enum WdSpecialPane
  define('wdPaneNone',                                                    0x00000000);
  define('wdPanePrimaryHeader',                                           0x00000001);
  define('wdPaneFirstPageHeader',                                         0x00000002);
  define('wdPaneEvenPagesHeader',                                         0x00000003);
  define('wdPanePrimaryFooter',                                           0x00000004);
  define('wdPaneFirstPageFooter',                                         0x00000005);
  define('wdPaneEvenPagesFooter',                                         0x00000006);
  define('wdPaneFootnotes',                                               0x00000007);
  define('wdPaneEndnotes',                                                0x00000008);
  define('wdPaneFootnoteContinuationNotice',                              0x00000009);
  define('wdPaneFootnoteContinuationSeparator',                           0x0000000A);
  define('wdPaneFootnoteSeparator',                                       0x0000000B);
  define('wdPaneEndnoteContinuationNotice',                               0x0000000C);
  define('wdPaneEndnoteContinuationSeparator',                            0x0000000D);
  define('wdPaneEndnoteSeparator',                                        0x0000000E);
  define('wdPaneComments',                                                0x0000000F);
  define('wdPaneCurrentPageHeader',                                       0x00000010);
  define('wdPaneCurrentPageFooter',                                       0x00000011);

  // Constants for enum WdPageFit
  define('wdPageFitNone',                                                 0x00000000);
  define('wdPageFitFullPage',                                             0x00000001);
  define('wdPageFitBestFit',                                              0x00000002);
  define('wdPageFitTextFit',                                              0x00000003);

  // Constants for enum WdBrowseTarget
  define('wdBrowsePage',                                                  0x00000001);
  define('wdBrowseSection',                                               0x00000002);
  define('wdBrowseComment',                                               0x00000003);
  define('wdBrowseFootnote',                                              0x00000004);
  define('wdBrowseEndnote',                                               0x00000005);
  define('wdBrowseField',                                                 0x00000006);
  define('wdBrowseTable',                                                 0x00000007);
  define('wdBrowseGraphic',                                               0x00000008);
  define('wdBrowseHeading',                                               0x00000009);
  define('wdBrowseEdit',                                                  0x0000000A);
  define('wdBrowseFind',                                                  0x0000000B);
  define('wdBrowseGoTo',                                                  0x0000000C);

  // Constants for enum WdPaperTray
  define('wdPrinterDefaultBin',                                           0x00000000);
  define('wdPrinterUpperBin',                                             0x00000001);
  define('wdPrinterOnlyBin',                                              0x00000001);
  define('wdPrinterLowerBin',                                             0x00000002);
  define('wdPrinterMiddleBin',                                            0x00000003);
  define('wdPrinterManualFeed',                                           0x00000004);
  define('wdPrinterEnvelopeFeed',                                         0x00000005);
  define('wdPrinterManualEnvelopeFeed',                                   0x00000006);
  define('wdPrinterAutomaticSheetFeed',                                   0x00000007);
  define('wdPrinterTractorFeed',                                          0x00000008);
  define('wdPrinterSmallFormatBin',                                       0x00000009);
  define('wdPrinterLargeFormatBin',                                       0x0000000A);
  define('wdPrinterLargeCapacityBin',                                     0x0000000B);
  define('wdPrinterPaperCassette',                                        0x0000000E);
  define('wdPrinterFormSource',                                           0x0000000F);

  // Constants for enum WdOrientation
  define('wdOrientPortrait',                                              0x00000000);
  define('wdOrientLandscape',                                             0x00000001);

  // Constants for enum WdSelectionType
  define('wdNoSelection',                                                 0x00000000);
  define('wdSelectionIP',                                                 0x00000001);
  define('wdSelectionNormal',                                             0x00000002);
  define('wdSelectionFrame',                                              0x00000003);
  define('wdSelectionColumn',                                             0x00000004);
  define('wdSelectionRow',                                                0x00000005);
  define('wdSelectionBlock',                                              0x00000006);
  define('wdSelectionInlineShape',                                        0x00000007);
  define('wdSelectionShape',                                              0x00000008);

  // Constants for enum WdCaptionLabelID
  define('wdCaptionFigure',                                               0xFFFFFFFF);
  define('wdCaptionTable',                                                0xFFFFFFFE);
  define('wdCaptionEquation',                                             0xFFFFFFFD);

  // Constants for enum WdReferenceType
  define('wdRefTypeNumberedItem',                                         0x00000000);
  define('wdRefTypeHeading',                                              0x00000001);
  define('wdRefTypeBookmark',                                             0x00000002);
  define('wdRefTypeFootnote',                                             0x00000003);
  define('wdRefTypeEndnote',                                              0x00000004);

  // Constants for enum WdReferenceKind
  define('wdContentText',                                                 0xFFFFFFFF);
  define('wdNumberRelativeContext',                                       0xFFFFFFFE);
  define('wdNumberNoContext',                                             0xFFFFFFFD);
  define('wdNumberFullContext',                                           0xFFFFFFFC);
  define('wdEntireCaption',                                               0x00000002);
  define('wdOnlyLabelAndNumber',                                          0x00000003);
  define('wdOnlyCaptionText',                                             0x00000004);
  define('wdFootnoteNumber',                                              0x00000005);
  define('wdEndnoteNumber',                                               0x00000006);
  define('wdPageNumber',                                                  0x00000007);
  define('wdPosition',                                                    0x0000000F);
  define('wdFootnoteNumberFormatted',                                     0x00000010);
  define('wdEndnoteNumberFormatted',                                      0x00000011);

  // Constants for enum WdIndexFormat
  define('wdIndexTemplate',                                               0x00000000);
  define('wdIndexClassic',                                                0x00000001);
  define('wdIndexFancy',                                                  0x00000002);
  define('wdIndexModern',                                                 0x00000003);
  define('wdIndexBulleted',                                               0x00000004);
  define('wdIndexFormal',                                                 0x00000005);
  define('wdIndexSimple',                                                 0x00000006);

  // Constants for enum WdIndexType
  define('wdIndexIndent',                                                 0x00000000);
  define('wdIndexRunin',                                                  0x00000001);

  // Constants for enum WdRevisionsWrap
  define('wdWrapNever',                                                   0x00000000);
  define('wdWrapAlways',                                                  0x00000001);
  define('wdWrapAsk',                                                     0x00000002);

  // Constants for enum WdRevisionType
  define('wdNoRevision',                                                  0x00000000);
  define('wdRevisionInsert',                                              0x00000001);
  define('wdRevisionDelete',                                              0x00000002);
  define('wdRevisionProperty',                                            0x00000003);
  define('wdRevisionParagraphNumber',                                     0x00000004);
  define('wdRevisionDisplayField',                                        0x00000005);
  define('wdRevisionReconcile',                                           0x00000006);
  define('wdRevisionConflict',                                            0x00000007);
  define('wdRevisionStyle',                                               0x00000008);
  define('wdRevisionReplace',                                             0x00000009);

  // Constants for enum WdRoutingSlipDelivery
  define('wdOneAfterAnother',                                             0x00000000);
  define('wdAllAtOnce',                                                   0x00000001);

  // Constants for enum WdRoutingSlipStatus
  define('wdNotYetRouted',                                                0x00000000);
  define('wdRouteInProgress',                                             0x00000001);
  define('wdRouteComplete',                                               0x00000002);

  // Constants for enum WdSectionStart
  define('wdSectionContinuous',                                           0x00000000);
  define('wdSectionNewColumn',                                            0x00000001);
  define('wdSectionNewPage',                                              0x00000002);
  define('wdSectionEvenPage',                                             0x00000003);
  define('wdSectionOddPage',                                              0x00000004);

  // Constants for enum WdSaveOptions
  define('wdDoNotSaveChanges',                                            0x00000000);
  define('wdSaveChanges',                                                 0xFFFFFFFF);
  define('wdPromptToSaveChanges',                                         0xFFFFFFFE);

  // Constants for enum WdDocumentKind
  define('wdDocumentNotSpecified',                                        0x00000000);
  define('wdDocumentLetter',                                              0x00000001);
  define('wdDocumentEmail',                                               0x00000002);

  // Constants for enum WdDocumentType
  define('wdTypeDocument',                                                0x00000000);
  define('wdTypeTemplate',                                                0x00000001);
  define('wdTypeFrameset',                                                0x00000002);

  // Constants for enum WdOriginalFormat
  define('wdWordDocument',                                                0x00000000);
  define('wdOriginalDocumentFormat',                                      0x00000001);
  define('wdPromptUser',                                                  0x00000002);

  // Constants for enum WdRelocate
  define('wdRelocateUp',                                                  0x00000000);
  define('wdRelocateDown',                                                0x00000001);

  // Constants for enum WdInsertedTextMark
  define('wdInsertedTextMarkNone',                                        0x00000000);
  define('wdInsertedTextMarkBold',                                        0x00000001);
  define('wdInsertedTextMarkItalic',                                      0x00000002);
  define('wdInsertedTextMarkUnderline',                                   0x00000003);
  define('wdInsertedTextMarkDoubleUnderline',                             0x00000004);

  // Constants for enum WdRevisedLinesMark
  define('wdRevisedLinesMarkNone',                                        0x00000000);
  define('wdRevisedLinesMarkLeftBorder',                                  0x00000001);
  define('wdRevisedLinesMarkRightBorder',                                 0x00000002);
  define('wdRevisedLinesMarkOutsideBorder',                               0x00000003);

  // Constants for enum WdDeletedTextMark
  define('wdDeletedTextMarkHidden',                                       0x00000000);
  define('wdDeletedTextMarkStrikeThrough',                                0x00000001);
  define('wdDeletedTextMarkCaret',                                        0x00000002);
  define('wdDeletedTextMarkPound',                                        0x00000003);

  // Constants for enum WdRevisedPropertiesMark
  define('wdRevisedPropertiesMarkNone',                                   0x00000000);
  define('wdRevisedPropertiesMarkBold',                                   0x00000001);
  define('wdRevisedPropertiesMarkItalic',                                 0x00000002);
  define('wdRevisedPropertiesMarkUnderline',                              0x00000003);
  define('wdRevisedPropertiesMarkDoubleUnderline',                        0x00000004);

  // Constants for enum WdFieldShading
  define('wdFieldShadingNever',                                           0x00000000);
  define('wdFieldShadingAlways',                                          0x00000001);
  define('wdFieldShadingWhenSelected',                                    0x00000002);

  // Constants for enum WdDefaultFilePath
  define('wdDocumentsPath',                                               0x00000000);
  define('wdPicturesPath',                                                0x00000001);
  define('wdUserTemplatesPath',                                           0x00000002);
  define('wdWorkgroupTemplatesPath',                                      0x00000003);
  define('wdUserOptionsPath',                                             0x00000004);
  define('wdAutoRecoverPath',                                             0x00000005);
  define('wdToolsPath',                                                   0x00000006);
  define('wdTutorialPath',                                                0x00000007);
  define('wdStartupPath',                                                 0x00000008);
  define('wdProgramPath',                                                 0x00000009);
  define('wdGraphicsFiltersPath',                                         0x0000000A);
  define('wdTextConvertersPath',                                          0x0000000B);
  define('wdProofingToolsPath',                                           0x0000000C);
  define('wdTempFilePath',                                                0x0000000D);
  define('wdCurrentFolderPath',                                           0x0000000E);
  define('wdStyleGalleryPath',                                            0x0000000F);
  define('wdBorderArtPath',                                               0x00000013);

  // Constants for enum WdCompatibility
  define('wdNoTabHangIndent',                                             0x00000001);
  define('wdNoSpaceRaiseLower',                                           0x00000002);
  define('wdPrintColBlack',                                               0x00000003);
  define('wdWrapTrailSpaces',                                             0x00000004);
  define('wdNoColumnBalance',                                             0x00000005);
  define('wdConvMailMergeEsc',                                            0x00000006);
  define('wdSuppressSpBfAfterPgBrk',                                      0x00000007);
  define('wdSuppressTopSpacing',                                          0x00000008);
  define('wdOrigWordTableRules',                                          0x00000009);
  define('wdTransparentMetafiles',                                        0x0000000A);
  define('wdShowBreaksInFrames',                                          0x0000000B);
  define('wdSwapBordersFacingPages',                                      0x0000000C);
  define('wdLeaveBackslashAlone',                                         0x0000000D);
  define('wdExpandShiftReturn',                                           0x0000000E);
  define('wdDontULTrailSpace',                                            0x0000000F);
  define('wdDontBalanceSingleByteDoubleByteWidth',                        0x00000010);
  define('wdSuppressTopSpacingMac5',                                      0x00000011);
  define('wdSpacingInWholePoints',                                        0x00000012);
  define('wdPrintBodyTextBeforeHeader',                                   0x00000013);
  define('wdNoLeading',                                                   0x00000014);
  define('wdNoSpaceForUL',                                                0x00000015);
  define('wdMWSmallCaps',                                                 0x00000016);
  define('wdNoExtraLineSpacing',                                          0x00000017);
  define('wdTruncateFontHeight',                                          0x00000018);
  define('wdSubFontBySize',                                               0x00000019);
  define('wdUsePrinterMetrics',                                           0x0000001A);
  define('wdWW6BorderRules',                                              0x0000001B);
  define('wdExactOnTop',                                                  0x0000001C);
  define('wdSuppressBottomSpacing',                                       0x0000001D);
  define('wdWPSpaceWidth',                                                0x0000001E);
  define('wdWPJustification',                                             0x0000001F);
  define('wdLineWrapLikeWord6',                                           0x00000020);
  define('wdShapeLayoutLikeWW8',                                          0x00000021);
  define('wdFootnoteLayoutLikeWW8',                                       0x00000022);
  define('wdDontUseHTMLParagraphAutoSpacing',                             0x00000023);
  define('wdDontAdjustLineHeightInTable',                                 0x00000024);
  define('wdForgetLastTabAlignment',                                      0x00000025);
  define('wdAutospaceLikeWW7',                                            0x00000026);
  define('wdAlignTablesRowByRow',                                         0x00000027);
  define('wdLayoutRawTableWidth',                                         0x00000028);
  define('wdLayoutTableRowsApart',                                        0x00000029);
  define('wdUseWord97LineBreakingRules',                                  0x0000002A);

  // Constants for enum WdPaperSize
  define('wdPaper10x14',                                                  0x00000000);
  define('wdPaper11x17',                                                  0x00000001);
  define('wdPaperLetter',                                                 0x00000002);
  define('wdPaperLetterSmall',                                            0x00000003);
  define('wdPaperLegal',                                                  0x00000004);
  define('wdPaperExecutive',                                              0x00000005);
  define('wdPaperA3',                                                     0x00000006);
  define('wdPaperA4',                                                     0x00000007);
  define('wdPaperA4Small',                                                0x00000008);
  define('wdPaperA5',                                                     0x00000009);
  define('wdPaperB4',                                                     0x0000000A);
  define('wdPaperB5',                                                     0x0000000B);
  define('wdPaperCSheet',                                                 0x0000000C);
  define('wdPaperDSheet',                                                 0x0000000D);
  define('wdPaperESheet',                                                 0x0000000E);
  define('wdPaperFanfoldLegalGerman',                                     0x0000000F);
  define('wdPaperFanfoldStdGerman',                                       0x00000010);
  define('wdPaperFanfoldUS',                                              0x00000011);
  define('wdPaperFolio',                                                  0x00000012);
  define('wdPaperLedger',                                                 0x00000013);
  define('wdPaperNote',                                                   0x00000014);
  define('wdPaperQuarto',                                                 0x00000015);
  define('wdPaperStatement',                                              0x00000016);
  define('wdPaperTabloid',                                                0x00000017);
  define('wdPaperEnvelope9',                                              0x00000018);
  define('wdPaperEnvelope10',                                             0x00000019);
  define('wdPaperEnvelope11',                                             0x0000001A);
  define('wdPaperEnvelope12',                                             0x0000001B);
  define('wdPaperEnvelope14',                                             0x0000001C);
  define('wdPaperEnvelopeB4',                                             0x0000001D);
  define('wdPaperEnvelopeB5',                                             0x0000001E);
  define('wdPaperEnvelopeB6',                                             0x0000001F);
  define('wdPaperEnvelopeC3',                                             0x00000020);
  define('wdPaperEnvelopeC4',                                             0x00000021);
  define('wdPaperEnvelopeC5',                                             0x00000022);
  define('wdPaperEnvelopeC6',                                             0x00000023);
  define('wdPaperEnvelopeC65',                                            0x00000024);
  define('wdPaperEnvelopeDL',                                             0x00000025);
  define('wdPaperEnvelopeItaly',                                          0x00000026);
  define('wdPaperEnvelopeMonarch',                                        0x00000027);
  define('wdPaperEnvelopePersonal',                                       0x00000028);
  define('wdPaperCustom',                                                 0x00000029);

  // Constants for enum WdCustomLabelPageSize
  define('wdCustomLabelLetter',                                           0x00000000);
  define('wdCustomLabelLetterLS',                                         0x00000001);
  define('wdCustomLabelA4',                                               0x00000002);
  define('wdCustomLabelA4LS',                                             0x00000003);
  define('wdCustomLabelA5',                                               0x00000004);
  define('wdCustomLabelA5LS',                                             0x00000005);
  define('wdCustomLabelB5',                                               0x00000006);
  define('wdCustomLabelMini',                                             0x00000007);
  define('wdCustomLabelFanfold',                                          0x00000008);
  define('wdCustomLabelVertHalfSheet',                                    0x00000009);
  define('wdCustomLabelVertHalfSheetLS',                                  0x0000000A);
  define('wdCustomLabelHigaki',                                           0x0000000B);
  define('wdCustomLabelHigakiLS',                                         0x0000000C);
  define('wdCustomLabelB4JIS',                                            0x0000000D);

  // Constants for enum WdProtectionType
  define('wdNoProtection',                                                0xFFFFFFFF);
  define('wdAllowOnlyRevisions',                                          0x00000000);
  define('wdAllowOnlyComments',                                           0x00000001);
  define('wdAllowOnlyFormFields',                                         0x00000002);

  // Constants for enum WdPartOfSpeech
  define('wdAdjective',                                                   0x00000000);
  define('wdNoun',                                                        0x00000001);
  define('wdAdverb',                                                      0x00000002);
  define('wdVerb',                                                        0x00000003);
  define('wdPronoun',                                                     0x00000004);
  define('wdConjunction',                                                 0x00000005);
  define('wdPreposition',                                                 0x00000006);
  define('wdInterjection',                                                0x00000007);
  define('wdIdiom',                                                       0x00000008);
  define('wdOther',                                                       0x00000009);

  // Constants for enum WdSubscriberFormats
  define('wdSubscriberBestFormat',                                        0x00000000);
  define('wdSubscriberRTF',                                               0x00000001);
  define('wdSubscriberText',                                              0x00000002);
  define('wdSubscriberPict',                                              0x00000004);

  // Constants for enum WdEditionType
  define('wdPublisher',                                                   0x00000000);
  define('wdSubscriber',                                                  0x00000001);

  // Constants for enum WdEditionOption
  define('wdCancelPublisher',                                             0x00000000);
  define('wdSendPublisher',                                               0x00000001);
  define('wdSelectPublisher',                                             0x00000002);
  define('wdAutomaticUpdate',                                             0x00000003);
  define('wdManualUpdate',                                                0x00000004);
  define('wdChangeAttributes',                                            0x00000005);
  define('wdUpdateSubscriber',                                            0x00000006);
  define('wdOpenSource',                                                  0x00000007);

  // Constants for enum WdRelativeHorizontalPosition
  define('wdRelativeHorizontalPositionMargin',                            0x00000000);
  define('wdRelativeHorizontalPositionPage',                              0x00000001);
  define('wdRelativeHorizontalPositionColumn',                            0x00000002);
  define('wdRelativeHorizontalPositionCharacter',                         0x00000003);

  // Constants for enum WdRelativeVerticalPosition
  define('wdRelativeVerticalPositionMargin',                              0x00000000);
  define('wdRelativeVerticalPositionPage',                                0x00000001);
  define('wdRelativeVerticalPositionParagraph',                           0x00000002);
  define('wdRelativeVerticalPositionLine',                                0x00000003);

  // Constants for enum WdHelpType
  define('wdHelp',                                                        0x00000000);
  define('wdHelpAbout',                                                   0x00000001);
  define('wdHelpActiveWindow',                                            0x00000002);
  define('wdHelpContents',                                                0x00000003);
  define('wdHelpExamplesAndDemos',                                        0x00000004);
  define('wdHelpIndex',                                                   0x00000005);
  define('wdHelpKeyboard',                                                0x00000006);
  define('wdHelpPSSHelp',                                                 0x00000007);
  define('wdHelpQuickPreview',                                            0x00000008);
  define('wdHelpSearch',                                                  0x00000009);
  define('wdHelpUsingHelp',                                               0x0000000A);
  define('wdHelpIchitaro',                                                0x0000000B);
  define('wdHelpPE2',                                                     0x0000000C);
  define('wdHelpHWP',                                                     0x0000000D);

  // Constants for enum WdHelpTypeHID

  // Constants for enum WdKeyCategory
  define('wdKeyCategoryNil',                                              0xFFFFFFFF);
  define('wdKeyCategoryDisable',                                          0x00000000);
  define('wdKeyCategoryCommand',                                          0x00000001);
  define('wdKeyCategoryMacro',                                            0x00000002);
  define('wdKeyCategoryFont',                                             0x00000003);
  define('wdKeyCategoryAutoText',                                         0x00000004);
  define('wdKeyCategoryStyle',                                            0x00000005);
  define('wdKeyCategorySymbol',                                           0x00000006);
  define('wdKeyCategoryPrefix',                                           0x00000007);

  // Constants for enum WdKey
  define('wdNoKey',                                                       0x000000FF);
  define('wdKeyShift',                                                    0x00000100);
  define('wdKeyControl',                                                  0x00000200);
  define('wdKeyCommand',                                                  0x00000200);
  define('wdKeyAlt',                                                      0x00000400);
  define('wdKeyOption',                                                   0x00000400);
  define('wdKeyA',                                                        0x00000041);
  define('wdKeyB',                                                        0x00000042);
  define('wdKeyC',                                                        0x00000043);
  define('wdKeyD',                                                        0x00000044);
  define('wdKeyE',                                                        0x00000045);
  define('wdKeyF',                                                        0x00000046);
  define('wdKeyG',                                                        0x00000047);
  define('wdKeyH',                                                        0x00000048);
  define('wdKeyI',                                                        0x00000049);
  define('wdKeyJ',                                                        0x0000004A);
  define('wdKeyK',                                                        0x0000004B);
  define('wdKeyL',                                                        0x0000004C);
  define('wdKeyM',                                                        0x0000004D);
  define('wdKeyN',                                                        0x0000004E);
  define('wdKeyO',                                                        0x0000004F);
  define('wdKeyP',                                                        0x00000050);
  define('wdKeyQ',                                                        0x00000051);
  define('wdKeyR',                                                        0x00000052);
  define('wdKeyS',                                                        0x00000053);
  define('wdKeyT',                                                        0x00000054);
  define('wdKeyU',                                                        0x00000055);
  define('wdKeyV',                                                        0x00000056);
  define('wdKeyW',                                                        0x00000057);
  define('wdKeyX',                                                        0x00000058);
  define('wdKeyY',                                                        0x00000059);
  define('wdKeyZ',                                                        0x0000005A);
  define('wdKey0',                                                        0x00000030);
  define('wdKey1',                                                        0x00000031);
  define('wdKey2',                                                        0x00000032);
  define('wdKey3',                                                        0x00000033);
  define('wdKey4',                                                        0x00000034);
  define('wdKey5',                                                        0x00000035);
  define('wdKey6',                                                        0x00000036);
  define('wdKey7',                                                        0x00000037);
  define('wdKey8',                                                        0x00000038);
  define('wdKey9',                                                        0x00000039);
  define('wdKeyBackspace',                                                0x00000008);
  define('wdKeyTab',                                                      0x00000009);
  define('wdKeyNumeric5Special',                                          0x0000000C);
  define('wdKeyReturn',                                                   0x0000000D);
  define('wdKeyPause',                                                    0x00000013);
  define('wdKeyEsc',                                                      0x0000001B);
  define('wdKeySpacebar',                                                 0x00000020);
  define('wdKeyPageUp',                                                   0x00000021);
  define('wdKeyPageDown',                                                 0x00000022);
  define('wdKeyEnd',                                                      0x00000023);
  define('wdKeyHome',                                                     0x00000024);
  define('wdKeyInsert',                                                   0x0000002D);
  define('wdKeyDelete',                                                   0x0000002E);
  define('wdKeyNumeric0',                                                 0x00000060);
  define('wdKeyNumeric1',                                                 0x00000061);
  define('wdKeyNumeric2',                                                 0x00000062);
  define('wdKeyNumeric3',                                                 0x00000063);
  define('wdKeyNumeric4',                                                 0x00000064);
  define('wdKeyNumeric5',                                                 0x00000065);
  define('wdKeyNumeric6',                                                 0x00000066);
  define('wdKeyNumeric7',                                                 0x00000067);
  define('wdKeyNumeric8',                                                 0x00000068);
  define('wdKeyNumeric9',                                                 0x00000069);
  define('wdKeyNumericMultiply',                                          0x0000006A);
  define('wdKeyNumericAdd',                                               0x0000006B);
  define('wdKeyNumericSubtract',                                          0x0000006D);
  define('wdKeyNumericDecimal',                                           0x0000006E);
  define('wdKeyNumericDivide',                                            0x0000006F);
  define('wdKeyF1',                                                       0x00000070);
  define('wdKeyF2',                                                       0x00000071);
  define('wdKeyF3',                                                       0x00000072);
  define('wdKeyF4',                                                       0x00000073);
  define('wdKeyF5',                                                       0x00000074);
  define('wdKeyF6',                                                       0x00000075);
  define('wdKeyF7',                                                       0x00000076);
  define('wdKeyF8',                                                       0x00000077);
  define('wdKeyF9',                                                       0x00000078);
  define('wdKeyF10',                                                      0x00000079);
  define('wdKeyF11',                                                      0x0000007A);
  define('wdKeyF12',                                                      0x0000007B);
  define('wdKeyF13',                                                      0x0000007C);
  define('wdKeyF14',                                                      0x0000007D);
  define('wdKeyF15',                                                      0x0000007E);
  define('wdKeyF16',                                                      0x0000007F);
  define('wdKeyScrollLock',                                               0x00000091);
  define('wdKeySemiColon',                                                0x000000BA);
  define('wdKeyEquals',                                                   0x000000BB);
  define('wdKeyComma',                                                    0x000000BC);
  define('wdKeyHyphen',                                                   0x000000BD);
  define('wdKeyPeriod',                                                   0x000000BE);
  define('wdKeySlash',                                                    0x000000BF);
  define('wdKeyBackSingleQuote',                                          0x000000C0);
  define('wdKeyOpenSquareBrace',                                          0x000000DB);
  define('wdKeyBackSlash',                                                0x000000DC);
  define('wdKeyCloseSquareBrace',                                         0x000000DD);
  define('wdKeySingleQuote',                                              0x000000DE);

  // Constants for enum WdOLEType
  define('wdOLELink',                                                     0x00000000);
  define('wdOLEEmbed',                                                    0x00000001);
  define('wdOLEControl',                                                  0x00000002);

  // Constants for enum WdOLEVerb
  define('wdOLEVerbPrimary',                                              0x00000000);
  define('wdOLEVerbShow',                                                 0xFFFFFFFF);
  define('wdOLEVerbOpen',                                                 0xFFFFFFFE);
  define('wdOLEVerbHide',                                                 0xFFFFFFFD);
  define('wdOLEVerbUIActivate',                                           0xFFFFFFFC);
  define('wdOLEVerbInPlaceActivate',                                      0xFFFFFFFB);
  define('wdOLEVerbDiscardUndoState',                                     0xFFFFFFFA);

  // Constants for enum WdOLEPlacement
  define('wdInLine',                                                      0x00000000);
  define('wdFloatOverText',                                               0x00000001);

  // Constants for enum WdEnvelopeOrientation
  define('wdLeftPortrait',                                                0x00000000);
  define('wdCenterPortrait',                                              0x00000001);
  define('wdRightPortrait',                                               0x00000002);
  define('wdLeftLandscape',                                               0x00000003);
  define('wdCenterLandscape',                                             0x00000004);
  define('wdRightLandscape',                                              0x00000005);
  define('wdLeftClockwise',                                               0x00000006);
  define('wdCenterClockwise',                                             0x00000007);
  define('wdRightClockwise',                                              0x00000008);

  // Constants for enum WdLetterStyle
  define('wdFullBlock',                                                   0x00000000);
  define('wdModifiedBlock',                                               0x00000001);
  define('wdSemiBlock',                                                   0x00000002);

  // Constants for enum WdLetterheadLocation
  define('wdLetterTop',                                                   0x00000000);
  define('wdLetterBottom',                                                0x00000001);
  define('wdLetterLeft',                                                  0x00000002);
  define('wdLetterRight',                                                 0x00000003);

  // Constants for enum WdSalutationType
  define('wdSalutationInformal',                                          0x00000000);
  define('wdSalutationFormal',                                            0x00000001);
  define('wdSalutationBusiness',                                          0x00000002);
  define('wdSalutationOther',                                             0x00000003);

  // Constants for enum WdSalutationGender
  define('wdGenderFemale',                                                0x00000000);
  define('wdGenderMale',                                                  0x00000001);
  define('wdGenderNeutral',                                               0x00000002);
  define('wdGenderUnknown',                                               0x00000003);

  // Constants for enum WdMovementType
  define('wdMove',                                                        0x00000000);
  define('wdExtend',                                                      0x00000001);

  // Constants for enum WdConstants
  define('wdUndefined',                                                   0x0098967F);
  define('wdToggle',                                                      0x0098967E);
  define('wdForward',                                                     0x3FFFFFFF);
  define('wdBackward',                                                    0xC0000001);
  define('wdAutoPosition',                                                0x00000000);
  define('wdFirst',                                                       0x00000001);
  define('wdCreatorCode',                                                 0x4D535744);

  // Constants for enum WdPasteDataType
  define('wdPasteOLEObject',                                              0x00000000);
  define('wdPasteRTF',                                                    0x00000001);
  define('wdPasteText',                                                   0x00000002);
  define('wdPasteMetafilePicture',                                        0x00000003);
  define('wdPasteBitmap',                                                 0x00000004);
  define('wdPasteDeviceIndependentBitmap',                                0x00000005);
  define('wdPasteHyperlink',                                              0x00000007);
  define('wdPasteShape',                                                  0x00000008);
  define('wdPasteEnhancedMetafile',                                       0x00000009);
  define('wdPasteHTML',                                                   0x0000000A);

  // Constants for enum WdPrintOutItem
  define('wdPrintDocumentContent',                                        0x00000000);
  define('wdPrintProperties',                                             0x00000001);
  define('wdPrintComments',                                               0x00000002);
  define('wdPrintStyles',                                                 0x00000003);
  define('wdPrintAutoTextEntries',                                        0x00000004);
  define('wdPrintKeyAssignments',                                         0x00000005);
  define('wdPrintEnvelope',                                               0x00000006);

  // Constants for enum WdPrintOutPages
  define('wdPrintAllPages',                                               0x00000000);
  define('wdPrintOddPagesOnly',                                           0x00000001);
  define('wdPrintEvenPagesOnly',                                          0x00000002);

  // Constants for enum WdPrintOutRange
  define('wdPrintAllDocument',                                            0x00000000);
  define('wdPrintSelection',                                              0x00000001);
  define('wdPrintCurrentPage',                                            0x00000002);
  define('wdPrintFromTo',                                                 0x00000003);
  define('wdPrintRangeOfPages',                                           0x00000004);

  // Constants for enum WdDictionaryType
  define('wdSpelling',                                                    0x00000000);
  define('wdGrammar',                                                     0x00000001);
  define('wdThesaurus',                                                   0x00000002);
  define('wdHyphenation',                                                 0x00000003);
  define('wdSpellingComplete',                                            0x00000004);
  define('wdSpellingCustom',                                              0x00000005);
  define('wdSpellingLegal',                                               0x00000006);
  define('wdSpellingMedical',                                             0x00000007);
  define('wdHangulHanjaConversion',                                       0x00000008);
  define('wdHangulHanjaConversionCustom',                                 0x00000009);

  // Constants for enum WdDictionaryTypeHID

  // Constants for enum WdSpellingWordType
  define('wdSpellword',                                                   0x00000000);
  define('wdWildcard',                                                    0x00000001);
  define('wdAnagram',                                                     0x00000002);

  // Constants for enum WdSpellingErrorType
  define('wdSpellingCorrect',                                             0x00000000);
  define('wdSpellingNotInDictionary',                                     0x00000001);
  define('wdSpellingCapitalization',                                      0x00000002);

  // Constants for enum WdProofreadingErrorType
  define('wdSpellingError',                                               0x00000000);
  define('wdGrammaticalError',                                            0x00000001);

  // Constants for enum WdInlineShapeType
  define('wdInlineShapeEmbeddedOLEObject',                                0x00000001);
  define('wdInlineShapeLinkedOLEObject',                                  0x00000002);
  define('wdInlineShapePicture',                                          0x00000003);
  define('wdInlineShapeLinkedPicture',                                    0x00000004);
  define('wdInlineShapeOLEControlObject',                                 0x00000005);
  define('wdInlineShapeHorizontalLine',                                   0x00000006);
  define('wdInlineShapePictureHorizontalLine',                            0x00000007);
  define('wdInlineShapeLinkedPictureHorizontalLine',                      0x00000008);
  define('wdInlineShapePictureBullet',                                    0x00000009);
  define('wdInlineShapeScriptAnchor',                                     0x0000000A);
  define('wdInlineShapeOWSAnchor',                                        0x0000000B);

  // Constants for enum WdArrangeStyle
  define('wdTiled',                                                       0x00000000);
  define('wdIcons',                                                       0x00000001);

  // Constants for enum WdSelectionFlags
  define('wdSelStartActive',                                              0x00000001);
  define('wdSelAtEOL',                                                    0x00000002);
  define('wdSelOvertype',                                                 0x00000004);
  define('wdSelActive',                                                   0x00000008);
  define('wdSelReplace',                                                  0x00000010);

  // Constants for enum WdAutoVersions
  define('wdAutoVersionOff',                                              0x00000000);
  define('wdAutoVersionOnClose',                                          0x00000001);

  // Constants for enum WdOrganizerObject
  define('wdOrganizerObjectStyles',                                       0x00000000);
  define('wdOrganizerObjectAutoText',                                     0x00000001);
  define('wdOrganizerObjectCommandBars',                                  0x00000002);
  define('wdOrganizerObjectProjectItems',                                 0x00000003);

  // Constants for enum WdFindMatch
  define('wdMatchParagraphMark',                                          0x0001000F);
  define('wdMatchTabCharacter',                                           0x00000009);
  define('wdMatchCommentMark',                                            0x00000005);
  define('wdMatchAnyCharacter',                                           0x0001003F);
  define('wdMatchAnyDigit',                                               0x0001001F);
  define('wdMatchAnyLetter',                                              0x0001002F);
  define('wdMatchCaretCharacter',                                         0x0000000B);
  define('wdMatchColumnBreak',                                            0x0000000E);
  define('wdMatchEmDash',                                                 0x00002014);
  define('wdMatchEnDash',                                                 0x00002013);
  define('wdMatchEndnoteMark',                                            0x00010013);
  define('wdMatchField',                                                  0x00000013);
  define('wdMatchFootnoteMark',                                           0x00010012);
  define('wdMatchGraphic',                                                0x00000001);
  define('wdMatchManualLineBreak',                                        0x0001000F);
  define('wdMatchManualPageBreak',                                        0x0001001C);
  define('wdMatchNonbreakingHyphen',                                      0x0000001E);
  define('wdMatchNonbreakingSpace',                                       0x000000A0);
  define('wdMatchOptionalHyphen',                                         0x0000001F);
  define('wdMatchSectionBreak',                                           0x0001002C);
  define('wdMatchWhiteSpace',                                             0x00010077);

  // Constants for enum WdFindWrap
  define('wdFindStop',                                                    0x00000000);
  define('wdFindContinue',                                                0x00000001);
  define('wdFindAsk',                                                     0x00000002);

  // Constants for enum WdInformation
  define('wdActiveEndAdjustedPageNumber',                                 0x00000001);
  define('wdActiveEndSectionNumber',                                      0x00000002);
  define('wdActiveEndPageNumber',                                         0x00000003);
  define('wdNumberOfPagesInDocument',                                     0x00000004);
  define('wdHorizontalPositionRelativeToPage',                            0x00000005);
  define('wdVerticalPositionRelativeToPage',                              0x00000006);
  define('wdHorizontalPositionRelativeToTextBoundary',                    0x00000007);
  define('wdVerticalPositionRelativeToTextBoundary',                      0x00000008);
  define('wdFirstCharacterColumnNumber',                                  0x00000009);
  define('wdFirstCharacterLineNumber',                                    0x0000000A);
  define('wdFrameIsSelected',                                             0x0000000B);
  define('wdWithInTable',                                                 0x0000000C);
  define('wdStartOfRangeRowNumber',                                       0x0000000D);
  define('wdEndOfRangeRowNumber',                                         0x0000000E);
  define('wdMaximumNumberOfRows',                                         0x0000000F);
  define('wdStartOfRangeColumnNumber',                                    0x00000010);
  define('wdEndOfRangeColumnNumber',                                      0x00000011);
  define('wdMaximumNumberOfColumns',                                      0x00000012);
  define('wdZoomPercentage',                                              0x00000013);
  define('wdSelectionMode',                                               0x00000014);
  define('wdCapsLock',                                                    0x00000015);
  define('wdNumLock',                                                     0x00000016);
  define('wdOverType',                                                    0x00000017);
  define('wdRevisionMarking',                                             0x00000018);
  define('wdInFootnoteEndnotePane',                                       0x00000019);
  define('wdInCommentPane',                                               0x0000001A);
  define('wdInHeaderFooter',                                              0x0000001C);
  define('wdAtEndOfRowMarker',                                            0x0000001F);
  define('wdReferenceOfType',                                             0x00000020);
  define('wdHeaderFooterType',                                            0x00000021);
  define('wdInMasterDocument',                                            0x00000022);
  define('wdInFootnote',                                                  0x00000023);
  define('wdInEndnote',                                                   0x00000024);
  define('wdInWordMail',                                                  0x00000025);
  define('wdInClipboard',                                                 0x00000026);

  // Constants for enum WdWrapType
  define('wdWrapSquare',                                                  0x00000000);
  define('wdWrapTight',                                                   0x00000001);
  define('wdWrapThrough',                                                 0x00000002);
  define('wdWrapNone',                                                    0x00000003);
  define('wdWrapTopBottom',                                               0x00000004);

  // Constants for enum WdWrapSideType
  define('wdWrapBoth',                                                    0x00000000);
  define('wdWrapLeft',                                                    0x00000001);
  define('wdWrapRight',                                                   0x00000002);
  define('wdWrapLargest',                                                 0x00000003);

  // Constants for enum WdOutlineLevel
  define('wdOutlineLevel1',                                               0x00000001);
  define('wdOutlineLevel2',                                               0x00000002);
  define('wdOutlineLevel3',                                               0x00000003);
  define('wdOutlineLevel4',                                               0x00000004);
  define('wdOutlineLevel5',                                               0x00000005);
  define('wdOutlineLevel6',                                               0x00000006);
  define('wdOutlineLevel7',                                               0x00000007);
  define('wdOutlineLevel8',                                               0x00000008);
  define('wdOutlineLevel9',                                               0x00000009);
  define('wdOutlineLevelBodyText',                                        0x0000000A);

  // Constants for enum WdTextOrientation
  define('wdTextOrientationHorizontal',                                   0x00000000);
  define('wdTextOrientationUpward',                                       0x00000002);
  define('wdTextOrientationDownward',                                     0x00000003);
  define('wdTextOrientationVerticalFarEast',                              0x00000001);
  define('wdTextOrientationHorizontalRotatedFarEast',                     0x00000004);

  // Constants for enum WdTextOrientationHID

  // Constants for enum WdPageBorderArt
  define('wdArtApples',                                                   0x00000001);
  define('wdArtMapleMuffins',                                             0x00000002);
  define('wdArtCakeSlice',                                                0x00000003);
  define('wdArtCandyCorn',                                                0x00000004);
  define('wdArtIceCreamCones',                                            0x00000005);
  define('wdArtChampagneBottle',                                          0x00000006);
  define('wdArtPartyGlass',                                               0x00000007);
  define('wdArtChristmasTree',                                            0x00000008);
  define('wdArtTrees',                                                    0x00000009);
  define('wdArtPalmsColor',                                               0x0000000A);
  define('wdArtBalloons3Colors',                                          0x0000000B);
  define('wdArtBalloonsHotAir',                                           0x0000000C);
  define('wdArtPartyFavor',                                               0x0000000D);
  define('wdArtConfettiStreamers',                                        0x0000000E);
  define('wdArtHearts',                                                   0x0000000F);
  define('wdArtHeartBalloon',                                             0x00000010);
  define('wdArtStars3D',                                                  0x00000011);
  define('wdArtStarsShadowed',                                            0x00000012);
  define('wdArtStars',                                                    0x00000013);
  define('wdArtSun',                                                      0x00000014);
  define('wdArtEarth2',                                                   0x00000015);
  define('wdArtEarth1',                                                   0x00000016);
  define('wdArtPeopleHats',                                               0x00000017);
  define('wdArtSombrero',                                                 0x00000018);
  define('wdArtPencils',                                                  0x00000019);
  define('wdArtPackages',                                                 0x0000001A);
  define('wdArtClocks',                                                   0x0000001B);
  define('wdArtFirecrackers',                                             0x0000001C);
  define('wdArtRings',                                                    0x0000001D);
  define('wdArtMapPins',                                                  0x0000001E);
  define('wdArtConfetti',                                                 0x0000001F);
  define('wdArtCreaturesButterfly',                                       0x00000020);
  define('wdArtCreaturesLadyBug',                                         0x00000021);
  define('wdArtCreaturesFish',                                            0x00000022);
  define('wdArtBirdsFlight',                                              0x00000023);
  define('wdArtScaredCat',                                                0x00000024);
  define('wdArtBats',                                                     0x00000025);
  define('wdArtFlowersRoses',                                             0x00000026);
  define('wdArtFlowersRedRose',                                           0x00000027);
  define('wdArtPoinsettias',                                              0x00000028);
  define('wdArtHolly',                                                    0x00000029);
  define('wdArtFlowersTiny',                                              0x0000002A);
  define('wdArtFlowersPansy',                                             0x0000002B);
  define('wdArtFlowersModern2',                                           0x0000002C);
  define('wdArtFlowersModern1',                                           0x0000002D);
  define('wdArtWhiteFlowers',                                             0x0000002E);
  define('wdArtVine',                                                     0x0000002F);
  define('wdArtFlowersDaisies',                                           0x00000030);
  define('wdArtFlowersBlockPrint',                                        0x00000031);
  define('wdArtDecoArchColor',                                            0x00000032);
  define('wdArtFans',                                                     0x00000033);
  define('wdArtFilm',                                                     0x00000034);
  define('wdArtLightning1',                                               0x00000035);
  define('wdArtCompass',                                                  0x00000036);
  define('wdArtDoubleD',                                                  0x00000037);
  define('wdArtClassicalWave',                                            0x00000038);
  define('wdArtShadowedSquares',                                          0x00000039);
  define('wdArtTwistedLines1',                                            0x0000003A);
  define('wdArtWaveline',                                                 0x0000003B);
  define('wdArtQuadrants',                                                0x0000003C);
  define('wdArtCheckedBarColor',                                          0x0000003D);
  define('wdArtSwirligig',                                                0x0000003E);
  define('wdArtPushPinNote1',                                             0x0000003F);
  define('wdArtPushPinNote2',                                             0x00000040);
  define('wdArtPumpkin1',                                                 0x00000041);
  define('wdArtEggsBlack',                                                0x00000042);
  define('wdArtCup',                                                      0x00000043);
  define('wdArtHeartGray',                                                0x00000044);
  define('wdArtGingerbreadMan',                                           0x00000045);
  define('wdArtBabyPacifier',                                             0x00000046);
  define('wdArtBabyRattle',                                               0x00000047);
  define('wdArtCabins',                                                   0x00000048);
  define('wdArtHouseFunky',                                               0x00000049);
  define('wdArtStarsBlack',                                               0x0000004A);
  define('wdArtSnowflakes',                                               0x0000004B);
  define('wdArtSnowflakeFancy',                                           0x0000004C);
  define('wdArtSkyrocket',                                                0x0000004D);
  define('wdArtSeattle',                                                  0x0000004E);
  define('wdArtMusicNotes',                                               0x0000004F);
  define('wdArtPalmsBlack',                                               0x00000050);
  define('wdArtMapleLeaf',                                                0x00000051);
  define('wdArtPaperClips',                                               0x00000052);
  define('wdArtShorebirdTracks',                                          0x00000053);
  define('wdArtPeople',                                                   0x00000054);
  define('wdArtPeopleWaving',                                             0x00000055);
  define('wdArtEclipsingSquares2',                                        0x00000056);
  define('wdArtHypnotic',                                                 0x00000057);
  define('wdArtDiamondsGray',                                             0x00000058);
  define('wdArtDecoArch',                                                 0x00000059);
  define('wdArtDecoBlocks',                                               0x0000005A);
  define('wdArtCirclesLines',                                             0x0000005B);
  define('wdArtPapyrus',                                                  0x0000005C);
  define('wdArtWoodwork',                                                 0x0000005D);
  define('wdArtWeavingBraid',                                             0x0000005E);
  define('wdArtWeavingRibbon',                                            0x0000005F);
  define('wdArtWeavingAngles',                                            0x00000060);
  define('wdArtArchedScallops',                                           0x00000061);
  define('wdArtSafari',                                                   0x00000062);
  define('wdArtCelticKnotwork',                                           0x00000063);
  define('wdArtCrazyMaze',                                                0x00000064);
  define('wdArtEclipsingSquares1',                                        0x00000065);
  define('wdArtBirds',                                                    0x00000066);
  define('wdArtFlowersTeacup',                                            0x00000067);
  define('wdArtNorthwest',                                                0x00000068);
  define('wdArtSouthwest',                                                0x00000069);
  define('wdArtTribal6',                                                  0x0000006A);
  define('wdArtTribal4',                                                  0x0000006B);
  define('wdArtTribal3',                                                  0x0000006C);
  define('wdArtTribal2',                                                  0x0000006D);
  define('wdArtTribal5',                                                  0x0000006E);
  define('wdArtXIllusions',                                               0x0000006F);
  define('wdArtZanyTriangles',                                            0x00000070);
  define('wdArtPyramids',                                                 0x00000071);
  define('wdArtPyramidsAbove',                                            0x00000072);
  define('wdArtConfettiGrays',                                            0x00000073);
  define('wdArtConfettiOutline',                                          0x00000074);
  define('wdArtConfettiWhite',                                            0x00000075);
  define('wdArtMosaic',                                                   0x00000076);
  define('wdArtLightning2',                                               0x00000077);
  define('wdArtHeebieJeebies',                                            0x00000078);
  define('wdArtLightBulb',                                                0x00000079);
  define('wdArtGradient',                                                 0x0000007A);
  define('wdArtTriangleParty',                                            0x0000007B);
  define('wdArtTwistedLines2',                                            0x0000007C);
  define('wdArtMoons',                                                    0x0000007D);
  define('wdArtOvals',                                                    0x0000007E);
  define('wdArtDoubleDiamonds',                                           0x0000007F);
  define('wdArtChainLink',                                                0x00000080);
  define('wdArtTriangles',                                                0x00000081);
  define('wdArtTribal1',                                                  0x00000082);
  define('wdArtMarqueeToothed',                                           0x00000083);
  define('wdArtSharksTeeth',                                              0x00000084);
  define('wdArtSawtooth',                                                 0x00000085);
  define('wdArtSawtoothGray',                                             0x00000086);
  define('wdArtPostageStamp',                                             0x00000087);
  define('wdArtWeavingStrips',                                            0x00000088);
  define('wdArtZigZag',                                                   0x00000089);
  define('wdArtCrossStitch',                                              0x0000008A);
  define('wdArtGems',                                                     0x0000008B);
  define('wdArtCirclesRectangles',                                        0x0000008C);
  define('wdArtCornerTriangles',                                          0x0000008D);
  define('wdArtCreaturesInsects',                                         0x0000008E);
  define('wdArtZigZagStitch',                                             0x0000008F);
  define('wdArtCheckered',                                                0x00000090);
  define('wdArtCheckedBarBlack',                                          0x00000091);
  define('wdArtMarquee',                                                  0x00000092);
  define('wdArtBasicWhiteDots',                                           0x00000093);
  define('wdArtBasicWideMidline',                                         0x00000094);
  define('wdArtBasicWideOutline',                                         0x00000095);
  define('wdArtBasicWideInline',                                          0x00000096);
  define('wdArtBasicThinLines',                                           0x00000097);
  define('wdArtBasicWhiteDashes',                                         0x00000098);
  define('wdArtBasicWhiteSquares',                                        0x00000099);
  define('wdArtBasicBlackSquares',                                        0x0000009A);
  define('wdArtBasicBlackDashes',                                         0x0000009B);
  define('wdArtBasicBlackDots',                                           0x0000009C);
  define('wdArtStarsTop',                                                 0x0000009D);
  define('wdArtCertificateBanner',                                        0x0000009E);
  define('wdArtHandmade1',                                                0x0000009F);
  define('wdArtHandmade2',                                                0x000000A0);
  define('wdArtTornPaper',                                                0x000000A1);
  define('wdArtTornPaperBlack',                                           0x000000A2);
  define('wdArtCouponCutoutDashes',                                       0x000000A3);
  define('wdArtCouponCutoutDots',                                         0x000000A4);

  // Constants for enum WdBorderDistanceFrom
  define('wdBorderDistanceFromText',                                      0x00000000);
  define('wdBorderDistanceFromPageEdge',                                  0x00000001);

  // Constants for enum WdReplace
  define('wdReplaceNone',                                                 0x00000000);
  define('wdReplaceOne',                                                  0x00000001);
  define('wdReplaceAll',                                                  0x00000002);

  // Constants for enum WdFontBias
  define('wdFontBiasDontCare',                                            0x000000FF);
  define('wdFontBiasDefault',                                             0x00000000);
  define('wdFontBiasFareast',                                             0x00000001);

  // Constants for enum WdBrowserLevel
  define('wdBrowserLevelV4',                                              0x00000000);
  define('wdBrowserLevelMicrosoftInternetExplorer5',                      0x00000001);

  // Constants for enum WdEnclosureType
  define('wdEnclosureCircle',                                             0x00000000);
  define('wdEnclosureSquare',                                             0x00000001);
  define('wdEnclosureTriangle',                                           0x00000002);
  define('wdEnclosureDiamond',                                            0x00000003);

  // Constants for enum WdEncloseStyle
  define('wdEncloseStyleNone',                                            0x00000000);
  define('wdEncloseStyleSmall',                                           0x00000001);
  define('wdEncloseStyleLarge',                                           0x00000002);

  // Constants for enum WdHighAnsiText
  define('wdHighAnsiIsFarEast',                                           0x00000000);
  define('wdHighAnsiIsHighAnsi',                                          0x00000001);
  define('wdAutoDetectHighAnsiFarEast',                                   0x00000002);

  // Constants for enum WdLayoutMode
  define('wdLayoutModeDefault',                                           0x00000000);
  define('wdLayoutModeGrid',                                              0x00000001);
  define('wdLayoutModeLineGrid',                                          0x00000002);
  define('wdLayoutModeGenko',                                             0x00000003);

  // Constants for enum WdDocumentMedium
  define('wdEmailMessage',                                                0x00000000);
  define('wdDocument',                                                    0x00000001);
  define('wdWebPage',                                                     0x00000002);

  // Constants for enum WdMailerPriority
  define('wdPriorityNormal',                                              0x00000001);
  define('wdPriorityLow',                                                 0x00000002);
  define('wdPriorityHigh',                                                0x00000003);

  // Constants for enum WdDocumentViewDirection
  define('wdDocumentViewRtl',                                             0x00000000);
  define('wdDocumentViewLtr',                                             0x00000001);

  // Constants for enum WdArabicNumeral
  define('wdNumeralArabic',                                               0x00000000);
  define('wdNumeralHindi',                                                0x00000001);
  define('wdNumeralContext',                                              0x00000002);
  define('wdNumeralSystem',                                               0x00000003);

  // Constants for enum WdMonthNames
  define('wdMonthNamesArabic',                                            0x00000000);
  define('wdMonthNamesEnglish',                                           0x00000001);
  define('wdMonthNamesFrench',                                            0x00000002);

  // Constants for enum WdCursorMovement
  define('wdCursorMovementLogical',                                       0x00000000);
  define('wdCursorMovementVisual',                                        0x00000001);

  // Constants for enum WdVisualSelection
  define('wdVisualSelectionBlock',                                        0x00000000);
  define('wdVisualSelectionContinuous',                                   0x00000001);

  // Constants for enum WdTableDirection
  define('wdTableDirectionRtl',                                           0x00000000);
  define('wdTableDirectionLtr',                                           0x00000001);

  // Constants for enum WdFlowDirection
  define('wdFlowLtr',                                                     0x00000000);
  define('wdFlowRtl',                                                     0x00000001);

  // Constants for enum WdDiacriticColor
  define('wdDiacriticColorBidi',                                          0x00000000);
  define('wdDiacriticColorLatin',                                         0x00000001);

  // Constants for enum WdGutterStyle
  define('wdGutterPosLeft',                                               0x00000000);
  define('wdGutterPosTop',                                                0x00000001);
  define('wdGutterPosRight',                                              0x00000002);

  // Constants for enum WdGutterStyleOld
  define('wdGutterStyleLatin',                                            0xFFFFFFF6);
  define('wdGutterStyleBidi',                                             0x00000002);

  // Constants for enum WdSectionDirection
  define('wdSectionDirectionRtl',                                         0x00000000);
  define('wdSectionDirectionLtr',                                         0x00000001);

  // Constants for enum WdDateLanguage
  define('wdDateLanguageBidi',                                            0x0000000A);
  define('wdDateLanguageLatin',                                           0x00000409);

  // Constants for enum WdCalendarTypeBi
  define('wdCalendarTypeBidi',                                            0x00000063);
  define('wdCalendarTypeGregorian',                                       0x00000064);

  // Constants for enum WdCalendarType
  define('wdCalendarWestern',                                             0x00000000);
  define('wdCalendarArabic',                                              0x00000001);
  define('wdCalendarHebrew',                                              0x00000002);
  define('wdCalendarChina',                                               0x00000003);
  define('wdCalendarJapan',                                               0x00000004);
  define('wdCalendarThai',                                                0x00000005);
  define('wdCalendarKorean',                                              0x00000006);

  // Constants for enum WdReadingOrder
  define('wdReadingOrderRtl',                                             0x00000000);
  define('wdReadingOrderLtr',                                             0x00000001);

  // Constants for enum WdHebSpellStart
  define('wdFullScript',                                                  0x00000000);
  define('wdPartialScript',                                               0x00000001);
  define('wdMixedScript',                                                 0x00000002);
  define('wdMixedAuthorizedScript',                                       0x00000003);

  // Constants for enum WdAraSpeller
  define('wdNone',                                                        0x00000000);
  define('wdInitialAlef',                                                 0x00000001);
  define('wdFinalYaa',                                                    0x00000002);
  define('wdBoth',                                                        0x00000003);

  // Constants for enum WdColor
  define('wdColorAutomatic',                                              0xFF000000);
  define('wdColorBlack',                                                  0x00000000);
  define('wdColorBlue',                                                   0x00FF0000);
  define('wdColorTurquoise',                                              0x00FFFF00);
  define('wdColorBrightGreen',                                            0x0000FF00);
  define('wdColorPink',                                                   0x00FF00FF);
  define('wdColorRed',                                                    0x000000FF);
  define('wdColorYellow',                                                 0x0000FFFF);
  define('wdColorWhite',                                                  0x00FFFFFF);
  define('wdColorDarkBlue',                                               0x00800000);
  define('wdColorTeal',                                                   0x00808000);
  define('wdColorGreen',                                                  0x00008000);
  define('wdColorViolet',                                                 0x00800080);
  define('wdColorDarkRed',                                                0x00000080);
  define('wdColorDarkYellow',                                             0x00008080);
  define('wdColorBrown',                                                  0x00003399);
  define('wdColorOliveGreen',                                             0x00003333);
  define('wdColorDarkGreen',                                              0x00003300);
  define('wdColorDarkTeal',                                               0x00663300);
  define('wdColorIndigo',                                                 0x00993333);
  define('wdColorOrange',                                                 0x000066FF);
  define('wdColorBlueGray',                                               0x00996666);
  define('wdColorLightOrange',                                            0x000099FF);
  define('wdColorLime',                                                   0x0000CC99);
  define('wdColorSeaGreen',                                               0x00669933);
  define('wdColorAqua',                                                   0x00CCCC33);
  define('wdColorLightBlue',                                              0x00FF6633);
  define('wdColorGold',                                                   0x0000CCFF);
  define('wdColorSkyBlue',                                                0x00FFCC00);
  define('wdColorPlum',                                                   0x00663399);
  define('wdColorRose',                                                   0x00CC99FF);
  define('wdColorTan',                                                    0x0099CCFF);
  define('wdColorLightYellow',                                            0x0099FFFF);
  define('wdColorLightGreen',                                             0x00CCFFCC);
  define('wdColorLightTurquoise',                                         0x00FFFFCC);
  define('wdColorPaleBlue',                                               0x00FFCC99);
  define('wdColorLavender',                                               0x00FF99CC);
  define('wdColorGray05',                                                 0x00F3F3F3);
  define('wdColorGray10',                                                 0x00E6E6E6);
  define('wdColorGray125',                                                0x00E0E0E0);
  define('wdColorGray15',                                                 0x00D9D9D9);
  define('wdColorGray20',                                                 0x00CCCCCC);
  define('wdColorGray25',                                                 0x00C0C0C0);
  define('wdColorGray30',                                                 0x00B3B3B3);
  define('wdColorGray35',                                                 0x00A6A6A6);
  define('wdColorGray375',                                                0x00A0A0A0);
  define('wdColorGray40',                                                 0x00999999);
  define('wdColorGray45',                                                 0x008C8C8C);
  define('wdColorGray50',                                                 0x00808080);
  define('wdColorGray55',                                                 0x00737373);
  define('wdColorGray60',                                                 0x00666666);
  define('wdColorGray625',                                                0x00606060);
  define('wdColorGray65',                                                 0x00595959);
  define('wdColorGray70',                                                 0x004C4C4C);
  define('wdColorGray75',                                                 0x00404040);
  define('wdColorGray80',                                                 0x00333333);
  define('wdColorGray85',                                                 0x00262626);
  define('wdColorGray875',                                                0x00202020);
  define('wdColorGray90',                                                 0x00191919);
  define('wdColorGray95',                                                 0x000C0C0C);

  // Constants for enum WdShapePosition
  define('wdShapeTop',                                                    0xFFF0BDC1);
  define('wdShapeLeft',                                                   0xFFF0BDC2);
  define('wdShapeBottom',                                                 0xFFF0BDC3);
  define('wdShapeRight',                                                  0xFFF0BDC4);
  define('wdShapeCenter',                                                 0xFFF0BDC5);
  define('wdShapeInside',                                                 0xFFF0BDC6);
  define('wdShapeOutside',                                                0xFFF0BDC7);

  // Constants for enum WdTablePosition
  define('wdTableTop',                                                    0xFFF0BDC1);
  define('wdTableLeft',                                                   0xFFF0BDC2);
  define('wdTableBottom',                                                 0xFFF0BDC3);
  define('wdTableRight',                                                  0xFFF0BDC4);
  define('wdTableCenter',                                                 0xFFF0BDC5);
  define('wdTableInside',                                                 0xFFF0BDC6);
  define('wdTableOutside',                                                0xFFF0BDC7);

  // Constants for enum WdDefaultListBehavior
  define('wdWord8ListBehavior',                                           0x00000000);
  define('wdWord9ListBehavior',                                           0x00000001);

  // Constants for enum WdDefaultTableBehavior
  define('wdWord8TableBehavior',                                          0x00000000);
  define('wdWord9TableBehavior',                                          0x00000001);

  // Constants for enum WdAutoFitBehavior
  define('wdAutoFitFixed',                                                0x00000000);
  define('wdAutoFitContent',                                              0x00000001);
  define('wdAutoFitWindow',                                               0x00000002);

  // Constants for enum WdPreferredWidthType
  define('wdPreferredWidthAuto',                                          0x00000001);
  define('wdPreferredWidthPercent',                                       0x00000002);
  define('wdPreferredWidthPoints',                                        0x00000003);

  // Constants for enum WdFarEastLineBreakLanguageID
  define('wdLineBreakJapanese',                                           0x00000411);
  define('wdLineBreakKorean',                                             0x00000412);
  define('wdLineBreakSimplifiedChinese',                                  0x00000804);
  define('wdLineBreakTraditionalChinese',                                 0x00000404);

  // Constants for enum WdViewTypeOld
  define('wdPageView',                                                    0x00000003);
  define('wdOnlineView',                                                  0x00000006);

  // Constants for enum WdFramesetType
  define('wdFramesetTypeFrameset',                                        0x00000000);
  define('wdFramesetTypeFrame',                                           0x00000001);

  // Constants for enum WdFramesetSizeType
  define('wdFramesetSizeTypePercent',                                     0x00000000);
  define('wdFramesetSizeTypeFixed',                                       0x00000001);
  define('wdFramesetSizeTypeRelative',                                    0x00000002);

  // Constants for enum WdFramesetNewFrameLocation
  define('wdFramesetNewFrameAbove',                                       0x00000000);
  define('wdFramesetNewFrameBelow',                                       0x00000001);
  define('wdFramesetNewFrameRight',                                       0x00000002);
  define('wdFramesetNewFrameLeft',                                        0x00000003);

  // Constants for enum WdScrollbarType
  define('wdScrollbarTypeAuto',                                           0x00000000);
  define('wdScrollbarTypeYes',                                            0x00000001);
  define('wdScrollbarTypeNo',                                             0x00000002);

  // Constants for enum WdTwoLinesInOneType
  define('wdTwoLinesInOneNone',                                           0x00000000);
  define('wdTwoLinesInOneNoBrackets',                                     0x00000001);
  define('wdTwoLinesInOneParentheses',                                    0x00000002);
  define('wdTwoLinesInOneSquareBrackets',                                 0x00000003);
  define('wdTwoLinesInOneAngleBrackets',                                  0x00000004);
  define('wdTwoLinesInOneCurlyBrackets',                                  0x00000005);

  // Constants for enum WdHorizontalInVerticalType
  define('wdHorizontalInVerticalNone',                                    0x00000000);
  define('wdHorizontalInVerticalFitInLine',                               0x00000001);
  define('wdHorizontalInVerticalResizeLine',                              0x00000002);

  // Constants for enum WdHorizontalLineAlignment
  define('wdHorizontalLineAlignLeft',                                     0x00000000);
  define('wdHorizontalLineAlignCenter',                                   0x00000001);
  define('wdHorizontalLineAlignRight',                                    0x00000002);

  // Constants for enum WdHorizontalLineWidthType
  define('wdHorizontalLinePercentWidth',                                  0xFFFFFFFF);
  define('wdHorizontalLineFixedWidth',                                    0xFFFFFFFE);

  // Constants for enum WdPhoneticGuideAlignmentType
  define('wdPhoneticGuideAlignmentCenter',                                0x00000000);
  define('wdPhoneticGuideAlignmentZeroOneZero',                           0x00000001);
  define('wdPhoneticGuideAlignmentOneTwoOne',                             0x00000002);
  define('wdPhoneticGuideAlignmentLeft',                                  0x00000003);
  define('wdPhoneticGuideAlignmentRight',                                 0x00000004);

  // Constants for enum WdNewDocumentType
  define('wdNewBlankDocument',                                            0x00000000);
  define('wdNewWebPage',                                                  0x00000001);
  define('wdNewEmailMessage',                                             0x00000002);
  define('wdNewFrameset',                                                 0x00000003);

  // Constants for enum WdKana
  define('wdKanaKatakana',                                                0x00000008);
  define('wdKanaHiragana',                                                0x00000009);

  // Constants for enum WdCharacterWidth
  define('wdWidthHalfWidth',                                              0x00000006);
  define('wdWidthFullWidth',                                              0x00000007);

  // Constants for enum WdNumberStyleWordBasicBiDi
  define('wdListNumberStyleBidi1',                                        0x00000031);
  define('wdListNumberStyleBidi2',                                        0x00000032);
  define('wdCaptionNumberStyleBidiLetter1',                               0x00000031);
  define('wdCaptionNumberStyleBidiLetter2',                               0x00000032);
  define('wdNoteNumberStyleBidiLetter1',                                  0x00000031);
  define('wdNoteNumberStyleBidiLetter2',                                  0x00000032);
  define('wdPageNumberStyleBidiLetter1',                                  0x00000031);
  define('wdPageNumberStyleBidiLetter2',                                  0x00000032);

  // Constants for enum WdTCSCConverterDirection
  define('wdTCSCConverterDirectionSCTC',                                  0x00000000);
  define('wdTCSCConverterDirectionTCSC',                                  0x00000001);
  define('wdTCSCConverterDirectionAuto',                                  0x00000002);
  
  ::uses('com.microsoft.com.COMObject');

  /**
   * Word COM API
   *
   * @ext      com
   * @see      http://www.phpbuilder.net/columns/alain20001003.php3
   * @see      http://www.4guysfromrolla.com/webtech/040300-1.shtml
   */
  class Word extends COMObject {

    /**
     * Constructor
     *
     */    
    public function __construct() {
      parent::__construct('Word.Application');
    }
  }
?>
