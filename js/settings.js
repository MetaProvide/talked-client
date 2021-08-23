/**
 * Talked - Call recording for Nextcloud
 *
 * @copyright Copyright (C) 2021  Magnus Walbeck <mw@mwalbeck.org>
 *
 * @author Magnus Walbeck <mw@mwalbeck.org>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

function postSuccess(selector, id) {
    $(selector).after(
        " <span id='" + id + "' class='msg success'>" + t("talked", "Saved") + "</span>"
    );
    setTimeout(function () {
        $("#" + id).remove();
    }, 3000);
}

function postError(selector, id) {
    $(selector).after(
        " <span id='" + id + "' class='msg error'>" + t("talked", "Error") + "</span>"
    );
    setTimeout(function () {
        $("#" + id).remove();
    }, 3000);
}

window.addEventListener("DOMContentLoaded", function () {
    $("#talked-save-settings").click(function () {
        $.post(OC.generateUrl("apps/talked/settings/admin"), {
            server_url: $("#talked-server-url").val(),
            use_http_basic_auth: $("#talked-use-http-basic-auth").prop("checked") ? 1 : 0,
            http_basic_auth_username: $("#talked-http-basic-auth-username").val(),
            http_basic_auth_password: $("#talked-http-basic-auth-password").val(),
        })
            .done(function () {
                postSuccess("#talked-save-settings", "talked-save-settings-msg");
            })
            .fail(function () {
                postError("#talked-save-settings", "talked-save-settings-msg");
            });
    });
});
