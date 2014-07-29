<?php
/*------------------------------------------------------------------------

# TZ Pinboard Extension

# ------------------------------------------------------------------------

# author    TuNguyenTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/
defined("_JEXEC") or die;
$pth_c = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR .
    'com_tz_pinboard' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'category.php';
require_once $pth_c;
jimport('joomla.html');
?>

    <div class="create_board_overlay"></div>
    <div id="tz_pin_upload_name">
    <span>
    <?php echo JText::_('MOD_TZ_PINBOARD_LOCAL_PIN'); ?>
    </span>
        <i id="tz_pin_upload" class="tz-pin-cross"></i>
    </div>
    <div id="tz_pin_upload_content">
        <form name="frmupload"
              action="" method="post"
              enctype="multipart/form-data">

            <div id="tz_pin_upload_content_1">
                <input onchange="readURL(this);" id="upload_pin" type="file" name="upload_pinl">
            </div>

            <div id="tz_pin_upload_content_2">
                <div class="info">
                    <div id="tz_pin_upload_right">

                        <div class="tz_pin_upload_input">
                            <label>
                                <?php echo JText::_('MOD_TZ_PINBOARD_ADD_TAG_LABEL'); ?>
                            </label>
                            <input id="tz_pin_upload_keyword" type="text" name="keywords_pin_local"
                                   maxlength="<?php echo $text_key; ?>"
                                   placeholder="<?php echo JText::_('MOD_TZ_PINBOARD_YOUR_KEYWORDS'); ?>" value="">

                            <p id="tz_p_local_keyword"></p>
                            <input type="hidden" id="tz_pinPrice" name="tz_price" value="">
                        </div>

                        <div class="tz_pin_upload_input">
                            <label><?php echo JText::_('MOD_TZ_PINBOARD_ADD_TITLE_LABEL'); ?></label>
                            <input id="tz_pin_upload_title" type="text" name="title_pin_local"
                                   maxlength="<?php echo $text_title; ?>"
                                   placeholder="<?php echo JText::_('MOD_TZ_PINBOARD_YOUR_TITLE'); ?>" value="">

                            <p id="tz_p_title"></p>
                        </div>

                        <div class="tz_pin_upload_input">
                            <label><?php echo JText::_('MOD_TZ_PINBOARD_ADD_BOARD_LABEL'); ?></label>
                            <select name="board" id="tz_pin_upload_select">
                                <option value=""><?php echo JText::_('MOD_TZ_PINBOARD_SELECT_CATEGORY') ?></option>
                                <?php foreach ($bord as $row) { ?>
                                    <option value="<?php echo $row->id ?>">
                                        <?php echo $row->title; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <?php if ($param_pinboard->get('state_boar') == 1): ?>
                                <input id="create_board_on_pin_up" class="btn" type="button"
                                       value="<?php echo JText::_('MOD_TZ_PINBOARD_CREATE_BOARD_LABEL'); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="tz_pin_upload_input">
                            <label><?php echo JText::_('MOD_TZ_PINBOARD_ADD_DESCRIPTION_LABEL'); ?></label>
                            <textarea name="tz_descript_url"
                                      id="tz_pin_pin_textarea"
                                      maxlength="<?php echo $text_title_ds; ?>"
                                      placeholder="<?php echo JText::_('MOD_TZ_PINBOARD_YOUR_DESCRIPTION'); ?>">
                            </textarea>

                            <p id="tz_p_textarea"></p>
                        </div>

                    </div>

                    <div id="tz_pin_upload_left">
                        <div id="tz_pin_upload_left_img">
                            <div class="tz_upload_price"></div>
                            <img id="tz_pin_upload_left_img_load" src="#" alt="">
                        </div>
                    </div>
                </div>
                <div class="tz_pin_upload_input">

                    <input type="button"
                           class="btn btn-large "
                           id="cancel_a_pin"
                           name="cancel"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_CANCEL_A_BOARD'); ?>"/>

                    <input type="hidden" name="task" value="upload.local.pin"/>

                    <input class="btn btn-large"
                           id="upload_a_pin"
                           type="submit"
                           name="uploadpin"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_WEB_PIN'); ?>"/>


                    <p class="tz_click_button"></p>
                    <?php echo JHtml::_('form.token'); ?>
                </div>

                <div class="cler"></div>
            </div>
            <input type="hidden" name="option" value="com_tz_pinboard"/>
            <input type="hidden" name="view" value="addpinboards"/>
        </form>
        <div class="create_board_up">

            <form id="tz_create_board_Form_up" action="<?php echo JRoute::_('index.php') ?>" method="post">
                <div class="header_form">
                <span>
                    <?php echo JText::_('MOD_TZ_PINBOARD_ADD_BOARD_ON_PIN_LABEL'); ?>
                </span>
                    <i id="tz-pin-cross-up" class="tz-pin-cross"></i>
                </div>
                <div class="tz_create_board_names1">

                    <input type="text" id="boardnames_up" maxlength="<?php echo $text_board; ?>"
                           name="boardname" value=""
                           placeholder=" <?php echo JText::_('MOD_TZ_PINBOARD_ADD_BOARD_ON_PIN_LABEL'); ?>">

                    <select name="catego" id="category_boards_up">
                        <option
                            value=""><?php echo JText::_('MOD_TZ_PINBOARD_BOARD_CATEGORY'); ?></option>
                        <?php
                        echo JHtml::_('select.options', JHtml::_('category.options', 'com_tz_pinboard'), 'value', 'text', '');
                        ?>
                    </select>

                    <p id="error_create_board_on_up"></p>

                    <p id="error_choose_category_on_up"></p>

                    <div class="cler"></div>
                </div>
                <div id="create_board_warp_form_submit_up">

                    <input type="hidden" name="task" value="tz.insert.board.on.pin">
                    <input type="hidden" name="option" value="com_tz_pinboard"/>
                    <input type="hidden" name="view" value="addpinboards"/>
                    <input type="button" class="btn btn-large submitcreate1" id="create_board_upload"
                           name="create"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_CREATE_A_BOARD'); ?>">
                    <input type="button" class="btn btn-large cancel_create_board_on_pin"
                           id="cancel_create_board_on_pin_up"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_CANCEL_A_BOARD'); ?>">
                </div>

            </form>
        </div>
    </div>
<?php if ($param_pinboard->get('state_boar') == 1): ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {

            jQuery('#create_board_on_pin_up').click(function () {
                jQuery('.create_board_overlay').fadeIn(1000);
                jQuery('.create_board_up').fadeIn(1200);
            });

            jQuery('#cancel_create_board_on_pin_up').click(function () {
                jQuery('.create_board_overlay').fadeOut(1000);
                jQuery('.create_board_up').fadeOut(1200);
            });

            jQuery('#tz-pin-cross-up').click(function () {
                jQuery('.create_board_up').fadeOut(1200);
                jQuery('.create_board_overlay').fadeOut(1000);

            });

            jQuery('#create_board_upload').click(function () {

                if (jQuery('#boardnames_up').val() == "") {
                    jQuery('#error_create_board_on_up').css('display', 'block');
                    document.getElementById('error_create_board_on_up').innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_ADD_ERROR_NAME_BOARD_LABEL');?>";
                    jQuery('#boardnames_up').focus();
                    return false;
                }

                if (jQuery('#category_boards_up').val() == "") {
                    jQuery('#error_choose_category_on_up').css('display', 'block');
                    document.getElementById('error_choose_category_on_up').innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_ADD_ERROR_CHOOSE_CATEGORY_ON_PIN_LABEL');?>";
                    jQuery('#category_boards_up').focus();
                    return false;
                }

                jQuery.ajax({
                    url: 'index.php?option=com_tz_pinboard&view=addpinboards&task=tz.insert.board.on.pin&Itemid=<?php echo JRequest::getVar('Itemid')?>',
                    type: 'post',
                    data: {
                        name_board: jQuery('#boardnames_up').val(),
                        name_category: jQuery('#category_boards_up').val()
                    }
                }).success(function (data) {
                        jQuery('.create_board_up').fadeOut(1200);
                        jQuery('.create_board_overlay').fadeOut(1000);
                        var parData = jQuery.parseJSON(data);
                        jQuery('#tz_pin_upload_select').append('<option value="' + parData.id + '" selected="selected">' + parData.title + '</option>');
                    });
            });
        });
    </script>
<?php endif; ?>