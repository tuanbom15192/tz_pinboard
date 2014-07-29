<?php

# TZ Pinboard Extension

# ------------------------------------------------------------------------

# author    TuNguyenTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

#-------------------------------------------------------------------------*/
defined("_JEXEC") or die;

$doc = JFactory::getDocument();
$doc->addStyleSheet('modules/mod_tz_pinboard_add/css/tz_add.css');
$doc->addStyleSheet('modules/mod_tz_pinboard_add/css/anythingslider.css');
$doc->addCustomTag('<script type="text/javascript" src="modules/mod_tz_pinboard_add/js/jquery.anythingslider.js"></script>');

?>
<script type="text/javascript">
// method check text
function checkText(text, maxtext) {
    var countText = text.length;
    var text = maxtext - countText;
    return text;
}

jQuery(document).ready(function () {

    jQuery('#add_button_pin_board').click(function () {
        jQuery('#tz_pinboard_add_warp').toggle();
        jQuery('#add_button_pin_board').toggleClass('tz_active');
    });
    jQuery('#tz_pinboard_add_name_img').click(function () {
        jQuery('#tz_pinboard_add').animate({top: '15px'}, 600);
        jQuery('#tz_pinboard_add_warp').fadeOut();
    });
    jQuery("#add_button_pin_err").click(function () {
        alert("<?php echo JText::_('MOD_TZ_PINBOARD_LOGIN'); ?>");
        window.location = "<?php echo JURI::root() . "index.php?option=com_users&view=login"; ?>";
    });

    jQuery('#tz_pinboard_add_pin_wed,#tz_pinboard_add_pin_local, #tz_pinboard_add_pin_creboa').click(function () {
        jQuery('.tz_pinboard_full').css('display', 'block');
    });

    jQuery('#tz_pinboard_add_pin_wed').click(function () {
        jQuery('#tz_url_img').keyup(function () {
            if (jQuery('#tz_url_img').val() != '') {
                jQuery('#tz_pin_url_button').prop('disabled', false);
            } else {
                jQuery('#tz_pin_url_button').prop('disabled', true);
            }
        });
        jQuery('#tz_pin_url_content_2').fadeOut(100);
        jQuery('#tz_pin_url_content_1').fadeIn(100);
        jQuery('#tz_pin_add_url_pin').fadeIn(100, function () {
            jQuery('#tz_pin_add_url_pin').animate({bottom: '50%'}, 500);
        });


    });
    jQuery('#tz_pin_url_img').click(function () {
        jQuery('#tz_pin_add_url_pin').animate({bottom: '-100%'}, 600, function () {
            jQuery('#tz_pin_add_url_pin').fadeOut(1200);
        });
        jQuery('.tz_pinboard_full').fadeOut(1200);
        jQuery('.tz_upload_price').animate({"display": "block", "left": "-200px"});
        jQuery('#tz_pin_url_content_2').fadeOut(1200);

    });

    jQuery('#create_board_on_pin_web').click(function () {
        jQuery('.create_board_overlay').fadeIn(1000);
        jQuery('.create_board_web').fadeIn(1200);
    });

    // upload pin //////////////////////////////
    jQuery('#tz_pinboard_add_pin_local').click(function () {
        jQuery("#tz_pin_add_upload_pin").slideDown(200);
        jQuery('#tz_pin_add_upload_pin').animate({bottom: '50%'}, 500);
        jQuery('#tz_pin_upload_content_1').fadeIn(500);
        jQuery('#upload_pin').change(function () {
            jQuery('#tz_pin_upload_content_1').fadeOut(1000, function () {
                jQuery('#tz_pin_add_upload_pin').animate({bottom: '20%'}, 500);
                jQuery('#tz_pin_upload_content_2').slideDown();
            });
        });
    });
    jQuery('#tz_pin_upload').click(function () {
        jQuery('.tz_upload_price').animate({"display": "block", "left": "-200px"});
        jQuery('.tz_pinboard_full').fadeOut(1200);
        jQuery('#tz_pin_upload_content_2').fadeOut();
        jQuery('#tz_pin_add_upload_pin').animate({"bottom": "-60%"}, 600, function () {
            jQuery('#tz_pin_add_upload_pin').fadeOut(1200);
        });
    });

    jQuery('#cancel_a_pin').click(function () {
        jQuery('#tz_pin_add_url_pin').fadeOut(1200);
        jQuery('.tz_pinboard_full').fadeOut(1200);
        jQuery('#tz_pin_add_upload_pin').fadeOut(1200);
        jQuery('#tz_pin_upload_content_2').fadeOut(1200);
    });

    jQuery('#cancel_pin_url').click(function () {
        jQuery('#tz_pin_add_url_pin').fadeOut(1200);
        jQuery('.tz_pinboard_full').fadeOut(1200);
    });

    //create board on layout pin
    jQuery('#tz-pin-cross-web').click(function () {
        jQuery('.create_board_web').fadeOut(1200);
        jQuery('.create_board_overlay').fadeOut(1000);

    });
    jQuery('#cancel_create_board_on_pin_web').click(function () {
        jQuery('.create_board_overlay').fadeOut(1000);
        jQuery('.create_board_web').fadeOut(1200);
    });

    //pin url
    jQuery('#tz_pin_url_button').click(function () {
        jQuery("#slider li").remove();
        var srt3 = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!-\/]))?/;
        var check_url_img = jQuery('#tz_url_img').val();
        if (check_url_img == "") {
            alert("<?php echo JText::_('MOD_TZ_PINBOARD_CHECK_TITLE'); ?>");
            document.getElementById('tz_url_img').focus();
            return false;
        } else if (srt3.test(check_url_img) == false) {
            alert("<?php echo JText::_('MOD_TZ_PINBOARD_CHECK_URL'); ?>");
            document.getElementById('tz_url_img').focus();
            return false;
        }
        jQuery('#tz_pin_loadding').html("<img src='<?php echo JUri::root().'/components/com_tz_pinboard/images/loading-icon.gif'?>' />").fadeIn('fast');
        jQuery.ajax({
            url: 'index.php?option=com_tz_pinboard&view=addpinboards&task=add_pin_website&Itemid=<?php echo JRequest::getVar('Itemid'); ?>',
            type: 'post',
            data: {
                link: jQuery('#tz_url_img').val()
            }
        }).success(function (data) {
                jQuery('#tz_pin_loadding').fadeOut('fast');

                var getData = jQuery.parseJSON(data);

                if (getData != 'notimage' && getData != false) {

                    jQuery("#tz_pin_url_keygord").val(getData.keywoa);
                    jQuery("#tz_pin_url_title").val(getData.title);
                    jQuery("#tz_pin_url_textarea").val(getData.introtext);
                    jQuery("#video_hidden").val(getData.video);
                    var str_http = /^http.*$|^\/\//;        // check url
                    var str = /^\/.*$/;               // check url
                    var i = 0;
                    for (i; i < getData.img.length; i++) {

                        if (str_http.test(getData.img[i]) == true) {
                            getData.img[i] = getData.img[i];
                        } else if (str.test(getData.img[i]) == true) {
                            getData.img[i] = getData.url + getData.img[i];
                        } else {
                            getData.img[i] = getData.url2 + getData.img[i];
                        }
                        var img = jQuery('<img src="' + getData.img[i] + '" alt=""/>').load(function () {
                            if (this.width > <?php echo $param_pinboard->get('width_image');?> && this.height > <?php echo $param_pinboard->get('height_image');?>) {
                                jQuery("#slider").append('<li><img alt="" src="' + this.src + '"/></li>');
                            }
                            jQuery('#slider').anythingSlider({
                                enableArrows: true,      // if false, arrows will be visible, but not clickable.
                                enableNavigation: false,      // if false, navigation links will still be visible, but not clickable.
                                enableStartStop: false
                            });

                        });
                    }

                    jQuery('#tz_pin_url_content_1').fadeOut(1000, function () {
                        jQuery('#tz_pin_add_url_pin').animate({bottom: '20%'}, 1000);
                        jQuery('#tz_pin_url_content_2').fadeIn(2000);
                    });


                    jQuery('#tz_pin_url_keygord').focus(function () {
                        jQuery('#tz_pin_url_keygord').keyup(function () {
                            var Max_Text_input = jQuery('#tz_pin_url_keygord').attr('maxlength');
                            var Text_value_input = jQuery('#tz_pin_url_keygord').attr('value');
                            var p_title = document.getElementById('tz_url_p_keyword');
                            var Text_k = checkText(Text_value_input, Max_Text_input);
                            if (Text_k > 0) {
                                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_KEY'); ?>: " + Text_k;
                            } else {
                                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_KEY_0'); ?>";
                            }
                        });
                    });
                    jQuery('#tz_pin_url_keygord').blur(function () {
                        var p_title = document.getElementById('tz_url_p_keyword');
                        p_title.innerHTML = " ";
                    });
                    jQuery('#tz_pin_url_title').focus(function () {
                        jQuery('#tz_pin_url_title').keyup(function () {
                            var Max_Text_input = jQuery('#tz_pin_url_title').attr('maxlength');
                            var Text_value_input = jQuery('#tz_pin_url_title').attr('value');
                            var p_title = document.getElementById('tz_url_p_title');
                            var Text_t = checkText(Text_value_input, Max_Text_input);
                            if (Text_t > 0) {
                                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_TITLE') ?>: " + Text_t;
                            } else {
                                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_TITLE') ?>: 0";
                            }
                        });
                    });
                    jQuery('#tz_pin_url_title').blur(function () {
                        var p_title = document.getElementById('tz_url_p_title');
                        p_title.innerHTML = " ";
                    }); // over title


                    jQuery('#tz_pin_url_textarea').focus(function () { // jquery  Descript
                        jQuery('#tz_pin_url_textarea').keyup(function () {
                            var Max_Text_input = jQuery('#tz_pin_url_textarea').attr('maxlength');
                            var Text_value_input = jQuery('#tz_pin_url_textarea').attr('value');
                            var p_title = document.getElementById('tz_url_p_textarea');
                            var Text_d = checkText(Text_value_input, Max_Text_input);
                            if (Text_d > 0) {
                                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_DESCRIPTION'); ?>: " + Text_d;
                            } else {
                                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_DESCRIPTION_0'); ?>";
                            }
                            if (key_cost.test(Text_value_input) == true) {
                                jQuery('.tz_upload_price').animate({"left": "-28px"}, 100);
                                var Results = Text_value_input.match(reg);
                                jQuery('.tz_upload_price').html(Results);
                            } else {
                                jQuery('.tz_upload_price').animate({"left": "-120px"}, 100);
                                jQuery('.tz_upload_price').html(" ");
                            }
                        });
                    });
                    jQuery('#tz_pin_url_textarea').blur(function () {
                        var p_title = document.getElementById('tz_url_p_textarea');
                        p_title.innerHTML = " ";
                    });

                    jQuery('#url_a_pin').click(function () {
                        if (jQuery('li').hasClass('activePage') == false) {
                            var srcc = jQuery('li.panel > img').attr("src");
                            jQuery('#img_hidder').attr('value', srcc);
                        } else {
                            var srcc = jQuery('li.activePage > img').attr("src");
                            jQuery('#img_hidder').attr('value', srcc);
                        }
                        var srcc = jQuery('.tz_upload_price').html();
                        jQuery('#tz_url_price').attr('value', srcc);
                        var Title = jQuery('#tz_pin_url_title').attr('value');
                        var boards = jQuery("#tz_pin_url_select").val();
                        if (Title == "") {
                            alert("<?php echo JText::_('MOD_TZ_PINBOARD_CHECK_TITLE'); ?>");
                            jQuery('#tz_pin_url_title').focus();
                            return false;
                        }
                        if (boards == "" || boards == "create_board") {
                            alert("<?php echo JText::_('MOD_TZ_PINBOARD_ERRO_SELECT'); ?>");
                            jQuery("#tz_pin_upload_select").focus();
                            return false;
                        }
                        jQuery(".tz_click_button").css("z-index", "99");
                    });
                } else {
                    if (getData == 'notimage') {
                        alert('<?php echo JText::_('MOD_TZ_PINBOARD_ERROR_NOT_IMAGE');?>');
                        jQuery('#tz_url_img').focus();
                        return false;
                    } else {
                        alert("<?php echo JText::_('MOD_TZ_PINBOARD_ERRO_URL'); ?>");
                        jQuery('#tz_url_img').focus();
                        return false;
                    }
                }
            });
    });
    var reg = /(\s)?\$(\s)?[0-9\.]{1,100}/;     // check price
    var key_cost = /(\s)?\$(\s)?[0-9].*?$/; // check price
    // jquey  create board
    jQuery('#tz_pinboard_add_pin_creboa').click(function () {
        jQuery('#tz_create_board_add').animate({"bottom": "20%"}, 600, function () {
            jQuery('#tz_create_board_add').slideDown();
        });
    });
    jQuery('#tz_create_board').click(function () {
        jQuery('#tz_create_board_add').animate({bottom: '-100%'}, 600, function () {
            jQuery('#tz_create_board_add').fadeOut(1000);
        });
        jQuery('.tz_pinboard_full').fadeOut(1000);
    });
    // check name board
    jQuery('#cr_boardnames').focus(function () {
        var inpName = jQuery('#cr_boardnames').attr('value');
        var p_title = document.getElementById("p_create_name");
        jQuery('#cr_boardnames').keyup(function () {
            var maxName = jQuery('#cr_boardnames').attr('maxlength');
            var inpName = jQuery('#cr_boardnames').attr('value');
            var Text_b = checkText(inpName, maxName);
            if (Text_b > 0) {
                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_BOARD_NAME'); ?>" + Text_b;
            } else {
                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_BOARD_NAME_0') ?>";
            }
        });
    });
    jQuery('#cr_boardnames').blur(function () {
        var p_title = document.getElementById("p_create_name");
        p_title.innerHTML = "";
    });

    ////////////////////////// check alias
    jQuery('#aliasnames').focus(function () {
        var inpName = jQuery('#aliasnames').attr('value');
        var p_title = document.getElementById("p_aliasname");
        jQuery('#aliasnames').keyup(function () {
            var maxName = jQuery('#aliasnames').attr('maxlength');
            var inpName = jQuery('#aliasnames').attr('value');
            var Text_a = checkText(inpName, maxName);
            if (Text_a > 0) {
                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_BOARD_ALIAS'); ?>" + Text_a;
            } else {
                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_BOARD_ALIAS_0') ?>";
            }
        });
    });
    jQuery('#aliasnames').blur(function () {
        var p_title = document.getElementById("p_aliasname");
        p_title.innerHTML = "";
    });

    // check descript
    jQuery('#create_board_textra').focus(function () {
        var inpName = jQuery('#create_board_textra').attr('value');
        var p_title = document.getElementById("p_create_decsipts");
        jQuery('#create_board_textra').keyup(function () {
            var maxName = jQuery('#create_board_textra').attr('maxlength');
            var inpName = jQuery('#create_board_textra').attr('value');
            var Text_d = checkText(inpName, maxName);
            if (Text_d > 0) {
                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_DESCRIPTION'); ?>" + Text_d;
            } else {
                p_title.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_DESCRIPTION_0') ?>";
            }
        });
    });

    jQuery('#create_board_textra').blur(function () {
        var p_title = document.getElementById("p_create_decsipts");
        p_title.innerHTML = "";
    });

    ///// submit
    jQuery('#submitcreate_cr').click(function () {
        var inpName = jQuery('#cr_boardnames').attr('value');
        var pp = document.getElementById("p_create_name");
        var selec = jQuery('#category_boards').attr('value');
        var p_sele = document.getElementById("p_create_select");
        if (inpName == "") {
            pp.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_CHECK_TITLE'); ?>";
            jQuery('#cr_boardnames').focus();
            return false;
        }
        if (selec == '') {
            p_sele.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_NOTICE_CATEGORY'); ?>";
            jQuery('#category_boards').focus();
            return false;
        } else {
            p_sele.innerHTML = "";
        }

        jQuery.ajax({
            url: 'index.php?option=com_tz_pinboard&view=addpinboards&task=tz.insert.board&Itemid=<?php echo JRequest::getVar('Itemid')?>',
            type: 'post',
            data: {
                boardname: jQuery('#cr_boardnames').val(),
                aliasname: jQuery('#aliasnames').val(),
                catego: jQuery('#category_boards').val(),
                decsipt: jQuery('#create_board_textra').val()
            }
        }).success(function (data) {
                <?php if( $param_pinboard->get('state_boar') == 1): ?>
                alert('<?php echo JText::_("MOD_TZ_ADD_PINBOARD_ADD_CREATE_BOARD1");?>');
                <?php else:?>
                alert('<?php echo JText::_("MOD_TZ_ADD_PINBOARD_ADD_CREATE_BOARD2");?>');
                <?php endif;?>
                jQuery('#tz_create_board_add').animate({"bottom": "-60%"}, 600, function () {
                    jQuery('#tz_create_board_add').fadeOut(1000);
                });
                jQuery('.tz_pinboard_full').fadeOut(1200);
            });
    });

    //upload pin local //////////////////////////////////////////////////////
    jQuery('#tz_pin_upload_keyword').focus(function () {
        jQuery('#tz_pin_upload_keyword').keyup(function () {
            var max_text_ky = jQuery('#tz_pin_upload_keyword').attr('maxlength');
            var text_key = jQuery('#tz_pin_upload_keyword').val();
            var p_key = document.getElementById('tz_p_local_keyword');
            var Text_w = checkText(text_key, max_text_ky);
            if (Text_w > 0) {
                p_key.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_KEY'); ?>: " + Text_w;
            } else {
                p_key.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_KEY_0'); ?>";
            }
        })
    });

    jQuery('#tz_pin_upload_keyword').blur(function () {
        document.getElementById('tz_p_local_keyword').innerHTML = "";
    });

    jQuery('#tz_pin_upload_title').focus(function () {
        jQuery('#tz_pin_upload_title').keyup(function () {
            var max_text_ky = jQuery('#tz_pin_upload_title').attr('maxlength');
            var text_key = jQuery('#tz_pin_upload_title').val();
            var p_key = document.getElementById('tz_p_title');
            var Text_t = checkText(text_key, max_text_ky);
            if (Text_t > 0) {
                p_key.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_TITLE'); ?>: " + Text_t;
            } else {
                p_key.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_TITLE_0'); ?>";
            }
        });
    });
    jQuery('#tz_pin_upload_title').blur(function () {
        document.getElementById('tz_p_title').innerHTML = "";
    });

    jQuery('#tz_pin_pin_textarea').focus(function () {
        jQuery('#tz_pin_pin_textarea').keyup(function () {
            var max_text_ky = jQuery('#tz_pin_pin_textarea').attr('maxlength');
            var text_key = jQuery('#tz_pin_pin_textarea').val();
            if (key_cost.test(text_key) == true) {
                var Results = text_key.match(reg);
                jQuery('.tz_upload_price').html(Results);
                jQuery('.tz_upload_price').animate({"left": "-28px"}, 100);

            } else {
                jQuery('.tz_upload_price').animate({"left": "-120px"}, 100);
                jQuery('.tz_upload_price').html(" ");

            }
            var p_key = document.getElementById('tz_p_textarea');
            var Text_d = checkText(text_key, max_text_ky);
            if (Text_d > 0) {
                p_key.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_DESCRIPTION'); ?>: " + Text_d;
            } else {
                p_key.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_PIN_WEBSITE_DESCRIPTION_0'); ?>";
            }

        });
    });
    jQuery('#tz_pin_pin_textarea').blur(function () {
        document.getElementById('tz_p_textarea').innerHTML = "";
    });

    //  button Pin

    jQuery('#upload_a_pin').click(function () {
        var srcc = jQuery('.tz_upload_price').html();
        jQuery('#tz_pinPrice').attr('value', srcc);
        var content_title = jQuery('#tz_pin_upload_title').val();
        var title_p = document.getElementById("tz_p_title");
        var boards = jQuery("#tz_pin_upload_select").val();
        if (boards == "") {
            alert("<?php echo JText::_('MOD_TZ_PINBOARD_ERRO_SELECT'); ?>");
            jQuery("#tz_pin_upload_select").focus();
            return false;
        }
        if (content_title == "") {
            title_p.innerHTML = "<?php echo JText::_('MOD_TZ_PINBOARD_LOCAL_TITLE'); ?>";
            jQuery('#pin_add_upload_title_pin_local').focus();
            return false;
        } else {
            title_p.innerHTML = "";
        }
        jQuery(".tz_click_button").css("z-index", "99");

    });
});
</script>
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                jQuery('#tz_pin_upload_left_img_load')
                    .attr('src', e.target.result)
                    .width(160);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<div class="mod_tz_pinboard_add">
    <?php if (isset($user) && !empty($user)) { ?>
        <button id="add_button_pin_board" class="btn add_pinboard">
            <i class="tz-pin-plus2"></i>
        </button>
    <?php } else { ?>
        <button id="add_button_pin_err" class="btn add_pinboard">
            <i class="tz-pin-plus2"></i>
        </button>
    <?php } ?>

    <div id="tz_pinboard_add_warp">
        <div class="tz_pinboard_full"></div>
        <div id="tz_pinboard_add">
            <div id="tz_pinboard_add_name_gt">
            <span>
            <?php echo JText::_("MOD_TZ_PINBOARD_ADD_DISCRIPTION"); ?>
            </span>
            </div>
            <div id="tz_pinboard_add_all">
                <ul>
                    <li class="tz_pinboard_add_pin_webiste">
                        <a id="tz_pinboard_add_pin_wed" href="#">
                            <i class="tz-pin-globe"></i>

                            <span>
                                <?php echo JText::_("MOD_TZ_PINBOARD_ADD_A_PIN"); ?>
                            </span>

                        </a>
                    </li>
                    <li class="tz_pinboard_add_pin_webiste">
                        <a id="tz_pinboard_add_pin_local" href="#">
                            <i class="tz-pin-arrow-up"></i>

                            <span>
                                <?php echo JText::_("MOD_TZ_PINBOARD_ADD_A_UPLOAD"); ?>
                            </span>

                        </a>
                    </li>
                    <li class="tz_pinboard_add_pin_webiste">
                        <a id="tz_pinboard_add_pin_creboa" href="#">
                            <i class="tz-pin-th-small"></i>

                            <span>
                                <?php echo JText::_("MOD_TZ_PINBOARD_CREATE_BOARD"); ?>
                            </span>

                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div id="tz_pin_add_upload_pin">
            <?php require JModuleHelper::getLayoutPath('mod_tz_pinboard_add', $params->get('layout', 'default' . '_uploadpin')); ?>
        </div>
        <div id="tz_pin_add_url_pin">
            <?php require JModuleHelper::getLayoutPath('mod_tz_pinboard_add', $params->get('layout', 'default' . '_webpin')); ?>
        </div>
        <div id="tz_create_board_add">
            <?php require JModuleHelper::getLayoutPath('mod_tz_pinboard_add', $params->get('layout', 'default' . '_createboard')); ?>
        </div>
    </div>
</div>
