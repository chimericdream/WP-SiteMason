<?php
add_action('widgets_init', 'registerCollapsArchWidget');

function registerCollapsArchWidget()
{
    register_widget('collapsArchWidget');
} //end registerCollapsArchWidget

class collapsArchWidget extends WP_Widget
{
    function collapsArchWidget()
    {
        $widget_ops = array(
            'classname' => 'widget_collapsarch',
            'description' => 'Collapsible archives listing'
        );
        $control_ops = array(
            'width' => '400',
            'height' => '400'
        );
        $this->WP_Widget('collapsarch', 'Collapsing Archives', $widget_ops, $control_ops);
    } //end collapsArchWidget

    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);

        $title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
        echo $before_widget . $before_title . $title . $after_title;
        $instance['number'] = $this->get_field_id('top');
        $instance['number'] = preg_replace('/[a-zA-Z-]/', '', $instance['number']);
        echo "<ul id='" . $this->get_field_id('top') . "' class='collapsing archives list'>\n";
        if (function_exists('collapsArch')) {
            echo collapsArch($instance);
        } else {
            wp_list_archives();
        }
        echo "</ul>\n";
        echo $after_widget;
    } //end widget

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

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

        return $instance;
    } //end update

    function form($instance)
    {
        $defaults = array(
            'title' => 'Archives',
            'noTitle' => '',
            'includeOrExcludeCategories' => 'exclude',
            'categoriesToFilter' => '',
            'includeOrExcludeYears' => 'exclude',
            'yearsToFilter' => '',
            'showPages' => false,
            'sort' => 'DESC',
            'linkToArch' => true,
            'showYearCount' => true,
            'expandCurrentYear' => true,
            'expandMonths' => true,
            'expandYears' => true,
            'expandCurrentMonth' => true,
            'showMonthCount' => true,
            'showPostTitle' => true,
            'expand' => '0',
            'showPostDate' => false,
            'debug' => '0',
            'postDateFormat' => 'm/d',
            'postDateAppend' => 'after',
            'animate' => 0,
            'postTitleLength' => ''
        );

        $options = wp_parse_args($instance, $defaults);
        extract($options);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:
                <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
            </label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPostCount'); ?>" <?php if ($showPostCount == 'yes') echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>"></input>
            <label for="collapsArchShowPostCount">Show Post Count</label>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPages'); ?>" <?php if ($showPages == 'yes') echo 'checked'; ?> id="<?php echo $this->get_field_id('showPages'); ?>"></input>
            <label for="collapsArchShowPages">Show Pages as well as posts</label>
        </p>
        <p>Display archives in
            <select name="<?php echo $this->get_field_name('sort'); ?>">
                <option <?php if ($sort == 'ASC') echo 'selected'; ?> id="<?php echo $this->get_field_id('sort'); ?>" value='ASC'>Chronological order</option>
                <option <?php if ($sort == 'DESC') echo 'selected'; ?> id="<?php echo $this->get_field_id('sort'); ?>" value='DESC'>Reverse Chronological order</option>
            </select>
        </p>
        <p>Expanding and collapse characters:<br />
            <strong>html:</strong>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 0) echo 'checked'; ?> id="expand0" value='0'></input>
            <label for="expand0">&#9658;&nbsp;&#9660;</label>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 1) echo 'checked'; ?> id="expand1" value='1'></input>
            <label for="expand1">+&nbsp;&mdash;</label>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 2) echo 'checked'; ?> id="expand2" value='2'></input>
            <label for="expand2">[+]&nbsp;[&mdash;]</label>&nbsp;&nbsp;
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 4) echo 'checked'; ?> id="expand4" value='4'></input>
            <label for="expand4">custom</label>
            expand:
            <input type="text" size='1' name="<?php echo $this->get_field_name('customExpand'); ?>" value="<?php echo $customExpand ?>" id="<?php echo $this->get_field_id('customExpand'); ?>"></input>
            collapse:
            <input type="text" size='1' name="<?php echo $this->get_field_name('customCollapse'); ?>" value="<?php echo $customCollapse ?>" id="<?php echo $this->get_field_id('customCollapse'); ?>"></input>
            <strong>images:</strong>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 3) echo 'checked'; ?> id="expand0" value='3'></input>
            <label for="expand3"><img src='<?php echo get_settings('siteurl') . "/wp-content/plugins/collapsArch/" ?>img/collapse.gif' />&nbsp;<img src='<?php echo get_settings('siteurl') . "/wp-content/plugins/collapsArch/" ?>img/expand.gif' /></label>
        </p>
        <p>
            <select name="<?php echo $this->get_field_name('inExcludePage'); ?>">
                <option <?php if ($inExcludePage == 'include') echo 'selected'; ?> id="<?php echo $this->get_field_id(''); ?>" value='include'>Include</option>
                <option <?php if ($inExcludePage == 'exclude') echo 'selected'; ?> id="<?php echo $this->get_field_id(''); ?>" value='exclude'>Exclude</option>
            </select>
            these years (separated by commas):<br />
            <input type="text" name="<?php echo $this->get_field_name('yearsToFilter'); ?>" value="<?php echo $yearsToFilter ?>" id="<?php echo $this->get_field_id('yearsToFilter'); ?>"></input>
        </p>
        <p>
            <select name="<?php echo $this->get_field_name('includeOrExcludeCategories'); ?>">
                <option <?php if ($includeOrExcludeCategories == 'include') echo 'selected'; ?> id="<?php echo $this->get_field_id('inExcludeCatInclude') ?>" value='include'>Include</option>
                <option <?php if ($includeOrExcludeCategories == 'exclude') echo 'selected'; ?> id="<?php echo $this->get_field_id('inExcludeCatExclude') ?>" value='exclude'>Exclude</option>
            </select>
            these categories (input slug or ID separated by commas):<br />
            <input type="text" name="<?php echo $this->get_field_name('categoriesToFilter'); ?>" value="<?php echo $categoriesToFilter ?>" id="<?php echo $this->get_field_id('categoriesToFilter') ?>"</input>
        </p>
        <p>Clicking on year/month:<br />
            <input type="radio" name="<?php echo $this->get_field_name('linkToArch'); ?>" <?php if ($linkToArch) echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>" value='yes'></input>
            <label for="collapsArch-linkToArchYes">Links to archive</label>
            <input type="radio" name="<?php echo $this->get_field_name('linkToArch'); ?>" <?php if (!$linkToArch) echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>" value='no'></input>
            <label for="linkToArchNo">Expands list</label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('expandCurrentYear'); ?>" <?php if ($expandCurrentYear) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandCurrentYear'); ?>"></input>
            <label for="expandCurrentYear">Leave Current Year Expanded by Default</label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showYearCount'); ?>" <?php if ($showYearCount) echo 'checked'; ?> id="<?php echo $this->get_field_id(''); ?>"></input>
            <label for="showYearCount">Show Post Count in Year Links</label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('expandYears'); ?>" <?php if ($expandYears) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandYears'); ?>"></input>
            <label for="expandYears">Show Month Link</label>
        </p>
        <p>
            &nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->get_field_name('showMonthCount'); ?>" <?php if ($showMonthCount == 'yes') echo 'checked'; ?> id="<?php echo $this->get_field_id('showMonthCount'); ?>"></input>
            <label for="showMonthCount">Show Post Count in Month Links</label><br />
            &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->get_field_name('expandMonths'); ?>" <?php if ($expandMonths) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandMonths'); ?>"></input>
            <label for="expandMonths">Month Links should expand to show Posts</label><br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->get_field_name('expandCurrentMonth'); ?>" <? if ($expandCurrentMonth) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandCurrentMonth'); ?>"></input>
            <label for="expandCurrentMonth">Leave Current Month Expanded by Default</label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPostTitle'); ?>" <?php if ($showPostTitle) echo 'checked'; ?> id="<?php echo $this->get_field_id('showPostTitle'); ?>"></input>
            <label for="showPostTitle">Show Post Title</label>
            | Truncate Post Title to
            <input type="text" size='3' name="<?php echo $this->get_field_name('postTitleLength'); ?>" id="<?php echo $this->get_field_id('postTitleLength'); ?>" value="<?php echo $postTitleLength; ?>"></input>
            <label for="postTitleLength"> characters</label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPostDate'); ?>" <?php if ($showPostDate) echo 'checked'; ?> id="<?php echo $this->get_field_id('showPostDate'); ?>"></input>
            <label for="showPostDate">Show Post Date</label> |
            <select name="<?php echo $this->get_field_name('postDateAppend'); ?>">
                <option <?php if ($postDateAppend == 'before') echo 'selected'; ?> id="<?php echo $this->get_field_id('postDateAppendBefore') ?>" value='before'>Before post title</option>
                <option <?php if ($postDateAppend == 'after') echo 'selected'; ?> id="<?php echo $this->get_field_id('postDateAppendAfter') ?>" value='after'>After post title</option>
            </select>
            <label for="postDateFormat"><a href='http://php.net/date' title='information about date formatting syntax' target='_blank'>as</a>:</label>
            <input type="text" size='8' name="<?php echo $this->get_field_name('postDateFormat'); ?>" value="<?php echo $postDateFormat; ?>" id="<?php echo $this->get_field_id('postDateFormat'); ?>"></input>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('animate'); ?>" <?php if ($animate == 1) echo 'checked'; ?> id="<?php echo $this->get_field_id(''); ?>"></input>
            <label for="animate">Animate collapsing and expanding</label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('debug'); ?>" <?php if ($debug == '1') echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>"></input>
            <label for="collapsArchDebug">Show debugging information (shows up as a hidden pre right after the title)</label>
        </p>
        <?php
    } //end form
} //end class collapsArchWidget