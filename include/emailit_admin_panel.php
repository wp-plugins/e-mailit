<?php
defined('ABSPATH') or die('No direct access permitted');

add_filter('admin_menu', 'emailit_admin_menu');

function emailit_admin_menu() {
    add_options_page('E-MAILiT Settings', 'E-MAILiT Share', 'manage_options', basename(__FILE__), 'emailit_settings_page');
}

function emailit_settings_page() {
    ?>
    <div id="emailit_admin_panel">
        <h1 class="header">E-MAILiT <span>Share Settings</span></h1>
        <form onsubmit="return validate()" id="emailit_options" action="options.php" method="post">
            <?php
            settings_fields('emailit_options');
            $emailit_options = get_option('emailit_options');
            ?>
            <script type="text/javascript">
                function validate() {
                    var e_mailit_default_servises = jQuery.map(jQuery('#social_services li'), function (element) {
                        return jQuery(element).attr('class').replace(/E_mailit_/gi, '').replace(/ ui-sortable-handle/gi, '');
                    }).join(',');

                    jQuery('#servicess input.ui-helper-hidden-accessible').attr("disabled", "disabled");
                    jQuery('#default_buttons').val(e_mailit_default_servises);

                    var follow_services = {};
                    jQuery("#social_services_follow .follow-field").each(function () {
                        if (jQuery(this).val() !== "") {
                            var name = jQuery(this).attr('name').replace(/follow_/gi, '');
                            follow_services[name] = jQuery(this).val();
                        }
                    });
                    jQuery("#follow_services").val(JSON.stringify(follow_services));
                    jQuery('#social_services_follow .follow-field').attr("disabled", "disabled");

                    if (!jQuery('#emailit_showonhome').is(':checked') && !jQuery('#emailit_showonarchives').is(':checked')
                            && !jQuery('#emailit_showoncats').is(':checked') && !jQuery('#emailit_showonpages').is(':checked') && !jQuery('#emailit_showonexcerpts').is(':checked') && !jQuery('#emailit_showonposts').is(':checked'))
                        alert("Select a placement option to display the button.");

                    return true;
                }
                jQuery(function () {
                    jQuery("#tabs").tabs();

                    jQuery("input[type='checkbox']").bootstrapSwitch();

                    jQuery('#colorSelector div,#colorSelector2 div').css('backgroundColor', '<?php echo $emailit_options["back_color"] ?>');
                    jQuery('#colorSelector,#colorSelector2').ColorPicker({
                        color: '<?php echo $emailit_options["back_color"] ?>',
                        onShow: function (colpkr) {
                            if (!jQuery(this).attr('disabled'))
                                jQuery(colpkr).fadeIn(500);
                            return false;
                        },
                        onHide: function (colpkr) {
                            jQuery(colpkr).fadeOut(500);
                            return false;
                        },
                        onChange: function (hsb, hex, rgb) {
                            jQuery("#colorpickerField").val("#" + hex);
                            jQuery("#colorpickerField2").val("#" + hex);
                            jQuery('#colorSelector div').css('backgroundColor', '#' + hex);
                            jQuery('#colorSelector2 div').css('backgroundColor', '#' + hex);
                        }
                    }).bind('keyup', function () {
                        jQuery(this).ColorPickerSetColor(this.value);
                    });

                    jQuery("#colorpickerField, #colorpickerField2").change(function () {
                        jQuery('#colorSelector div').css('backgroundColor', jQuery(this).val());
                        jQuery('#colorSelector2 div').css('backgroundColor', jQuery(this).val());
                        jQuery("#colorpickerField, #colorpickerField2").val(jQuery(this).val());
                    });

                    jQuery('#colorSelector3 div').css('backgroundColor', '<?php echo $emailit_options["text_color"] ?>');
                    jQuery('#colorSelector3').ColorPicker({
                        color: '<?php echo $emailit_options["text_color"] ?>',
                        onShow: function (colpkr) {
                            if (!jQuery(this).attr('disabled'))
                                jQuery(colpkr).fadeIn(500);
                            return false;
                        },
                        onHide: function (colpkr) {
                            jQuery(colpkr).fadeOut(500);
                            return false;
                        },
                        onChange: function (hsb, hex, rgb) {
                            jQuery("#colorpickerField3").val("#" + hex);
                            jQuery('#colorSelector3 div').css('backgroundColor', '#' + hex);
                        }
                    }).bind('keyup', function () {
                        jQuery(this).ColorPickerSetColor(this.value);
                    });
                    jQuery("#colorpickerField3").change(function () {
                        jQuery('#colorSelector3 div').css('backgroundColor', jQuery(this).val());
                    });

                    e_mailit_config = {mobile_bar: false};
                    jQuery.getScript("//www.e-mailit.com/widget/menu3x/js/button.js", function () {
    <?php
    if (isset($emailit_options["default_buttons"]) && $emailit_options["default_buttons"] !== "") {
        echo "var default_buttons ='" . $emailit_options["default_buttons"] . "';" . PHP_EOL;
    } else {
        echo "var default_buttons ='Facebook,Twitter,Google_Plus,Pinterest,LinkedIn,Gmail';" . PHP_EOL;
    }
    ?>
                        var share = e_mailit.services.split(","); // Get buttons
                        for (var key in share) {
                            var services = share[key];
                            var name = services.replace(/_/gi, " ");
                            if (name === "Google Plus")
                                name = "Google+";
                            if (name === "Facebook Like and Share")
                                name = "Facebook Like & Share";
                            var sharelinkInput = jQuery("<input type=\"checkbox\" id=\"checkbox" + services + "\" name=\"" + services + "\" />");
                            var sharelinkLabel = jQuery("<label for=\"checkbox" + services + "\" class='services_list' id=\"" + services + "\" ><div class=\"E_mailit_" + services + "\"> </div> <span class='services_list_name'>" + name + "</span></label>");
                            sharelinkInput.appendTo('#servicess');
                            sharelinkLabel.appendTo('#servicess');
                        }

                        jQuery("#servicess input[type=checkbox]").click(function () {
                            if (jQuery(this).is(':checked')) {
                                var class_name = this.name.replace(/_/gi, " ");
                                if (class_name === "Google Plus")
                                    class_name = "Google+";
                                if (class_name === "Facebook Like and Share")
                                    class_name = "Facebook Like & Share";

                                jQuery('#social_services').append('<li title="' + class_name + '" class="E_mailit_' + this.name + '"></li>');
                                jQuery("#E_mailit_" + this.name + "").effect("transfer", {
                                    to: "#social_services ." + this.name
                                }, 500);
                            }
                            else {
                                jQuery("#social_services .E_mailit_" + this.name).effect("transfer", {
                                    to: "#" + this.name + ""
                                }, 500).delay(500).remove();
                            }
                        });

                        var new_share = default_buttons.split(","); // Get buttons
                        addButtons(new_share);

                        jQuery("#social_services").sortable({
                            revert: true,
                            opacity: 0.8
                        });
                        jQuery("ul#social_services, #social_services li").disableSelection();
                        jQuery("#check").button();
                        jQuery("#servicess").buttonset();
                        jQuery(".uncheck_all_btn").click(function () {
                            jQuery("#servicess input[type=checkbox]").attr('checked', false);
                            jQuery("#servicess input[type=checkbox]").button("refresh");
                            jQuery("#social_services").empty();
                            jQuery("#servicess input:not(:checked)").button("option", "disabled", false);
                            jQuery(".message_good").show("fast");
                        });

                        jQuery(".social_services_default_btn").click(function () {
                            jQuery(".uncheck_all_btn").click();
                            addButtons(new_share);
                            jQuery("#servises_customize_btn").show('fast');
                            jQuery("#social_services #custom,#servicess,.filterinput,.social_services_default_btn,.message_good,.message_bad,.uncheck_all_btn").hide('fast');
                            styleChanged();
                        });

                        jQuery("#servises_customize_btn").click(function () {
                            jQuery("#servises_customize_btn").hide('fast');
                            jQuery("#social_services #custom,#servicess,.filterinput,.message_good,.social_services_default_btn,.uncheck_all_btn").show('fast');
                        });

                        jQuery.expr[':'].Contains = function (a, i, m) {//boitheia gia to search me ta grammata tis :contains
                            return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
                        };
                        jQuery('#filter-form-input-text').keyup(function (event) {
                            var filter = jQuery('#filter-form-input-text').val();
                            if (filter == '' || filter.length < 1) {
                                jQuery(".services_list").show();
                            }
                            else {
                                jQuery(".services_list").find(".services_list_name:not(:Contains(" + filter + "))").parent().parent().hide();
                                jQuery(".services_list").find(".services_list_name:Contains(" + filter + ")").parent().parent().show();
                            }
                            var value = jQuery("input[name='emailit_options[toolbar_type]']:checked").val();
                            var nativeServices = ["Facebook", "Facebook_Like", "Facebook_Like_and_Share", "Facebook_Send", "Twitter", "Google_Plus", "LinkedIn", "Pinterest", "VKontakte", "Odnoklassniki"];
                            if (value === "native") {
                                jQuery("#servicess label").each(function () {
                                    if (jQuery.inArray(jQuery(this).attr('id'), nativeServices) < 0) {
                                        jQuery(this).hide();
                                    }
                                });
                            } else {
                                jQuery("#servicess label#Facebook_Like, #servicess label#Facebook_Like_and_Share").hide();
                            }
                            jQuery("#servicess").buttonset("refresh");                            
                        });

                        var follow_values = JSON.parse('<?php echo $emailit_options["follow_services"] ?>');
                        for (var key in e_mailit.follows_links) {
                            var link_with_input = e_mailit.follows_links[key].replace(/{FOLLOW}/gi, '<input class="follow-field" name="follow_' + key + '" type="text">');
                            jQuery("#social_services_follow").append('<li><i class="E_mailit_' + key + '"></i>' + link_with_input + '</li>');
                            if (follow_values && follow_values[key]) {
                                jQuery("#social_services_follow .follow-field[name='follow_" + key + "']").val(follow_values[key]);
                            }
                        }
                        jQuery("input[name='emailit_options[toolbar_type]']").click(function () {
                            styleChanged();
                        });
                        function styleChanged() {
                            jQuery('#filter-form-input-text').val("");
                            var nativeServices = ["Facebook", "Facebook_Like", "Facebook_Like_and_Share", "Facebook_Send", "Twitter", "Google_Plus", "LinkedIn", "Pinterest", "VKontakte", "Odnoklassniki"];
                            var value = jQuery("input[name='emailit_options[toolbar_type]']:checked").val();
                            if (value === "native") {
                                jQuery("#emailit_circular").hide();
                                jQuery("#emailit_text_display").show();
                                jQuery("#emailit_text_color").show();
                                jQuery("#servicess label").show();
                                jQuery("#servicess label").each(function () {
                                    if (jQuery.inArray(jQuery(this).attr('id'), nativeServices) < 0) {
                                        jQuery(this).hide();
                                        jQuery("#social_services li.E_mailit_" + jQuery(this).attr('id')).remove();
                                        jQuery("#servicess input#checkbox" + jQuery(this).attr('id')).prop('checked', false);
                                    }
                                });
                            } else {
                                jQuery("#emailit_text_display").hide();
                                jQuery("#emailit_text_color").hide();
                                jQuery("#emailit_circular").show();
                                jQuery("#servicess label").show();
                                jQuery("#servicess label#Facebook_Like, #servicess label#Facebook_Like_and_Share").hide();
                                jQuery("#social_services li.E_mailit_Facebook_Like, #social_services li.E_mailit_Facebook_Like_and_Share").remove();
                                jQuery("#servicess input#checkboxFacebook_Like, #servicess input#checkboxFacebook_Like_and_Share").prop('checked', false);
                            }
                            jQuery("#servicess").buttonset("refresh");
                        }
                        styleChanged();
                    });
                });
                function addButtons(new_share) {
                    for (var key in new_share) {
                        var service = new_share[key];
                        jQuery('#servicess #checkbox' + service).click();
                    }
                }
            </script>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-share_buttons">SHARE BUTTONS</a></li>
                    <li><a href="#tabs-advanced">ADVANCED OPTIONS</a></li>
                </ul>
                <div id="tabs-share_buttons">
                    <div class="emailit_admin_panel_section">
                        <h3>Customize your buttons</h3>
                        <label class="label">STYLE</label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="large" <?php echo ($emailit_options["toolbar_type"] == "large" ? 'checked="checked"' : ''); ?>/>
                                    Large
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="small" <?php echo ($emailit_options["toolbar_type"] == "small" ? 'checked="checked"' : ''); ?>/>
                                    Small
                                </label>
                            </li>
                            <li>                                
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="native" <?php echo ($emailit_options["toolbar_type"] == "native" ? 'checked="checked"' : ''); ?>/>
                                    Native (original 3rd party share buttons)
                                </label>
                            </li>
                        </ul>
                        <ul class="fields">
                            <li>                                
                                <label>BACKGROUND COLOR (leave it blank for default style)</label>
                                <div class="colorpicker_widget">
                                    <input type="text" name="emailit_options[back_color]" maxlength="7" size="7" id="colorpickerField2" value="<?php echo $emailit_options["back_color"] ?>" />
                                    <div class="colorpicker_square" id="colorSelector2"><div style="background-color: #0000ff"></div></div>
                                </div>
                            </li>  
                            <li>
                                <label>COUNTERS</label>
                                <input type="checkbox" name="emailit_options[display_counter]" value="true" <?php echo ($emailit_options['display_counter'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>                            
                            <li id="emailit_circular">
                                <label>FLAT CIRCLE ICON SHAPE</label>
                                <input type="checkbox" name="emailit_options[circular]" value="true" <?php echo ($emailit_options['circular'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>                            
                        </ul>                        
                    </div>
                    <div class="emailit_admin_panel_section">
                        <h3>Global button (more sharing options)</h3>
                        <label>ORDER</label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[global_button]" value="last" <?php echo ($emailit_options['global_button'] == 'last' ? 'checked="checked"' : ''); ?>/>                               
                                    Show last in sharing toolbar
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[global_button]" value="first" <?php echo ($emailit_options['global_button'] == 'first' ? 'checked="checked"' : ''); ?>/>
                                    Show first in sharing toolbar
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[global_button]" value="disabled" <?php echo ($emailit_options['global_button'] == 'disabled' ? 'checked="checked"' : ''); ?>/>
                                    Deactivate
                                </label>
                            </li>
                        </ul>
                        <label>OPEN GLOBAL SHARING MENU ON</label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[open_on]" value="onclick" <?php echo ($emailit_options['open_on'] == 'onclick' ? 'checked="checked"' : ''); ?>/>  
                                    Click
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[open_on]" value="onmouseover" <?php echo ($emailit_options['open_on'] == 'onmouseover' ? 'checked="checked"' : ''); ?>/>
                                    Hover
                                </label>
                            </li>
                        </ul>
                        <ul class="fields">
                            <li id="emailit_text_display">
                                <label>SHARE TEXT</label>
                                <input type="text" name="emailit_options[text_display]" value="<?php
                                if ($emailit_options['text_display'])
                                    echo $emailit_options['text_display'];
                                else
                                    echo "Share";
                                ?>"/>                                
                            </li>
                            <li id="emailit_text_color">
                                <label>TEXT COLOR (leave it blank for default style)</label>
                                <div class="colorpicker_widget">
                                    <input type="text" name="emailit_options[text_color]" maxlength="7" size="7" id="colorpickerField3" value="<?php echo $emailit_options["text_color"] ?>" />
                                    <div class="colorpicker_square" id="colorSelector3"><div style="background-color: #0000ff"></div></div>
                                </div>                                
                            </li>
                            <li>
                                <label>AUTO SHOW SHARE OVERLAY AFTER</label>					
                                <input min="0" max="1000" type="number" name="emailit_options[auto_popup]" value="<?php
                                if ($emailit_options['auto_popup'])
                                    echo $emailit_options['auto_popup'];
                                else
                                    echo '0';
                                ?>"/> sec
                            </li>                             
                        </ul>
                    </div>
                    <div class="emailit_admin_panel_section">
                        <h3>Floating</h3>
                        <label>SHARE SIDEBAR</label>
                        <ul class="radio">                            
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_bar]" value="disabled" <?php echo ($emailit_options['floating_bar'] == 'disabled' ? 'checked="checked"' : ''); ?>/>
                                    Deactivate</label>
                            </li>
                            <li>
                                <label>                        
                                    <input type="radio" name="emailit_options[floating_bar]"  value="left" <?php echo ($emailit_options['floating_bar'] == 'left' ? 'checked="checked"' : ''); ?>/>
                                    Left</label>
                            </li>
                            <li>
                                <label>                           
                                    <input type="radio" name="emailit_options[floating_bar]"  value="right" <?php echo ($emailit_options['floating_bar'] == 'right' ? 'checked="checked"' : ''); ?>/>
                                    Right</label>
                            </li>
                        </ul>
                        <ul class="fields">
                            <li>
                                <label>MOBILE SHARE BAR</label>
                                <input id="mobile_bar" type="checkbox" name="emailit_options[mobile_bar]" value="true" <?php echo ($emailit_options['mobile_bar'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>                        
                        </ul>
                    </div>
                    <div class="emailit_admin_panel_section">
                        <h3>Standalone services</h3>
                        <label></label>
                        <div class="out-of-the-box">
                            <ul id="social_services" class="large"></ul>
                            <div class="services_buttons">
                                <a style="display:none;" class="social_services_default_btn">Restore settings</a>
                                <a style="display:none;" class="uncheck_all_btn">Clear all</a>
                                <a id="servises_customize_btn">Customize...</a> 
                            </div>                            
                            <div class="message_good" style="display:none">Select your buttons</div>
                            <div class="filterinput">
                                <input placeholder="Search for services" data-type="search" id="filter-form-input-text">
                            </div>                        
                            <div id="servicess" class="large">
                            </div>
                            <input id="default_buttons" name="emailit_options[default_buttons]" value="<?php echo $emailit_options['default_buttons']; ?>" type="hidden"/>
                        </div>
                        <ul class="fields">
                            <li><label>TWEET VIA (your Twitter username)</label>
                                <input type="text" name="emailit_options[TwitterID]" value="<?php echo $emailit_options['TwitterID']; ?>"/>
                            </li>
                            <li>
                                <label>PINTEREST SHAREABLE IMAGES</label>
                                <input id="hover_pinit" type="checkbox" name="emailit_options[hover_pinit]" value="true" <?php echo ($emailit_options['hover_pinit'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3>Placement</h3>
                        <label>DISPLAY</label>
                        <ul class="radio">
                            <li>
                                <label>                          
                                    <input type="radio" name="emailit_options[button_position]"  value="both" <?php echo ($emailit_options['button_position'] == 'both' ? 'checked="checked"' : ''); ?>/>
                                    Both</label>
                            </li>
                            <li>
                                <label>                            
                                    <input type="radio" name="emailit_options[button_position]" value="bottom" <?php echo ($emailit_options['button_position'] == 'bottom' ? 'checked="checked"' : ''); ?>/>
                                    Below content</label>
                            </li>
                            <li>
                                <label>                          
                                    <input type="radio" name="emailit_options[button_position]"  value="top" <?php echo ($emailit_options['button_position'] == 'top' ? 'checked="checked"' : ''); ?>/>
                                    Above content</label>
                            </li>
                        </ul>  
                        <label>LOCATIONS</label>
                        <label class="above">Homepage<br />
                            <input id="emailit_showonhome" type="checkbox" name="emailit_options[emailit_showonhome]" value="true" <?php echo ($emailit_options['emailit_showonhome'] == true ? 'checked="checked"' : ''); ?>/>
                        </label>
                        <label class="above">Posts<br />
                            <input id="emailit_showonposts" type="checkbox" name="emailit_options[emailit_showonposts]" value="true" <?php echo ($emailit_options['emailit_showonposts'] == true ? 'checked="checked"' : ''); ?>/>
                        </label>
                        <label class="above">Pages<br />
                            <input id="emailit_showonpages" type="checkbox" name="emailit_options[emailit_showonpages]" value="true" <?php echo ($emailit_options['emailit_showonpages'] == true ? 'checked="checked"' : ''); ?>/>
                        </label>
                        <label class="above">Excerpts<br />
                            <input id="emailit_showonexcerpts" type="checkbox" name="emailit_options[emailit_showonexcerpts]" value="true" <?php echo ($emailit_options['emailit_showonexcerpts'] == true ? 'checked="checked"' : ''); ?>/>  
                        </label>                    
                        <label class="above">Archives<br />
                            <input id="emailit_showonarchives" type="checkbox" name="emailit_options[emailit_showonarchives]" value="true" <?php echo ($emailit_options['emailit_showonarchives'] == true ? 'checked="checked"' : ''); ?>/>
                        </label>
                        <label class="above">Categories<br />
                            <input id="emailit_showoncats" type="checkbox" name="emailit_options[emailit_showoncats]" value="true" <?php echo ($emailit_options['emailit_showoncats'] == true ? 'checked="checked"' : ''); ?>/>
                        </label>
                    </div>
                </div>
                <div id="tabs-advanced">
                    <ul class="fields">                            
                        <li>
                            <label>AFTER SHARE PROMO</label>
                            <input id="after_share_dialog" type="checkbox" name="emailit_options[after_share_dialog]" value="true" <?php echo ($emailit_options['after_share_dialog'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>                       
                        <li>
                            <label>AFTER SHARE PROMO HEADING</label>
                            <input placeholder="Thanks for sharing! Like our content? Follow us!" name="emailit_options[thanks_message]" type="text" value="<?php echo $emailit_options["thanks_message"] ?>">
                        </li>
                    </ul>
                    <div class="follow_services">
                        <label>FOLLOW SERVICES (show on After Share Promo)</label>
                        <ul id="social_services_follow" class="large">

                        </ul>
                        <input id="follow_services" name="emailit_options[follow_services]" value="<?php echo $emailit_options['follow_services']; ?>" type="hidden"/>
                    </div> 
                    <ul class="fields">                            
                        <li>
                            <label>DISPLAY ADVERTS</label>					
                            <input id="display_ads" type="checkbox" name="emailit_options[display_ads]" value="true" <?php echo ($emailit_options['display_ads'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>
                        <li>
                            <label>MONETIZE (show on After Share Promo)</label>
                            <label>Insert your Ad Unit (or Promo) location</label>
                            <input placeholder="http://" name="emailit_options[ad_url]" type="text" value="<?php echo $emailit_options["ad_url"] ?>">
                        </li>
                    </ul>
                </div>
                <p>
                    <input id="submit" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
                <p/>
            </div>	
            <p>
                If you like our work, show some love and <a href="http://wordpress.org/support/view/plugin-reviews/e-mailit?rate=5#postform" target="_blank">give us a good rating</a>. Made with love in Athens, Greece.
            </p>
            <p>
                <a href="https://twitter.com/emailit" class="twitter-follow-button" data-show-count="true">Follow @emailit</a>
                <script>!function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, 'script', 'twitter-wjs');</script>
            </p>
        </form>
    </div>

    <?php
}
