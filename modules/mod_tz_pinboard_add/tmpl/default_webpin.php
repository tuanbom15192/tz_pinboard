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
    <div id="tz_pin_url_name">
    <span>
    <?php echo JText::_("MOD_TZ_PINBOARD_ADD_A_PIN_WEB"); ?>
    </span>
        <i id="tz_pin_url_img" class="tz-pin-cross"></i>
    </div>
    <div id="tz_pin_url_content">
        <form name="frmupload" action="" method="post" enctype="multipart/form-data">

            <div id="tz_pin_url_content_1">
                <input type="text" name="url_img" id="tz_url_img"
                       placeholder="<?php echo JText::_('MOD_TZ_PINBOARD_ADD_A_YOUR_WEBSITE'); ?>">
                <button id="tz_pin_url_button" class="btn" type="button" disabled="disabled">
                    <?php echo JText::_("Find Images"); ?>
                </button>

                <div class="cler"></div>
            </div>

            <div id="tz_pin_loadding"></div>

            <div id="tz_pin_url_content_2">
                <div class="info">
                    <div id="tz_pin_url_content_2_right">
                        <div class="tz_pin_url_input">
                            <label>
                                <?php echo JText::_('MOD_TZ_PINBOARD_ADD_TAG_LABEL'); ?>
                            </label>
                            <input id="tz_pin_url_keygord" type="text" name="keywords_pin_local"
                                   maxlength="<?php echo $text_key; ?>" value="">
                            <input type="hidden" name="tz_price" id="tz_url_price" value="">

                            <p id="tz_url_p_keyword">
                            </p>
                        </div>
                        <div class="tz_pin_url_input">
                            <label><?php echo JText::_('MOD_TZ_PINBOARD_ADD_TITLE_LABEL'); ?></label>
                            <input id="tz_pin_url_title" type="text" name="title_pin_local" title="Title"
                                   maxlength="<?php echo $text_title; ?>" value="">
                            <input type="hidden" name="img_hidde" id="img_hidder" value="">
                            <input type="hidden" name="video_hidden" id="video_hidden" value="">

                            <p id="tz_url_p_title">
                            </p>
                        </div>
                        <div class="tz_pin_url_input">
                            <label><?php echo JText::_('MOD_TZ_PINBOARD_ADD_BOARD_LABEL'); ?></label>
                            <select name="board" id="tz_pin_url_select">
                                <option value=""><?php echo JText::_('MOD_TZ_PINBOARD_SELECT_CATEGORY') ?></option>
                                <?php
                                foreach ($bord as $row) {
                                    ?>
                                    <option value="<?php echo $row->id ?>">
                                        <?php echo $row->title; ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                            <?php if ($param_pinboard->get('state_boar') == 1): ?>
                                <input id="create_board_on_pin_web" class="btn" type="button"
                                       value="<?php echo JText::_('MOD_TZ_PINBOARD_CREATE_BOARD_LABEL'); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="tz_pin_url_input">
                            <label><?php echo JText::_('MOD_TZ_PINBOARD_ADD_DESCRIPTION_LABEL'); ?></label>
                            <textarea id="tz_pin_url_textarea"
                                      title="Descript"
                                      name="tz_descript_url"
                                      maxlength="<?php echo $text_title_ds; ?>">
                            </textarea>

                            <p id="tz_url_p_textarea"></p>
                        </div>

                    </div>

                    <div id="tz_pin_url_content_2_left">
                        <div class="tz_upload_price"></div>
                        <ul id="slider">
                        </ul>
                        <div class="cler"></div>
                    </div>

                    <div class="cler"></div>

                </div>
                <div class="tz_pin_url_input">
                    <input type="button" class="btn btn-large " id="cancel_pin_url"
                           name="cancel"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_CANCEL_A_BOARD'); ?>">
                    <input type="hidden" name="task" value="task_upload_pin">
                    <input class="btn btn-large" id="url_a_pin" type="submit" name="uploadpin"
                           value="<?php echo JText::_('COM_TZ_PINBOARD_ADDPINBOARD_WEB_PIN'); ?>">

                    <p class="tz_click_button"></p>
                    <?php echo JHtml::_('form.token'); ?>
                </div>
            </div>
            <input type="hidden" name="option" value="com_tz_pinboard"/>
            <input type="hidden" name="view" value="addpinboards"/>
        </form>
        <div class="create_board_web">

            <form id="tz_create_board_Form_web" action="<?php echo JRoute::_('index.php') ?>" method="post">
                <div class="header_form">
                <span>
                    <?php echo JText::_('MOD_TZ_PINBOARD_ADD_BOARD_ON_PIN_LABEL'); ?>
                </span>
                    <i id="tz-pin-cross-web" class="tz-pin-cross"></i>
                </div>
                <div class="tz_create_board_names1">

                    <input type="text" id="boardnames_web" maxlength="<?php echo $text_board; ?>"
                           name="boardname" value=""
                           placeholder=" <?php echo JText::_('MOD_TZ_PINBOARD_ADD_BOARD_ON_PIN_LABEL'); ?>">

                    <select name="catego" id="category_boards_web">
                        <option
                            value=""><?php echo JText::_('MOD_TZ_PINBOARD_BOARD_CATEGORY'); ?></option>
                        <?php
                        echo JHtml::_('select.options', JHtml::_('category.options', 'com_tz_pinboard'), 'value', 'text', '');
                        ?>
                    </select>

                    <p id="error_create_board_on_pin"></p>

                    <p id="error_choose_category_on_pin"></p>

                    <div class="cler"></div>
                </div>
                <div id="create_board_warp_form_submit_web">

                    <input type="hidden" name="task" value="tz.insert.board.on.pin">
                    <input type="hidden" name="option" value="com_tz_pinboard"/>
                    <input type="hidden" name="view" value="addpinboards"/>
                    <input type="button" class="btn btn-large submitcreate1" id="submitcreate_web"
                           name="create"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_CREATE_A_BOARD'); ?>">
                    <input type="button" class="btn btn-large cancel_create_board_on_pin"
                           id="cancel_create_board_on_pin_web"
                           value="<?php echo JText::_('MOD_TZ_PINBOARD_CANCEL_A_BOARD'); ?>">
                </div>

            </form>
        </div>
    </div>
<?php if ($param_pinboard->get('state_boar') == 1): ?>
    <script type="text/javascript">
        jQuery(window).ready(function () {
            jQuery('#submitcreate_web').click(function () {

                if (jQuery('#boardnames_web').val() == "") {
                    jQuery('#error_create_board_on_pin').css('display', 'block');
                    document.getElementById('error_create_board_on_pin').innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_ADD_ERROR_NAME_BOARD_LABEL');?>";
                    jQuery('#boardnames_web').focus();
                    return false;
                }

                if (jQuery('#category_boards_web').val() == "") {
                    jQuery('#error_choose_category_on_pin').css('display', 'block');
                    document.getElementById('error_choose_category_on_pin').innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_ADD_ERROR_CHOOSE_CATEGORY_ON_PIN_LABEL');?>";
                    jQuery('#category_boards_web').focus();
                    return false;
                }

                jQuery.ajax({
                    url: 'index.php?option=com_tz_pinboard&view=addpinboards&task=tz.insert.board.on.pin&Itemid=<?php echo JRequest::getVar('Itemid')?>',
                    type: 'post',
                    data: {
                        name_board: jQuery('#boardnames_web').val(),
                        name_category: jQuery('#category_boards_web').val()
                    }
                }).success(function (data) {
                        jQuery('.create_board_web').fadeOut(1200);
                        jQuery('.create_board_overlay').fadeOut(1000);
                        var parData = jQuery.parseJSON(data);
                        jQuery('#tz_pin_url_select').append('<option value="' + parData.id + '" selected="selected">' + parData.title + '</option>');
                    });
            });

        });
    </script>
<?php endif; ?>