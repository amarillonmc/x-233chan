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
 * Upload class
 * +------------------------------------------------------------------------------+
 * Used for image/misc file upload through the post form on board/thread pages
 * +------------------------------------------------------------------------------+
 */

class Upload {
	var $file_name			= '';
	var $original_file_name	= '';
	var $file_type			= '';
	var $file_md5			= '';
	var $file_location		= '';
	var $file_thumb_location = '';
	var $file_is_special	= false;
	var $imgWidth			= 0;
	var $imgHeight			= 0;
	var $file_size			= 0;
	var $imgWidth_thumb		= 0;
	var $imgHeight_thumb	= 0;
	var $isreply			= false;

	function HandleUpload() {
		global $tc_db, $board_class, $is_oekaki, $oekaki;

		if (!$is_oekaki) {
			if ($board_class->board['type'] == 0 || $board_class->board['type'] == 2 || $board_class->board['type'] == 3) {
				$imagefile_name = isset($_FILES['imagefile']) ? $_FILES['imagefile']['name'] : '';
				if ($imagefile_name != '') {
					if (strpos($_FILES['imagefile']['name'], ',') != false) {
						exitWithErrorPage(_gettext('Please select only one image to upload.'));
					}

					if ($_FILES['imagefile']['size'] > $board_class->board['maximagesize']) {
						exitWithErrorPage(sprintf(_gettext('Please make sure your file is smaller than %dB'), $board_class->board['maximagesize']));
					}

					switch ($_FILES['imagefile']['error']) {
					case UPLOAD_ERR_OK:
						break;
					case UPLOAD_ERR_INI_SIZE:
						exitWithErrorPage(sprintf(_gettext('The uploaded file exceeds the upload_max_filesize directive (%s) in php.ini.')), ini_get('upload_max_filesize'));
						break;
					case UPLOAD_ERR_FORM_SIZE:
						exitWithErrorPage(sprintf(_gettext('Please make sure your file is smaller than %dB'), $board_class->board['maximagesize']));
						break;
					case UPLOAD_ERR_PARTIAL:
						exitWithErrorPage(_gettext('The uploaded file was only partially uploaded.'));
						break;
					case UPLOAD_ERR_NO_FILE:
						exitWithErrorPage(_gettext('No file was uploaded.'));
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						exitWithErrorPage(_gettext('Missing a temporary folder.'));
						break;
					case UPLOAD_ERR_CANT_WRITE:
						exitWithErrorPage(_gettext('Failed to write file to disk'));
						break;
					default:
						exitWithErrorPage(_gettext('Unknown File Error'));
					}

					$this->file_type = preg_replace('/.*(\..+)/','\1',$_FILES['imagefile']['name']);
					if ($this->file_type == '.jpeg') {
						/* Fix for the rarely used 4-char format */
						$this->file_type = '.jpg';
					}

					$pass = true;
					if (!is_file($_FILES['imagefile']['tmp_name']) || !is_readable($_FILES['imagefile']['tmp_name'])) {
						$pass = false;
					} else {
						if ($this->file_type == '.jpg' || $this->file_type == '.gif' || $this->file_type == '.png') {
							if (!@getimagesize($_FILES['imagefile']['tmp_name'])) {
								$pass = false;
							}
						}
					}
					if (!$pass) {
						exitWithErrorPage(_gettext('File transfer failure. Please go back and try again.'));
					}

					$this->file_name = substr(htmlspecialchars(preg_replace('/(.*)\..+/','\1',$_FILES['imagefile']['name']), ENT_QUOTES), 0, 50);
					$this->file_name = str_replace('.','_',$this->file_name);
					$this->original_file_name = $this->file_name;
					$this->file_md5 = md5_file($_FILES['imagefile']['tmp_name']);

					$exists_thread = checkMd5($this->file_md5, $board_class->board['name'], $board_class->board['id']);
					if (is_array($exists_thread)) {
						exitWithErrorPage(_gettext('Duplicate file entry detected.'), sprintf(_gettext('Already posted %shere%s.'),'<a href="' . KU_BOARDSPATH . '/' . $board_class->board['name'] . '/res/' . $exists_thread[0] . '.html#' . $exists_thread[1] . '">','</a>'));
					}

					if (strtolower($this->file_type) == 'svg') {
						require_once 'svg.class.php';
						$svg = new Svg($_FILES['imagefile']['tmp_name']);
						$this->imgWidth = $svg->width;
						$this->imgHeight = $svg->height;
					} else {
						$imageDim = getimagesize($_FILES['imagefile']['tmp_name']);
						$this->imgWidth = $imageDim[0];
						$this->imgHeight = $imageDim[1];
					}

					$this->file_type = strtolower($this->file_type);
					$this->file_size = $_FILES['imagefile']['size'];

					$filetype_forcethumb = $tc_db->GetOne("SELECT " . KU_DBPREFIX . "filetypes.force_thumb FROM " . KU_DBPREFIX . "boards, " . KU_DBPREFIX . "filetypes, " . KU_DBPREFIX . "board_filetypes WHERE " . KU_DBPREFIX . "boards.id = " . KU_DBPREFIX . "board_filetypes.boardid AND " . KU_DBPREFIX . "filetypes.id = " . KU_DBPREFIX . "board_filetypes.typeid AND " . KU_DBPREFIX . "boards.name = '" . $board_class->board['name'] . "' and " . KU_DBPREFIX . "filetypes.filetype = '" . substr($this->file_type, 1) . "';");
					if ($filetype_forcethumb != '') {
						if ($filetype_forcethumb == 0) {
							$this->file_name = time() . mt_rand(1, 99);

							/* If this board has a load balance url and password configured for it, attempt to use it */
							if ($board_class->board['loadbalanceurl'] != '' && $board_class->board['loadbalancepassword'] != '') {
								require_once KU_ROOTDIR . 'inc/classes/loadbalancer.class.php';
								$loadbalancer = new Load_Balancer;

								$loadbalancer->url = $board_class->board['loadbalanceurl'];
								$loadbalancer->password = $board_class->board['loadbalancepassword'];

								$response = $loadbalancer->Send('thumbnail', base64_encode(file_get_contents($_FILES['imagefile']['tmp_name'])), 'src/' . $this->file_name . $this->file_type, 'thumb/' . $this->file_name . 's' . $this->file_type, 'thumb/' . $this->file_name . 'c' . $this->file_type, '', $this->isreply, true);

								if ($response != 'failure' && $response != '') {
									$response_unserialized = unserialize($response);

									$this->imgWidth_thumb = $response_unserialized['imgw_thumb'];
									$this->imgHeight_thumb = $response_unserialized['imgh_thumb'];

									$imageused = true;
								} else {
									exitWithErrorPage(_gettext('File was not properly thumbnailed').': ' . $response);
								}
							/* Otherwise, use this script alone */
							} else {
								$this->file_location = KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $this->file_name . $this->file_type;
								$this->file_thumb_location = KU_BOARDSDIR . $board_class->board['name'] . '/thumb/' . $this->file_name . 's' . $this->file_type;
								$this->file_thumb_cat_location = KU_BOARDSDIR . $board_class->board['name'] . '/thumb/' . $this->file_name . 'c' . $this->file_type;

								if (!move_uploaded_file($_FILES['imagefile']['tmp_name'], $this->file_location)) {
									exitWithErrorPage(_gettext('Could not copy uploaded image.'));
								}
								chmod($this->file_location, 0644);

								if ($_FILES['imagefile']['size'] == filesize($this->file_location)) {
									if ((!$this->isreply && ($this->imgWidth > KU_THUMBWIDTH || $this->imgHeight > KU_THUMBHEIGHT)) || ($this->isreply && ($this->imgWidth > KU_REPLYTHUMBWIDTH || $this->imgHeight > KU_REPLYTHUMBHEIGHT))) {
										if (!$this->isreply) {
											if (!createThumbnail($this->file_location, $this->file_thumb_location, KU_THUMBWIDTH, KU_THUMBHEIGHT)) {
												exitWithErrorPage(_gettext('Could not create thumbnail.'));
											}
										} else {
											if (!createThumbnail($this->file_location, $this->file_thumb_location, KU_REPLYTHUMBWIDTH, KU_REPLYTHUMBHEIGHT)) {
												exitWithErrorPage(_gettext('Could not create thumbnail.'));
											}
										}
									} else {
										if (!createThumbnail($this->file_location, $this->file_thumb_location, $this->imgWidth, $this->imgHeight)) {
											exitWithErrorPage(_gettext('Could not create thumbnail.'));
										}
									}
									if (!createThumbnail($this->file_location, $this->file_thumb_cat_location, KU_CATTHUMBWIDTH, KU_CATTHUMBHEIGHT)) {
										exitWithErrorPage(_gettext('Could not create thumbnail.'));
									}
									$imageDim_thumb = getimagesize($this->file_thumb_location);
									$this->imgWidth_thumb = $imageDim_thumb[0];
									$this->imgHeight_thumb = $imageDim_thumb[1];
									$imageused = true;
								} else {
									exitWithErrorPage(_gettext('File was not fully uploaded. Please go back and try again.'));
								}
							}
						} else {
							/* Fetch the mime requirement for this special filetype */
							$filetype_required_mime = $tc_db->GetOne("SELECT `mime` FROM `" . KU_DBPREFIX . "filetypes` WHERE `filetype` = " . $tc_db->qstr(substr($this->file_type, 1)));

							$this->file_name = htmlspecialchars_decode($this->file_name, ENT_QUOTES);
							$this->file_name = stripslashes($this->file_name);
							$this->file_name = str_replace("\x80", " ", $this->file_name);					
							$this->file_name = str_replace(' ', '_', $this->file_name);
							$this->file_name = str_replace('#', '(number)', $this->file_name);
							$this->file_name = str_replace('@', '(at)', $this->file_name);
							$this->file_name = str_replace('/', '(fwslash)', $this->file_name);
							$this->file_name = str_replace('\\', '(bkslash)', $this->file_name);

							/* If this board has a load balance url and password configured for it, attempt to use it */
							if ($board_class->board['loadbalanceurl'] != '' && $board_class->board['loadbalancepassword'] != '') {
								require_once KU_ROOTDIR . 'inc/classes/loadbalancer.class.php';
								$loadbalancer = new Load_Balancer;

								$loadbalancer->url = $board_class->board['loadbalanceurl'];
								$loadbalancer->password = $board_class->board['loadbalancepassword'];

								if ($filetype_required_mime != '') {
									$checkmime = $filetype_required_mime;
								} else {
									$checkmime = '';
								}

								$response = $loadbalancer->Send('direct', $_FILES['imagefile']['tmp_name'], 'src/' . $this->file_name . $this->file_type, '', '', $checkmime, false, true);

								$this->file_is_special = true;
							/* Otherwise, use this script alone */
							} else {
								$this->file_location = KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $this->file_name . $this->file_type;
								
								if (file_exists($this->file_location)) {
									exitWithErrorPage(_gettext('A file by that name already exists'));
									die();
								}
								
								if($this->file_type == '.mp3') {
									require_once(KU_ROOTDIR . 'lib/getid3/getid3.php');

									$getID3 = new getID3;
									$getID3->analyze($_FILES['imagefile']['tmp_name']);
									if (isset($getID3->info['id3v2']['APIC'][0]['data']) && isset($getID3->info['id3v2']['APIC'][0]['image_mime'])) {
										$source_data = $getID3->info['id3v2']['APIC'][0]['data'];
										$mime = $getID3->info['id3v2']['APIC'][0]['image_mime'];
									}
									elseif (isset($getID3->info['id3v2']['PIC'][0]['data']) && isset($getID3->info['id3v2']['PIC'][0]['image_mime'])) {
										$source_data = $getID3->info['id3v2']['PIC'][0]['data'];
										$mime = $getID3->info['id3v2']['PIC'][0]['image_mime'];
									}

									if($source_data) {
										$im = imagecreatefromstring($source_data);
										if (preg_match("/png/", $mime)) {
											$ext = ".png";
											imagepng($im,$this->file_location.".tmp",0,PNG_ALL_FILTERS);
										} else if (preg_match("/jpg|jpeg/", $mime)) {
											$ext = ".jpg";
											imagejpeg($im, $this->file_location.".tmp");
										} else if (preg_match("/gif/", $mime)) {
											$ext = ".gif";
											imagegif($im, $this->file_location.".tmp");
										}
										$this->file_thumb_location = KU_BOARDSDIR . $board_class->board['name'] . '/thumb/' . $this->file_name .'s'. $ext;
										if (!$this->isreply) {
											if (!createThumbnail($this->file_location.".tmp", $this->file_thumb_location, KU_THUMBWIDTH, KU_THUMBHEIGHT)) {
												exitWithErrorPage(_gettext('Could not create thumbnail.'));
											}
										} else {
											if (!createThumbnail($this->file_location.".tmp", $this->file_thumb_location, KU_REPLYTHUMBWIDTH, KU_REPLYTHUMBHEIGHT)) {
												exitWithErrorPage(_gettext('Could not create thumbnail.'));
											}
										}
										$imageDim_thumb = getimagesize($this->file_thumb_location);
										$this->imgWidth_thumb = $imageDim_thumb[0];
										$this->imgHeight_thumb = $imageDim_thumb[1];
										$imageused = true;
										unlink($this->file_location.".tmp");
									}


								}

								/* Move the file from the post data to the server */
								if (!move_uploaded_file($_FILES['imagefile']['tmp_name'], $this->file_location)) {
									exitWithErrorPage(_gettext('Could not copy uploaded image.'));
								}

								/* Check if the filetype provided comes with a MIME restriction */
								if ($filetype_required_mime != '') {
									/* Check if the MIMEs don't match up */
									if (mime_content_type($this->file_location) != $filetype_required_mime) {
										/* Delete the file we just uploaded and kill the script */
										unlink($this->file_location);
										exitWithErrorPage(_gettext('Invalid MIME type for this filetype.'));
									}
								}

								/* Make sure the entire file was uploaded */
								if ($_FILES['imagefile']['size'] == filesize($this->file_location)) {
									$imageused = true;
								} else {
									exitWithErrorPage(_gettext('File transfer failure. Please go back and try again.'));
								}

								/* Flag that the file used isn't an internally supported type */
								$this->file_is_special = true;
							}
						}
					} else {
						exitWithErrorPage(_gettext('Sorry, that filetype is not allowed on this board.'));
					}
				} elseif (isset($_POST['embed'])) {
					if ($_POST['embed'] != '') {
						$_POST['embed'] = strip_tags(substr($_POST['embed'], 0, 20));
						$video_id = $_POST['embed'];
						$this->file_name = $video_id;

						if ($video_id != '' && strpos($video_id, '@') == false && strpos($video_id, '&') == false) {

							$embeds = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "embeds`");
							$worked = false;

							foreach ($embeds as $line) {
								if ((strtolower($_POST['embedtype']) == strtolower($line['name'])) && in_array($line['filetype'], explode(',', $board_class->board['embeds_allowed']))) {
									$worked = true;
									$videourl_start = $line['videourl'];
									$this->file_type = '.' . strtolower($line['filetype']);
								}
							}

							if (!$worked) {
								exitWithErrorPage(_gettext('Invalid video type.'));
							}

							$results = $tc_db->GetOne("SELECT COUNT(*) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $board_class->board['id'] . " AND `file` = " . $tc_db->qstr($video_id) . " AND `IS_DELETED` = 0");
							if ($results[0] == 0) {
								$video_check = check_link($videourl_start . $video_id);
								switch ($video_check[1]) {
									case 404:
										exitWithErrorPage(_gettext('Unable to connect to') .': '. $videourl_start . $video_id);
										break;
									case 303:
										exitWithErrorPage(_gettext('Invalid video ID.'));
										break;
									case 302:
										// Continue
										break;
									case 301:
										// Continue
										break;
									case 200:
										// Continue
										break;
									default:
										exitWithErrorPage(_gettext('Invalid response code ') .':'. $video_check[1]);
										break;
								}
							} else {
								$results = $tc_db->GetAll("SELECT `id`,`parentid` FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $board_class->board['id'] . " AND `file` = " . $tc_db->qstr($video_id) . " AND `IS_DELETED` = 0 LIMIT 1");
								foreach ($results as $line) {
									$real_threadid = ($line[1] == 0) ? $line[0] : $line[1];
									exitWithErrorPage(sprintf(_gettext('That video ID has already been posted %shere%s.'),'<a href="' . KU_BOARDSFOLDER . '/' . $board_class->board['name'] . '/res/' . $real_threadid . '.html#' . $line[1] . '">','</a>'));
								}
							}
						} else {
							exitWithErrorPage(_gettext('Invalid ID'));
						}
					}
				}
			}
		} else {
			$this->file_name = time() . mt_rand(1, 99);
			$this->original_file_name = $this->file_name;
			$this->file_md5 = md5_file($oekaki);
			$this->file_type = '.png';
			$this->file_size = filesize($oekaki);
			$imageDim = getimagesize($oekaki);
			$this->imgWidth = $imageDim[0];
			$this->imgHeight = $imageDim[1];

			if (!copy($oekaki, KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $this->file_name . $this->file_type)) {
				exitWithErrorPage(_gettext('Could not copy uploaded image.'));
			}

			$oekaki_animation = substr($oekaki, 0, -4) . '.pch';
			if (file_exists($oekaki_animation)) {
				if (!copy($oekaki_animation, KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $this->file_name . '.pch')) {
					exitWithErrorPage(_gettext('Could not copy animation.'));
				}
				unlink($oekaki_animation);
			}

			$thumbpath = KU_BOARDSDIR . $board_class->board['name'] . '/thumb/' . $this->file_name . 's' . $this->file_type;
			$thumbpath_cat = KU_BOARDSDIR . $board_class->board['name'] . '/thumb/' . $this->file_name . 'c' . $this->file_type;
			if (
				(!$this->isreply && ($this->imgWidth > KU_THUMBWIDTH || $this->imgHeight > KU_THUMBHEIGHT)) ||
				($this->isreply && ($this->imgWidth > KU_REPLYTHUMBWIDTH || $this->imgHeight > KU_REPLYTHUMBHEIGHT))
			) {
				if (!$this->isreply) {
					if (!createThumbnail($oekaki, $thumbpath, KU_THUMBWIDTH, KU_THUMBHEIGHT)) {
						exitWithErrorPage(_gettext('Could not create thumbnail.'));
					}
				} else {
					if (!createThumbnail($oekaki, $thumbpath, KU_REPLYTHUMBWIDTH, KU_REPLYTHUMBHEIGHT)) {
						exitWithErrorPage(_gettext('Could not create thumbnail.'));
					}
				}
			} else {
				if (!createThumbnail($oekaki, $thumbpath, $this->imgWidth, $this->imgHeight)) {
					exitWithErrorPage(_gettext('Could not create thumbnail.'));
				}
			}
			if (!createThumbnail($oekaki, $thumbpath_cat, KU_CATTHUMBWIDTH, KU_CATTHUMBHEIGHT)) {
				exitWithErrorPage(_gettext('Could not create thumbnail.'));
			}

			$imgDim_thumb = getimagesize($thumbpath);
			$this->imgWidth_thumb = $imgDim_thumb[0];
			$this->imgHeight_thumb = $imgDim_thumb[1];
			unlink($oekaki);
		}
	}
}
?>
