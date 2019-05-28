<?php
/*
 * This file is part of kusaba.
 *
 * kusaba is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * kusaba is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * kusaba; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 * +------------------------------------------------------------------------------+
 * Load balancer class
 * +------------------------------------------------------------------------------+
 */
class Load_Balancer {
	var $url;
	var $password;

	function Send($type, $file, $targetname, $targetthumb = '', $targetthumb_c = '', $checkmime = '', $isreply = false, $handle_errors = false) {
		$isreply_formatted = ($isreply) ? '1' : '0';

		$ch = curl_init($this->url);

		$post = array('password' => $this->password,
				'type' => $type,
				'isreply' => $isreply_formatted,
				'file' => $file,
				'targetname' => $targetname,
				'targetthumb' => $targetthumb,
				'targetthumb_c' => $targetthumb_c,
				'checkmime' => $checkmime);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$return = curl_exec($ch);

		curl_close($ch);

		if ($handle_errors) {
			if ($return == 'bad password') die(_gettext('The passwords of the load balancer password in the board configuration and the load receiver script differ.'));
			if ($return == 'unable to thumbnail') die(_gettext('The load balancer script was unable to create the thumbnail for that image.'));
			if ($return == 'file already exists') die(_gettext('That file already exists on the server.'));
			if ($return == 'unable to copy') die(_gettext('The load balancer script was unable to copy the file you uploaded.'));
			if ($return == 'bad mime type') die(_gettext('That file does not match up with the required mime type for that format.'));
			if ($return == '' || $return == 'failure') die(_gettext('The load balancer script stopped unexpectedly.'));
		}

		return $return;
	}

	function Delete($filename, $filetype) {
		$ch = curl_init($this->url);

		$post = array('password' => $this->password,
				'type' => 'delete',
				'filename' => $filename,
				'filetype' => $filetype);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$return = curl_exec($ch);

		curl_close($ch);

		return $return;
	}
}

?>