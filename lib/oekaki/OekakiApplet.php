<?php
/*
* This file is part of Trevorchan.
*
* Trevorchan is free software; you can redistribute it and/or modify it under the
* terms of the GNU General Public License as published by the Free Software
* Foundation; either version 2 of the License, or (at your option) any later
* version.
*
* Trevorchan is distributed in the hope that it will be useful, but WITHOUT ANY
* WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
* A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* Trevorchan; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
* +------------------------------------------------------------------------------+
* OekakiApplet
* Version 1.0 Beta
* Created by sk89q
* http://www.therisenrealm.com
* http://www.keiichianimeforever.com
*
* Copyright (c) 2004, sk89q
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are
* met:
*
* Redistributions of source code must retain the above copyright notice,
* this list of conditions and the following disclaimer.
* 
* Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in the
* documentation and/or other materials provided with the distribution.
* 
* Neither the name of sk89q nor the names of its contributors may be
* used to endorse or promote products derived from this software
* without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
* A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
* OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
* SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
* LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
* DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
* THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
* OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
* +------------------------------------------------------------------------------+
*/
	
	class OekakiApplet
	{
		// Important to applet!
		var $applet_id                      = 'oekaki';
		
		// Applet display
		var $applet_width                   = 700;
		var $applet_height                  = 500;
		
		// Image display
		var $canvas_width                   = 300;
		var $canvas_height                  = 300;
		var $animation                      = FALSE;
		
		// Set to URL of image to load image
		var $load_image_url;
		var $load_animation_url;
		
		// Mostly Global
		var $applet_bg_color                = '#AEAED9';
		var $applet_bg_grid_color           = '#A3A3CC';
		var $applet_dialog_bg_color         = '#AEAED9';
		var $applet_dialog_text_color       = '#000000';
		var $applet_dialog_border_color     = '#000000';
		
		// Not so global
		var $applet_dialog_border_hl_color  = '#CCCCCC';
		var $applet_dialog_border_dl_color  = '#666666';
		var $applet_dialog_title_bg_color   = '#6666FF';
		var $applet_dialog_title_hl_color   = '#8888FF';
		var $applet_dialog_title_text_color = '#000000';
		
		// Saving
		var $url_save;
		var $url_finish;
		var $url_target = '_self';
		
		// Format to save
		var $default_format = 'png';
		
		function shipainter( $jar_path, $dir_resource, $pro_version = FALSE, $extra_params = array() )
		{
			$output = '';
			
			$base_params = array(
									'dir_resource' => $dir_resource,
									'tt.zip' => 'tt_def.zip',
									'res.zip' => 'res.zip',
									'MAYSCRIPT' => 'true',
									'scriptable' => 'true',
									'tools' => $pro_version ? 'pro' : '',
									'layer_count' => '5',
									'undo' => '90',
									'undo_in_mg' => '15',
									'image_width' => $this->canvas_width,
									'image_height' => $this->canvas_height,
									'image_canvas' => $this->load_image_url,
									'pch_file' => $this->load_animation_url,
									'color_bk' => $this->applet_bg_color,
									'color_bk2' => $this->applet_bg_grid_color,
									'color_text' => $this->applet_dialog_text_color,
									'window_color_bk' => $this->applet_dialog_bg_color,
									'window_color_text' => $this->applet_dialog_text_color,
									'window_color_frame' => $this->applet_dialog_border_color,
									'window_color_bar' => $this->applet_dialog_title_bg_color,
									'window_color_bar_hl' => $this->applet_dialog_title_hl_color,
									'window_color_bar_text' => $this->applet_dialog_title_text_color,
									'url_save' => html_entity_decode($this->url_save),
									'url_exit' => html_entity_decode($this->url_finish),
									'url_target' => $this->url_target,
									'poo' => 'false',
									'send_advance' => 'true',
									'send_language' => 'utf8',
									'send_header' => '',
									'send_header_image_type' => 'false',
									'thumbnail_type' => $this->animation ? 'animation' : '',
									'image_jpeg' => $this->default_format == 'jpg' ? 'true' : 'false',
									'image_size' => '92',
									'compress_level' => '4',
									);
									
			$params = array_merge( $base_params, $extra_params );
			
			$output .= '<applet id="' . $this->applet_id . '" code="c.ShiPainter.class" archive="' . $jar_path . '" width="' . $this->applet_width . '" height="' . $this->applet_height . '" mayscript="">';
			foreach( $params as $key => $value )
			{
				$output .= '<param name="' . htmlspecialchars( $key ) . '" value="' . $value . '" />' . "\n";
			}
			$output .= '</applet>';
			
			return $output;
		}
		
		function paintbbs( $jar_path, $dir_resource, $extra_params = array() )
		{
			$output = '';
			
			$base_params = array(
									'dir_resource' => $dir_resource,
									'undo' => '90',
									'undo_in_mg' => '15',
									'image_width' => $this->canvas_width,
									'image_height' => $this->canvas_height,
									'image_canvas' => $this->load_animation_url ? $this->load_animation_url : $this->load_image_url,
									'color_bk' => $this->applet_bg_color,
									'color_bk2' => $this->applet_bg_grid_color,
									'color_text' => $this->applet_dialog_text_color,
									'window_color_bk' => $this->applet_dialog_bg_color,
									'window_color_text' => $this->applet_dialog_text_color,
									'window_color_frame' => $this->applet_dialog_border_color,
									'window_color_bar' => $this->applet_dialog_title_bg_color,
									'window_color_bar_hl' => $this->applet_dialog_title_hl_color,
									'window_color_bar_text' => $this->applet_dialog_title_text_color,
									'url_save' => $this->url_save,
									'url_exit' => $this->url_finish,
									'url_target' => $this->url_target,
									'poo' => 'false',
									'send_advance' => 'true',
									'send_language' => 'utf8',
									'send_header' => '',
									'send_header_image_type' => 'false',
									'thumbnail_type' => $this->animation ? 'animation' : '',
									'image_jpeg' => $this->default_format == 'jpg' ? 'true' : 'false',
									'image_size' => '92',
									'compress_level' => '4',
									);
									
			$params = array_merge( $base_params, $extra_params );
			
			$output .= '<applet id="' . $this->applet_id . '" code="pbbs.PaintBBS.class" archive="' . $jar_path . '" width="' . $this->applet_width . '" height="' . $this->applet_height . '" mayscript="">';
			foreach( $params as $key => $value )
			{
				$output .= '<param name="' . htmlspecialchars( $key ) . '" value="' . $value . '" />' . "\n";
			}
			$output .= '</applet>';
			
			return $output;
		}
		
		function oekakibbs( $jar_path, $extra_params = array() )
		{
			$output = '';
			
			$base_params = array(
									'readfilepath' => './',
									'readpicpath' => './',
									'readanmpath' => './',
									'picw' => $this->canvas_width,
									'pich' => $this->canvas_height,
									'readpic' => $this->load_image_url,
									'readanm' => $this->load_animation_url,
									'backC' => str_replace( '#', '', $this->applet_bg_color ),
									'baseC' => str_replace( '#', '', $this->applet_dialog_bg_color ),
									'brightC' => str_replace( '#', '', $this->applet_dialog_border_hl_color ),
									'darkC' => str_replace( '#', '', $this->applet_dialog_border_dl_color ),
									'cgi' => $this->url_save,
									'url' => $this->url_finish,
									'target' => $this->url_target,
									'popup' => '0',
									'tooltype' => 'full',
									'anime' => $this->animation ? '1' : '0',
									'animesimple' => '1',
									'tooljpgpng' => '0',
									'tooljpg' => '1',
									'passwd' => '',
									'passwd2' => '',
									'mask' => '5',
									'toolpaintmode' => '1',
									'toolmask' => '1',
									'toollayer' => '1',
									'toolalpha' => '1',
									'toolwidth' => '200',
									'catalog' => '0',
									'catalogwidth' => '100',
									'catalogheight' => '100',
									);
									
			$params = array_merge( $base_params, $extra_params );
			
			$output .= '<applet id="' . $this->applet_id . '" code="a.p.class" archive="' . $jar_path . '" width="' . $this->applet_width . '" height="' . $this->applet_height . '" mayscript="">';
			foreach( $params as $key => $value )
			{
				$output .= '<param name="' . htmlspecialchars( $key ) . '" value="' . $value . '" />' . "\n";
			}
			$output .= '</applet>';
			
			return $output;
		}
	}
?>