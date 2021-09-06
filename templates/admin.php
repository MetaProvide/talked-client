<?php
/**
 * Talked - Call recording for Nextcloud Talk
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

style('talked', 'settings');
script('talked', 'settings');
?>

<div id="talked" class="talked-admin section <?php p($hideSettings ? "hidden" : ""); ?>">
    <h2><?php p($l->t("Talked")); ?></h2>
    <h3><?php p($l->t("Talked server URL")); ?></h3>
    <input type="text" class="" id="talked-server-url" placeholder="https://talked.example.com" value="<?php p($serverUrl); ?>">

    <input type="checkbox" class="checkbox" id="talked-use-http-basic-auth" <?php p($useHttpBasicAuth ? "checked" : ""); ?>>
    <label for="talked-use-http-basic-auth"><?php p($l->t("Use HTTP Basic auth for the Talked server")); ?></label>

    <h3><?php p($l->t("HTTP Basic username")); ?></h3>
    <input type="text" class="" id="talked-http-basic-auth-username" value="<?php p($httpBasicAuthUsername); ?>">

    <h3><?php p($l->t("HTTP Basic password")); ?></h3>
    <input type="password" class="" id="talked-http-basic-auth-password" value="<?php p($httpBasicAuthPassword); ?>">

    <button id="talked-save-settings"><?php p($l->t("Save")); ?></button>
</div>
