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
	
	class OekakiInput
	{
		function autodetermine( $data )
		{
			switch( substr( $data, 0, 1 ) )
			{
				case 'S':
					return 'shipainter';
				case 'P':
					return 'paintbbs';
				default:
					return 'oekakibbs';
			}
		}
		
		function autoprocess( $applet, $data, &$anim_ext, &$print_ok, &$print_error_prefix, &$response_mimetype, &$error )
		{
			$response_mimetype = 'text/plain';
			switch( strtolower( $applet ) )
			{
				case 'shi':
				case 'shipro':
				case 'shipaint':
				case 'shipaintpro':
				case 'shipainter':
				case 'shipainterpro':
				{
					$anim_ext = 'pch';
					$print_ok = TRUE;
					return $this->shipainter( $data, $error );
					break;
				}
				case 'paint':
				case 'paintbbs':
				{
					$anim_ext = 'pch';
					$print_ok = TRUE;
					$print_error_prefix = TRUE;
					return $this->paintbbs( $data, $error );
					break;
				}
				case 'oekaki':
				case 'oekakibbs':
				{
					$anim_ext = 'oeb';
					$print_ok = FALSE;
					$print_error_prefix = FALSE;
					return $this->oekakibbs( $data, $error );
					break;
				}
				default:
				{
					$error = 'INVALID_APPLET';
					return FALSE;
				}
			}
		}
		
		function shipainter( $data, &$error )
		{
			// Same data format
			return $this->_shibbs( $data, $error, 'S' );
		}
		
		function paintbbs( $data, &$error )
		{
			// Same data format
			return $this->_shibbs( $data, $error, 'P' );
		}
		
		function _shibbs( $data, &$error, $data_string )
		{
			$error = '';
			
			$header = array(
							'length'    => 0,
							'data'      => array(),
							);
			$image = array(
							'length'    => 0,
							'data'      => '',
							);
			$animation = array(
							'length'    => 0,
							'data'      => '',
							);
			$thumbnail = array(
							'length'    => 0,
							'data'      => '',
							);
							
			$save_id = basename( $_GET['saveid'] );
		
			do
			{
				//-------------------------------------
				// SOME CHECKING
				//-------------------------------------
				
				if( empty( $data ) )
				{
					$error = 'NO_IMAGE_DATA';
					break;
				}
				
				if( substr( $data, 0, 1 ) != $data_string )
				{
					$error = 'INVALID_DATA';
					break;
				}
				
				//-------------------------------------
				// PARSE HEADER
				//-------------------------------------
				
				$header['length']   = intval( substr( $data, 1, 8 ) );
				
				$tmp_data['raw']    = substr( $data, 9, $header['length'] );
				$tmp_data['lines']  = explode( ';', $tmp_data['raw'] );
		
				foreach( $tmp_data['lines'] as $line )
				{
					$line = explode( '=', $line, 2 );
					if( $line[0] )
					{
						$header['data'][] = $line[1];
					}
				}
				
				unset( $tmp_data );
				
				//-------------------------------------
				// PARSE IMAGE DATA
				// Just gets the data after the header
				//-------------------------------------
				
				$image['length']        = intval( substr( $data, $header['length'] + 9, 8 ) );
				$image['data']          = substr( $data, $header['length'] + 19, $image['length'] );
				
				//-------------------------------------
				// PARSE ANIMATION DATA
				// Just gets the data after the header and image
				//-------------------------------------
				
				$animation['length']    = intval( substr( $data, $header['length'] + $image['length'] + 19, 8 ) );
				$animation['data']      = substr( $data, $header['length'] + $image['length'] + 27, $animation['length'] );
				
				//-------------------------------------
				// PARSE THUMBNAIL DATA
				// Just gets the data after the header, image and animation
				//-------------------------------------
				
				$thumbnail['length']    = intval( substr( $data, $header['length'] + $image['length'] + $animation['length'] + 27, 8 ) );
				$thumbnail['data']      = substr( $data, $header['length'] + $image['length'] + $animation['length'] + 35, $thumbnail['length'] );
				
				//-------------------------------------
				// EVEN MORE CHECKING
				//-------------------------------------
				
				if( empty( $image['data'] ) )
				{
					$error = 'NO_IMAGE_DATA';
					break;
				}
			}
			while( FALSE );
			
			if( $error )
			{
				return FALSE;
			}
			else
			{
				return array(
								'IMAGE'     => $image['data'],
								'ANIMATION' => $animation['data'],
								'THUMBNAIL' => $thumbnail['data'],
								);
			}
		}
		
		function oekakibbs( $data, &$error )
		{
			$error = '';
			
			$header = array(
							'length'    => 0,
							'data'      => array(),
							);
			$image = array(
							'length'    => 0,
							'data'      => '',
							);
			$animation = array(
							'length'    => 0,
							'data'      => '',
							);
							
			$save_id = basename( $_GET['saveid'] );
		
			do
			{
				//-------------------------------------
				// SOME CHECKING
				//-------------------------------------
				
				if( empty( $data ) )
				{
					$error = 'NO_IMAGE_DATA';
					break;
				}
				
				//-------------------------------------
				// PARSE DATA
				//-------------------------------------
				
				$start_offset = 0;
				$end_offset = TRUE;
				
				while( $end_offset )
				{                    
					// This is how it goes:
					// 1. Seek to next Content-type
					// 2. Read content type
					// 3. Seek to end of header for this data block
					// 4. Read data
					// 5. Stop at next Content-type or EOF
					
					$start_offset = strpos( $data, 'Content-type:', $start_offset ) + strlen( 'Content-type:' );
					$content_type = substr( $data, $start_offset, strpos( $data, "\r", $start_offset ) - $start_offset );
					$start_offset += strlen( $content_type ) + 4;
				
					//-------------------------------------
					// READ DATA
					//-------------------------------------
					
					if( FALSE === ( $end_offset = strpos( $data, 'Content-type:', $start_offset ) ) )
					{
						$read_data = substr( $data, $start_offset );
					}
					else
					{
						$read_data = substr( $data, $start_offset, $end_offset - $start_offset );
					}
				
					//-------------------------------------
					// PARSE DATA
					//-------------------------------------
				
					if( $content_type == 'image/0' || $content_type == 'image/1' )
					{
						$image['data'] = $read_data;
					}
					elseif( $content_type == 'animation' || $content_type == 'animation' )
					{
						$animation['data'] = $read_data;
					}
					
					$start_offset = $end_offset;
				}
				
				//-------------------------------------
				// EVEN MORE CHECKING
				//-------------------------------------
				
				if( empty( $image['data'] ) )
				{
					$error = 'NO_IMAGE_DATA';
					break;
				}
			}
			while( FALSE );
			
			if( $error )
			{
				return FALSE;
			}
			else
			{
				return array(
								'IMAGE'     => $image['data'],
								'ANIMATION' => $animation['data'],
								);
			}
		}
	}
?>