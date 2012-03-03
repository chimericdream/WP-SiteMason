<div class="wrap">
    <h3>Add a New Feed</h3>
    <a href="<?php echo admin_url('admin.php?page=theme-plugins-gcal&action=add'); ?>" class="button-secondary" title="Click here to add a new feed">Add Feed</a>
    <br /><br />
    <h3>Current Feeds</h3>
    <?php
    //Get saved feed options
    $options = get_option(GCE_OPTIONS_NAME);
    //If there are no saved feeds
    if (empty($options)) :
        ?>
        <p>You haven't added any Google Calendar feeds yet.</p>
        <?php //If there are saved feeds, display them ?>
    <?php else : ?>
        <table class="widefat">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">URL</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">URL</th>
                    <th scope="col"></th>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($options as $key => $event) : ?>
                    <tr>
                        <td><?php echo $key; ?></td>
                        <td><?php echo $event['title']; ?></td>
                        <td><?php echo $event['url']; ?></td>
                        <td align="right">
                            <a href="<?php echo admin_url('admin.php?page=theme-plugins-gcal&action=refresh&id=' . $key); ?>">Refresh</a>&nbsp;|&nbsp;<a href="<?php echo admin_url('admin.php?page=theme-plugins-gcal&action=edit&id=' . $key); ?>">Edit</a>&nbsp;|&nbsp;<a href="<?php echo admin_url('admin.php?page=theme-plugins-gcal&action=delete&id=' . $key); ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
    endif;
    //Get saved general options
    $options = get_option(GCE_GENERAL_OPTIONS_NAME);
    ?>
    <br />
    <h3>General Options</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Custom stylesheet URL</th>
            <td>
                <span class="description">If you want to alter the default plugin styling, create a new stylesheet on your server (not in the <code>google-calendar-events</code> directory) and then enter its URL below.</span>
                <br />
                <input type="text" name="gce_general[stylesheet]" value="<?php echo $options['stylesheet']; ?>" size="100" />
            </td>
        </tr><tr>
            <th scope="row">Add JavaScript to footer?</th>
            <td>
                <span class="description">If you are having issues with tooltips not appearing or the AJAX functionality not working, try ticking the checkbox below.</span>
                <br />
                <input type="checkbox" name="gce_general[javascript]"<?php checked($options['javascript'], true); ?> value="on" />
            </td>
        </tr><tr>
            <th scope="row">Loading text</th>
            <td>
                <span class="description">Text to display while calendar data is loading (on AJAX requests).</span>
                <br />
                <input type="text" name="gce_general[loading]" value="<?php echo $options['loading']; ?>" />
            </td>
        </tr><tr>
            <th scope="row">Error message</th>
            <td>
                <span class="description">An error message to display to non-admin users if events cannot be displayed for any reason (admins will see a message indicating the cause of the problem).</span>
                <br />
                <input type="text" name="gce_general[error]" value="<?php echo $options['error']; ?>" size="100" />
            </td>
        </tr><tr>
            <th scope="row">Optimize event retrieval?</th>
            <td>
                <span class="description">If this option is enabled, the plugin will use an experimental feature of the Google Data API, which can improve performance significantly, especially with large numbers of events. Google could potentially remove / change this feature at any time.</span>
                <br />
                <input type="checkbox" name="gce_general[fields]"<?php checked($options['fields'], true); ?> value="on" />
            </td>
        </tr><tr>
            <th scope="row">Use old styles?</th>
            <td>
                <span class="description">Some CSS changes were made in version 0.7. If this option is enabled, the old CSS will still be added along with the main stylesheet. You should consider updating your stylesheet so that you don't need this enabled.</span>
                <br />
                <input type="checkbox" name="gce_general[old_stylesheet]"<?php checked($options['old_stylesheet'], true); ?> value="on" />
            </td>
        </tr>
    </table>
    <br />
    <input type="submit" class="button-primary" value="Save" />
</div>