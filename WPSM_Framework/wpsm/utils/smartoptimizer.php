<?php
function wpsm_htaccess_optimization($rules) {
    $smart_optimizer = <<<EOD
\n# BEGIN Smart Optimizer Code
<IfModule mod_expires.c>
    <FilesMatch "\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico)$">
        ExpiresActive On
        ExpiresDefault "access plus 10 years"
    </FilesMatch>
</IfModule>
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !^.*wp-admin.*$
EOD;
    $smart_optimizer .= "\n" . '    RewriteRule ^(.*\.(js|css))$ ' . WPSM_URI_RELATIVE . '/utils/smartoptimizer/?$1' . "\n";
    $smart_optimizer .= <<<EOD

    <IfModule mod_expires.c>
        RewriteCond %{REQUEST_FILENAME} -f
        RewriteCond %{REQUEST_URI} !^.*wp-admin.*$
EOD;

    $smart_optimizer .= "\n" . '        RewriteRule ^(.*\.(js|css|html?|xml|txt))$ ' . WPSM_URI_RELATIVE . '/utils/smartoptimizer/?$1' . "\n";
    $smart_optimizer .= <<<EOD
    </IfModule>

    <IfModule !mod_expires.c>
        RewriteCond %{REQUEST_FILENAME} -f
        RewriteCond %{REQUEST_URI} !^.*wp-admin.*$
EOD;

    $smart_optimizer .= "\n" . '        RewriteRule ^(.*\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico))$ ' . WPSM_URI_RELATIVE . '/utils/smartoptimizer/?$1' . "\n";
    $smart_optimizer .= <<<EOD
    </IfModule>
</IfModule>
<FilesMatch "\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico)$">
    FileETag none
</FilesMatch>
# END Smart Optimizer Code\n
EOD;

    return $rules . $smart_optimizer;
} //end wpsm_htaccess_optimization

add_filter('mod_rewrite_rules', 'wpsm_htaccess_optimization');