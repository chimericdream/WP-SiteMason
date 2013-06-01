<?php

function wpsm_item_page($global_item_list, $item_name) {
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        foreach ($global_item_list as $i) {
            $value = ($_POST[$i['field_name']] == 'true') ? true : false;
            update_option($i['option_name'], $value);
        }

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
    <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These <?php echo $item_name; ?> are theme specific and will only apply when the current theme (<strong><?php echo WPSM_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Available <?php echo $item_name; ?></h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <?php
                foreach ($global_item_list as $i) {
                    $title       = $i['title'];
                    $field_name  = $i['field_name'];
                    $note        = $i['note'];
                    $option_name = $i['option_name'];
                    wpsm_item_option_html($title, $field_name, $note, $option_name, $global_item_list);
                }
                ?>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->
        </form>
    </div>
    <div style="clear:both;height:20px;"></div>
    <?php
} //end wpsm_item_page

function wpsm_item_option_html($title, $field_name, $note, $option_name, $global_item_list)
{
    ?>
                <div class="option">
                    <label for="<?php echo $field_name; ?>_yes"><?php echo $title; ?></label>
                    <div class="description">
                        <p><?php echo $note; ?></p>
                        <?php if (!empty($global_item_list[$option_name]['depends'])) : ?>
                        <h5>Dependencies:</h5>
                        <ul>
                        <?php foreach ($global_item_list[$option_name]['depends'] as $d) : ?>
                            <li>
                                <?php echo $global_item_list[$d]['title']; ?>:
                                <?php if (check_wpsm_item($d)) : ?>
                                <span class="success">Active</span>
                                <?php else : ?>
                                <span class="error">Inactive</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <fieldset>
                        <label for="<?php echo $field_name; ?>_yes"><input type="radio" value="true" id="<?php echo $field_name; ?>_yes" name="<?php echo $field_name; ?>"<?php
    if ((bool) get_option($option_name) == true) {
        echo ' checked="checked"';
    }
    ?> /> Yes</label><br />
                        <label for="<?php echo $field_name; ?>_no"><input type="radio" value="false" id="<?php echo $field_name; ?>_no" name="<?php echo $field_name; ?>"<?php
    if ((bool) get_option($option_name) == false) {
        echo ' checked="checked"';
    }
    ?> /> No</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>
    <?php
} //end wpsm_item_option_html