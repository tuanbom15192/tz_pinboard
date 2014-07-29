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

<div id="tz_create_board_h5s">
    <span>
        <?php echo JText::_('MOD_TZ_PINBOARD_CREATE_A_BOARD'); ?>
    </span>
    <i id="tz_create_board" class="tz-pin-cross"></i>
</div>

<div class="tz_create_board_content">
    <form id="tz_create_board_Form" action="<?php echo JRoute::_('index.php') ?>" method="post">
        <div class="tz_create_board_names">
            <label>
                <?php echo JText::_('MOD_TZ_PINBOARD_BOARD_NAME'); ?>
            </label>
            <input type="text" id="cr_boardnames" maxlength="<?php echo $text_board; ?>" name="boardname" value="">

            <p id="p_create_name"></p>
        </div>

        <div class="tz_create_board_names">
            <label>
                <?php echo JText::_('MOD_TZ_PINBOARD_BOARD_ALIAS'); ?>
            </label>
            <input type="text" id="aliasnames" maxlength="<?php echo $text_board; ?>" name="aliasname" value="">

            <p id="p_aliasname"></p>
        </div>

        <div class="tz_create_board_names">
            <label>
                <?php echo JText::_('MOD_TZ_PINBOARD_DESCRIPTION') ?>
            </label>
            <textarea name="decsipt" id="create_board_textra" maxlength="<?php echo $text_board_ds; ?>"></textarea>

            <p id="p_create_decsipts"></p>
        </div>

        <div class="tz_create_board_names">
            <label>
                <?php echo JText::_('MOD_TZ_PINBOARD_CATEGORY'); ?>
            </label>
            <select name="catego" id="category_boards">
                <option value=""><?php echo JText::_('MOD_TZ_PINBOARD_CREATE_BOARD_CATEGORY'); ?></option>
                <?php
                echo JHtml::_('select.options', JHtml::_('category.options', 'com_tz_pinboard'), 'value', 'text', '');
                ?>
            </select>

            <p id="p_create_select"></p>
        </div>

    </form>
    <div id="create_board_warp_form_submit">
        <input type="button"
               id="submitcreate_cr"
               name="create"
               class="btn"
               value="<?php echo JText::_('MOD_TZ_PINBOARD_CREATE_A_BOARD'); ?>">
    </div>

</div>

