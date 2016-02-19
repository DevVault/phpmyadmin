<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Logging functionality for webserver.
 *
 * This includes web server specific code to log some information.
 *
 * @package PhpMyAdmin
 */

/**
 * Logs user information to webserver logs.
 *
 * @param string $user   user name
 * @param string $status status message
 *
 * @return void
 */
function PMA_logUser($user, $status = 'ok')
{
    if (function_exists('apache_note')) {
        apache_note('userID', $user);
        apache_note('userStatus', $status);
    }
    if (function_exists('syslog') && $status != 'ok') {
        @openlog('phpMyAdmin', LOG_NDELAY | LOG_PID, LOG_AUTHPRIV);
        @syslog(
            LOG_WARNING,
            'user denied: ' . $user . ' (' . $status . ') from ' .
            $_SERVER['REMOTE_ADDR']
        );
    }
}

