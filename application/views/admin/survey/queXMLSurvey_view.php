<div class='side-body <?php echo getSideBodyClass(false); ?>'>
    <?php echo CHtml::form(array("admin/export/sa/quexml/surveyid/{$surveyid}/"), 'post'); ?>
        <div class='container-center'>
            <h3 class="col-12">
                <?php eT("queXML PDF export");?>
                <button role="button" type='submit' class="btn btn-primary pull-right">
                    <?php eT("queXML PDF export"); ?>
                </button>
            </h3>
            <div class="form-group row"><label class=" form-label" for='save_language'><?php eT("Language selection"); ?></label>
                <div class="">
                    <select class="form-select" name='save_language'>
                        <?php foreach ($slangs as $lang)
                        {
                            if ($lang == $baselang) { ?>
                                <option value='<?php echo $lang; ?>' selected='selected'><?php echo getLanguageNameFromCode($lang,false); ?></option>
                                <?php }
                            else { ?>
                                <option value='<?php echo $lang; ?>'><?php echo getLanguageNameFromCode($lang,false); ?></option>
                                <?php }
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12"><label class=" form-label" for='queXMLStyle'><?php eT("Style:"); ?></label>
                    <div class="">

                        <textarea class="form-control" rows="10" id='queXMLStyle' name='queXMLStyle'><?php echo $queXMLStyle; ?> </textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- First column -->
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class=" form-label" for='queXMLSingleResponseAreaHeight'><?php eT("Minimum height of single choice answer boxes"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLSingleResponseAreaHeight' name='queXMLSingleResponseAreaHeight' value="<?php echo $queXMLSingleResponseAreaHeight; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLSingleResponseHorizontalHeight'><?php eT("Minimum height of subquestion items"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLSingleResponseHorizontalHeight' name='queXMLSingleResponseHorizontalHeight' value="<?php echo $queXMLSingleResponseHorizontalHeight; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class="form-label" for='queXMLQuestionnaireInfoMargin'><?php eT("Margin before questionnaireInfo element (mm)"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLQuestionnaireInfoMargin' name='queXMLQuestionnaireInfoMargin' value="<?php echo $queXMLQuestionnaireInfoMargin; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLResponseTextFontSize'><?php eT("Answer option / subquestion font size"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLResponseTextFontSize' name='queXMLResponseTextFontSize' value="<?php echo $queXMLResponseTextFontSize; ?>" />
                    </div>

                    <div class="form-group row">                
                        <label class=" form-label" for='queXMLResponseLabelFontSize'><?php eT("Answer label font size (normal)"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLResponseLabelFontSize' name='queXMLResponseLabelFontSize' value="<?php echo $queXMLResponseLabelFontSize; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLResponseLabelFontSizeSmall'><?php eT("Answer label font size (small)"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLResponseLabelFontSizeSmall' name='queXMLResponseLabelFontSizeSmall' value="<?php echo $queXMLResponseLabelFontSizeSmall; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLSectionHeight'><?php eT("Minimum section height (mm)"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLSectionHeight' name='queXMLSectionHeight' value="<?php echo $queXMLSectionHeight; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLBackgroundColourSection'><?php eT("Background colour for sections (0 black - 255 white)"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLBackgroundColourSection' name='queXMLBackgroundColourSection' value="<?php echo $queXMLBackgroundColourSection; ?>" />
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLBackgroundColourQuestion'><?php eT("Background colour for questions (0 black - 255 white)"); ?></label>
                        <input class="col-12 form-control" type='text' size='10' id='queXMLBackgroundColourQuestion' name='queXMLBackgroundColourQuestion' value="<?php echo $queXMLBackgroundColourQuestion; ?>" />
                    </div>
                </div>
                <!-- Second column -->
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class=" form-label" for='queXMLAllowSplittingSingleChoiceHorizontal'><?php eT("Allow array style questions to be split over multiple pages"); ?></label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name'    => 'queXMLAllowSplittingSingleChoiceHorizontal',
                                'value'   => $queXMLAllowSplittingSingleChoiceHorizontal == 1,
                                'onLabel' => gT('Yes'),
                                'offLabel'=> gT('No')));
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLAllowSplittingSingleChoiceVertical'><?php eT("Allow single choice questions to be split over multiple pages"); ?></label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name'      => 'queXMLAllowSplittingSingleChoiceVertical',
                                'value'     => $queXMLAllowSplittingSingleChoiceVertical == 1,
                                'onLabel'   => gT('Yes'),
                                'offLabel'  => gT('No')));
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLAllowSplittingMatrixText'><?php eT("Allow Multiple short text / numeric questions to be split over multiple pages"); ?></label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name'    => 'queXMLAllowSplittingMatrixText',
                                'value'   => $queXMLAllowSplittingMatrixText == 1,
                                'onLabel' => gT('Yes'),
                                'offLabel'=> gT('No')));
                            ?>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLAllowSplittingVas'><?php eT("Allow slider questions to be split over multiple pages"); ?></label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name'      => 'queXMLAllowSplittingVas',
                                'value'     => $queXMLAllowSplittingVas == 1,
                                'onLabel'   => gT('Yes'),
                                'offLabel'  => gT('No')));
                            ?>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLPageOrientation'><?php eT("Page orientation:"); ?></label>
                        <div>
                            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', array(
                                'name' => 'queXMLPageOrientation',
                                'checkedOption'=> $queXMLPageOrientation ,
                                'selectOptions'=>array(
                                    "P"=>gT("Portrait",'unescaped'),
                                    "L"=>gT("Landscape",'unescaped')
                                )
                            ));?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLPageFormat'><?php eT("Page format:"); ?></label>
                        <div>
                            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', array(
                                'name' => 'queXMLPageFormat',
                                'checkedOption'=> $queXMLPageFormat ,
                                'selectOptions'=>array(
                                    "A4"=>gT("A4",'unescaped'),
                                    "A3"=>gT("A3",'unescaped'),
                                    "USLETTER"=>gT("US Letter",'unescaped')
                                )
                            ));?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class=" form-label" for='queXMLEdgeDetectionFormat'><?php eT("Edge detection format:"); ?></label>
                        <div>
                            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', array(
                                'name' => 'queXMLEdgeDetectionFormat',
                                'checkedOption'=> $queXMLEdgeDetectionFormat ,
                                'selectOptions'=>array(
                                    "lines"=>gT("Corner lines",'unescaped'),
                                    "boxes"=>gT("Corner boxes",'unescaped')
                                    )
                                ));?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-label"><?php eT("Reset to default settings:"); ?></label>
                        <div>
                            <?php echo CHtml::htmlButton(gT('Reset now'), array(
                                'class' => 'btn btn-danger col-12 col-md-6',
                                'data-submit-form' => 1,
                                'data-use-form-id' => 1,
                                'data-form-to-save' => 'quexmlclearform',
                                ));?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 ">
                    <input type='hidden' name='ok' value='Y' />
                </div>
            </div>
            
        </div>
    </form>
    <?php 
        // Form for "Reset" action. Submitted when clicking the "Reset now" button (inside the main form)
        echo CHtml::form(array("admin/export/sa/quexmlclear/surveyid/{$surveyid}/"), 'post', array('id'=>'quexmlclearform'));
        echo CHtml::htmlButton(gT('Reset now'), array(
            'type' => 'submit',
            'class' => 'btn btn-danger col-12 col-md-6 d-none',
            ));
    ?>
    </form>
</div>
<?php
App()->getClientScript()->registerScript('QueXMLExportBSSwitcher', "
LS.renderBootstrapSwitch();
", LSYii_ClientScript::POS_POSTSCRIPT);
?>

