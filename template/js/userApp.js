/**
 * Main user js file
 *
 *
 * @package    ad-web-app
 * @author     Original Author <justin.inw@hotmail.com>
 * @copyright  BZTF
 * @license    http =>//creativecommons.org/licenses/by-nc-sa/3.0/
 */

$(function(){
    /*
    * Check if user is logged in if not redirect him to login page.
    * Needed for browser history.
    */
    $.ajax({
        url : '/app/session_check.php',
        dataType : 'JSON',
        method : 'POST',

        success: function(resp) {
            if (resp == 'false') {
                window.location.href = '/';
            }
        },
    });

    $('.back-button').on('click', goBack);

    $('[name="changePw"]').on('click', togglePw);

    // Go back in history state
    function goBack() {
        window.history.back();
    }

    // Toggles display and required of password fields based on checkbox
    function togglePw() {
        $('.user-pw').toggle();

        if ($(this).is(':checked')) {
            $('[type=password]').prop('required', true);
        } else {
            $('[type=password]').prop('required', false);
        }
    }

});
