<?php
add_action('admin_menu', 'wtf_collapse_arch_admin');

function wtf_collapse_arch_admin()
{
    $collapse_arch_page = add_submenu_page('theme-plugins', 'Collapsing Archives', 'Collapsing Archives', 'manage_options', 'theme-plugins-collapse-arch', 'wtf_collapse_arch_plugin_page');
    add_action('admin_head-' . $collapse_arch_page,    'wtf_header');
} //end wtf_twitter_admin

function wtf_collapse_arch_plugin_page()
{
    check_admin_referer();

    $options = get_option('collapsArchOptions');
    $widgetOn = 0;
    $number = '%i%';
    if (empty($options)) {
        $number = '-1';
    } elseif (!isset($options['%i%']['title']) || count($options) > 1) {
        $widgetOn = 1;
    }

    if (isset($_POST['resetOptions'])) {
        if (isset($_POST['reset'])) {
            delete_option('collapsArchOptions');
            $widgetOn = 0;
            $number = '-1';
        }
    } elseif (isset($_POST['infoUpdate'])) {
        $style = $_POST['collapsArchStyle'];
        $defaultStyles = get_option('collapsArchDefaultStyles');
        $selectedStyle = $_POST['collapsArchSelectedStyle'];
        $defaultStyles['selected'] = $selectedStyle;
        $defaultStyles['custom'] = $_POST['collapsArchStyle'];

        update_option('collapsArchStyle', $style);
        update_option('collapsArchSidebarId', $_POST['collapsArchSidebarId']);
        update_option('collapsArchDefaultStyles', $defaultStyles);

        if ($widgetOn == 0) {
            $title = strip_tags(stripslashes($new_instance['title']));

            $archSortOrder = ($new_instance['archSortOrder'] == 'ASC') ? 'ASC' : 'DESC';
            $showPosts = ($new_instance['showPosts'] == 'yes') ? true : false;
            $linkToArch = ($new_instance['linkToArch'] == 'yes') ? true : false;
            $showPostCount = (isset($new_instance['showPostCount'])) ? true : false;
            $showArchives = (isset($new_instance['showArchives'])) ? true : false;
            $showYearCount = (isset($new_instance['showYearCount'])) ? true : false;
            $expandCurrentYear = (isset($new_instance['expandCurrentYear'])) ? true : false;
            $expand = $new_instance['expand'];
            $customExpand = $new_instance['customExpand'];
            $customCollapse = $new_instance['customCollapse'];
            $noTitle = $new_instance['noTitle'];
            $includeOrExcludeYears = $new_instance['includeOrExcludeYears'];
            $includeOrExcludeCategories = $new_instance['includeOrExcludeCategories'];

            $expandYears = (isset($new_instance['expandYears'])) ? true : false;
            $showMonthCount = (isset($new_instance['showMonthCount'])) ? true : false;
            $expandMonths = (isset($new_instance['expandMonths'])) ? true : false;
            $showPostTitle = (isset($new_instance['showPostTitle'])) ? true : false;
            $animate = (!isset($new_instance['animate'])) ? 0 : 1;
            $debug = (isset($new_instance['debug'])) ? true : false;
            $showPostDate = (isset($new_instance['showPostDate'])) ? true : false;
            $postDateFormat = addslashes($new_instance['postDateFormat']);
            $postDateAppend = ($new_instance['postDateAppend'] == 'before') ? 'before' : 'after';
            $expandCurrentMonth = (isset($new_instance['expandCurrentMonth'])) ? true : false;
            $yearsToFilter = addslashes($new_instance['yearsToFilter']);
            $postTitleLength = addslashes($new_instance['postTitleLength']);
            $categoriesToFilter = addslashes($new_instance['categoriesToFilter']);
            $defaultExpand = addslashes($new_instance['defaultExpand']);
            $instance = compact(
                    'title',
                    'showPostCount',
                    'includeOrExcludeCategories',
                    'categoriesToFilter',
                    'includeOrExcludeYears',
                    'yearsToFilter',
                    'archSortOrder',
                    'showPosts',
                    'showPages',
                    'linkToArch',
                    'debug',
                    'showYearCount',
                    'expandCurrentYear',
                    'expandMonths',
                    'expandYears',
                    'expandCurrentMonth',
                    'showMonthCount',
                    'showPostTitle',
                    'expand',
                    'noTitle',
                    'customExpand',
                    'customCollapse',
                    'postDateAppend',
                    'showPostDate',
                    'postDateFormat',
                    'animate',
                    'postTitleLength'
            );
        }
    }
    if (-1 == $number) {
        $title = 'Archives';
        $text = '';
        $showPostCount = 'yes';
        $archSortOrder = 'DESC';
        $defaultExpand = '';
        $number = '%i%';
        $expand = '1';
        $customExpand = '';
        $customCollapse = '';
        $noTitle = '';
        $includeOrExcludeCategories = 'include';
        $includeOrExcludeYears = 'include';
        $categoriesToFilter = '';
        $yearsToFilter = '';
        $postTitleLength = '';
        $showPosts = 'yes';
        $linkToArch = 'yes';
        $showArchives = 'no';
        $expandCurrentYear = 'yes';
        $showYearCount = 'yes';
        $expandCurrentMonth = 'yes';
        $expandMonths = 'yes';
        $showMonthCount = 'yes';
        $showMonths = 'yes';
        $showPostTitle = 'yes';
        $showPostDate = 'no';
        $postDateFormat = 'm/d';
        $animate = 1;
        $debug = 0;
    } else {
        $title = attribute_escape($options[$number]['title']);
        $showPostCount = $options[$number]['showPostCount'];
        $expand = $options[$number]['expand'];
        $customExpand = $options[$number]['customExpand'];
        $customCollapse = $options[$number]['customCollapse'];
        $categoriesToFilter = $options[$number]['categoriesToFilter'];
        $yearsToFilter = $options[$number]['yearsToFilter'];
        $postTitleLength = $options[$number]['postTitleLength'];
        $includeOrExcludeCategories = $options[$number]['includeOrExcludeCategories'];
        $includeOrExcludeYears = $options[$number]['includeOrExcludeYears'];
        $archSortOrder = $options[$number]['archSortOrder'];
        $defaultExpand = $options[$number]['defaultExpand'];
        $showPosts = $options[$number]['showPosts'];
        $showArchives = $options[$number]['showArchives'];
        $linkToArch = $options[$number]['linkToArch'];
        $showYearCount = $options[$number]['showYearCount'];
        $expandCurrentYear = $options[$number]['expandCurrentYear'];
        $showMonthCount = $options[$number]['showMonthCount'];
        $showMonths = $options[$number]['showMonths'];
        $expandMonths = $options[$number]['expandMonths'];
        $expandCurrentMonth = $options[$number]['expandCurrentMonth'];
        $showPostTitle = $options[$number]['showPostTitle'];
        $showPostDate = $options[$number]['showPostDate'];
        $postDateFormat = $options[$number]['postDateFormat'];
        $animate = $options[$number]['animate'];
        $debug = $options[$number]['debug'];
        $noTitle = $options[$number]['noTitle'];
    }
    ?>
    <div class=wrap>
        <form method="post">
            <h2>Collapsing Archives Options</h2>
            <fieldset name="Collapsing Archives Options">
                <p>
                    ID of the sidebar where collapsing pages appears:
                    <input id='collapsArchSidebarId' name='collapsArchSidebarId' type='text' size='20' value="<?php echo get_option('collapsArchSidebarId'); ?>" onchange='changeStyle("collapsArchStylePreview","collapsArchStyle", "collapsArchDefaultStyles", "collapsArchSelectedStyle", false);' />
                    <table>
                        <tr>
                            <td>
                                <input type='hidden' id='collapsArchCurrentStyle' value="<?php echo stripslashes(get_option('collapsArchStyle')); ?>" />
                                <input type='hidden' id='collapsArchSelectedStyle' name='collapsArchSelectedStyle' />
                                <label for="collapsArchStyle">Select style:</label>
                            </td>
                            <td>
                                <select name='collapsArchDefaultStyles' id='collapsArchDefaultStyles' onchange='changeStyle("collapsArchStylePreview","collapsArchStyle", "collapsArchDefaultStyles", "collapsArchSelectedStyle", false);' />
                                <?php
                                    $url = get_settings('siteurl') . '/wp-content/plugins/collapsing-archives';
                                    $styleOptions = get_option('collapsArchDefaultStyles');
                                    //print_r($styleOptions);
                                    $selected = $styleOptions['selected'];
                                    foreach ($styleOptions as $key => $value) {
                                        if ($key != 'selected') {
                                            if ($key == $selected) {
                                                $select = ' selected=selected ';
                                            } else {
                                                $select = ' ';
                                            }
                                            echo '<option' . $select . 'value="' . stripslashes($value) . '" >' . $key . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </td>
                            <td>Preview<br />
                                <img style='border:1px solid' id='collapsArchStylePreview' alt='preview'/>
                            </td>
                        </tr>
                    </table>
                    You may also customize your style below if you wish<br />
                    <input type='button' value='restore current style' onclick='restoreStyle();' /><br />
                    <textarea onchange='changeStyle("collapsArchStylePreview","collapsArchStyle", "collapsArchDefaultStyles", "collapsArchSelectedStyle", true);' cols='78' rows='10' id="collapsArchStyle"name="collapsArchStyle"><?php echo stripslashes(get_option('collapsArchStyle')) ?></textarea>
                </p>
                <script type='text/javascript'>
                    function changeStyle(preview,template,select,selected,custom) {
                        var preview = document.getElementById(preview);
                        var pageStyles = document.getElementById(select);
                        var selectedStyle;
                        var hiddenStyle=document.getElementById(selected);
                        var pageStyle = document.getElementById(template);
                        if (custom==true) {
                            selectedStyle=pageStyles.options[pageStyles.options.length-1];
                            selectedStyle.value=pageStyle.value;
                            selectedStyle.selected=true;
                        } else {
                            for(i=0; i<pageStyles.options.length; i++) {
                                if (pageStyles.options[i].selected == true) {
                                    selectedStyle=pageStyles.options[i];
                                }
                            }
                        }
                        hiddenStyle.value=selectedStyle.innerHTML
                        preview.src='<?php echo $url ?>/img/'+selectedStyle.innerHTML+'.png';
                        var sidebarId=document.getElementById('collapsArchSidebarId').value;
                        var theStyle = selectedStyle.value.replace(/#[a-zA-Z]+\s/g, '#'+sidebarId + ' ');
                        pageStyle.value=theStyle
                    }

                    function restoreStyle() {
                        var defaultStyle = document.getElementById('collapsArchCurrentStyle').value;
                        var pageStyle = document.getElementById('collapsArchStyle');
                        pageStyle.value=defaultStyle;
                    }
                    changeStyle('collapsArchStylePreview','collapsArchStyle', 'collapsArchDefaultStyles', 'collapsArchSelectedStyle', false);
                </script>
            </fieldset>
            <div class="submit">
                <input type="submit" name="infoUpdate" value="Update options &raquo;" />
            </div>
        </form>
    </div>
    <?php
} //end wtf_collapse_arch_plugin_page

/*
function wtf_twitter_plugin_page()
{
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        $use_css = ($_POST['use_css'] == 'true') ? true : false;
        update_option('wtf-twitter-default-account', strip_tags(stripslashes($_POST['default_account'])));
        update_option('wtf-twitter-tweet-limit',     strip_tags(stripslashes($_POST['tweet_limit'])));
        update_option('wtf-twitter-time-limit',      strip_tags(stripslashes($_POST['time_limit'])));
        update_option('wtf-twitter-use-css',         $use_css);

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?></h2>
            <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These settings are theme specific and will only apply when the current theme (<strong><?php echo WTF_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Twitter Options</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <div class="option">
                    <label for="default_account">Default Twitter Account</label>
                    <span class="description">This is the default account to be used for any sidebar widgets.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-default-account'); ?>" id="default_account" name="default_account"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="tweet_limit">Tweet Limit</label>
                    <span class="description">The maximum number of tweets to retrieve at a time.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-tweet-limit'); ?>" id="tweet_limit" name="tweet_limit"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="time_limit">Time Limit</label>
                    <span class="description">This is the minimum time, in minutes, the widget will wait before fetching new tweets.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-time-limit'); ?>" id="time_limit" name="time_limit"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="css_yes">Use CSS</label>
                    <span class="description">Enable the default styles for the Twitter widget. Turn this off if you prefer to use your own CSS rules.</span>
                    <fieldset>
                        <label for="css_yes"><input type="radio" value="true" id="css_yes" name="use_css"<?php
                        if ((bool) get_option('wtf-twitter-use-css') == true) {
                            echo ' checked="checked"';
                        }
                        ?> /> Yes</label><br />
                        <label for="css_no"><input type="radio" value="false" id="css_no" name="use_css"<?php
                        if ((bool) get_option('wtf-twitter-use-css') == false) {
                            echo ' checked="checked"';
                        }
                        ?> /> No</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->

    </form>
</div>
<div style="clear:both;height:20px;"></div>
    <?php
} //end wtf_twitter_plugin_page
*/