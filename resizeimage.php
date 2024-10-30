<?php
	define("HAR_AUTO_NAME",1);	
	class ResizeImage
	{
		var $imgFile="";
		var $imgWidth=0;
		var $imgHeight=0;
		var $imgType="";
		var $imgAttr="";
		var $type=NULL;
		var $_img=NULL;
		var $_error="";
		var $source_image='';
		var $new_image='';
		var $width	= '';
		var $height = '';
		var $maintain_ratio = false;
		var $create_thumb	= false;
		var $thumb_height = '';
		var $thumb_width = '';
		var $thumb_path = '';
		var $ref = '';
		
		function ResizeImage($imgFile="")
		{
			$this->type=Array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF', 15 => 'WBMP', 16 => 'XBM');
			if(!empty($imgFile))
			{
				$this->setImage($imgFile);
			}
		}
		
		function initialize($config = ''){
			if (empty($config)){
				print 'Parameter Empty';
				return false;
			}
			
			foreach ($config as $key=>$value){
				$this->$key = $value;
			}
			
			$this->imgFile = $this->source_image;
			
			if (empty($this->source_image) or (!file_exists($this->imgFile))){
				print 'Source Image Missing';			
				return false;
			}
			
			if (empty($this->width) or empty($this->height)){
				print 'Image Dimension Missing';			
				return false;
			}
			
			if (empty($this->new_image)){
					print 'Destination path for image missing';
			}
			
			
			$this->ResizeImage($this->imgFile);
			
			if ($this->maintain_ratio == true){
				
				//list($width, $height) = getimagesize($originalImage);
   				$xscale=$this->imgWidth/$this->width;
    			$yscale=$this->imgHeight/$this->height;
				
				if ($yscale>$xscale){
			        $new_width = round($this->imgWidth * (1/$yscale));
			        $new_height = round($this->imgHeight * (1/$yscale));
			    }
			    else {
			        $new_width = round($this->imgWidth * (1/$xscale));
			        $new_height = round($this->imgHeight * (1/$xscale));
			    }
				
			    $this->resize($new_width,$new_height,$this->new_image);
				
								
			} else {
				//$this->resize($this->width,$this->height,DOCUMENTROOT.$this->new_image);
				if ($this->ref == 'h'){
					$finalPercent = ($this->height/$this->imgHeight)*100;	
				} else {
					$finalPercent = ($this->width/$this->imgWidth)*100;	
				}
				
				
				$this->resize_percentage($finalPercent,$this->new_image);
			}
			
			if ($this->create_thumb == true){
				
				if (empty($this->thumb_width) or (empty($this->thumb_height))){
					print 'Dimension for thumbnail missing';
					return false;
				}
				
				if (empty($this->thumb_path)){
					print 'Destination Path for Thumbnail Missing';
				}
				
				if ($this->maintain_ratio == true){
					
					$xscale=$this->imgWidth/$this->thumb_width;
	    			$yscale=$this->imgHeight/$this->thumb_height;
					
					if ($yscale>$xscale){
				        $new_width = round($this->imgWidth * (1/$yscale));
				        $new_height = round($this->imgHeight * (1/$yscale));
				    }
				    else {
				        $new_width = round($this->imgWidth * (1/$xscale));
				        $new_height = round($this->imgHeight * (1/$xscale));
				    }
					
				    $this->resize($new_width,$new_height,$this->thumb_path);
					
									
				} else {
					$finalPercent = ($this->thumb_width/$this->imgWidth)*100;
					$this->resize_percentage($finalPercent,$this->thumb_path);
					//$this->resize($this->thumb_width,$this->thumb_height,DOCUMENTROOT.$this->thumb_path);
				}
			}	
		}

		function error()
		{
			return $this->_error;
		}

		function setImage($imgFile)
		{
			$this->imgFile=$imgFile;
			return $this->_createImage();
		}

		function close()
		{
			return @imagedestroy($this->_img);
		}

		function resize_limitwh($imgwidth,$imgheight,$newfile=NULL)
		{
			if(empty($this->imgFile))
			{
				$this->_error="File name is not initialised.";
				return false;
			}			
			if($this->imgWidth <= 0 || $this->imgHeight <= 0)
			{
				$this->_error="Could not resize given image";
				return false;
			}			
			if($this->imgWidth > $imgwidth)
				$image_per = floor(($imgwidth * 100) / $this->imgWidth);

			if(floor(($this->imgHeight * $image_per)/100) > $imgheight)
				$image_per = floor(($imgheight * 100) / $this->imgHeight);

			$this->resize_percentage($image_per,$newfile);

		}

		function resize_percentage($percent=100,$newfile=NULL)
		{
			$newWidth=($this->imgWidth*$percent)/100;
			$newHeight=($this->imgHeight*$percent)/100;
			return $this->resize($newWidth,$newHeight,$newfile);
		}

		function resize_xypercentage($xpercent=100,$ypercent=100,$newfile=NULL)
		{
			$newWidth=($this->imgWidth*$xpercent)/100;
			$newHeight=($this->imgHeight*$ypercent)/100;
			return $this->resize($newWidth,$newHeight,$newfile);
		}
		
		function resize($width,$height,$newfile=NULL)
		{
			if(empty($this->imgFile))
			{
				$this->_error="File name is not initialised.";
				return false;
			}
			if($this->imgWidth<=0 || $this->imgHeight<=0)
			{
				$this->_error="Could not resize given image";
				return false;
			}
			if($width<=0)
				$width=$this->imgWidth;
			if($height<=0)
				$height=$this->imgHeight;
				
			return $this->_resize($width,$height,$newfile);
		}

		function _getImageInfo()
		{
			list($this->imgWidth,$this->imgHeight,$type,$this->imgAttr) = getimagesize($this->imgFile);
			$this->imgType=$this->type[$type];
		}

		function _createImage()
		{
			//echo $this->imgFile
			$this->_getImageInfo($this->imgFile);
			if($this->imgType=='GIF')
			{
				$this->_img=@imagecreatefromgif($this->imgFile);
			}
			elseif($this->imgType=='JPG')
			{
				$this->_img=@imagecreatefromjpeg($this->imgFile);
			}
			elseif($this->imgType=='PNG')
			{
				$this->_img=@imagecreatefrompng($this->imgFile);
			}			
			if(!$this->_img || !@is_resource($this->_img))
			{
				$this->_error="Error loading ".$this->imgFile;
				return false;
			}
			return true;
		}

		function _resize($width,$height,$newfile=NULL)
		{
			if (!function_exists("imagecreate"))
			{
				$this->_error="Error: GD Library is not available.";
				return false;
			}

			$newimg=@imagecreatetruecolor($width,$height);
			imagealphablending( $newimg, false );
			imagesavealpha( $newimg, true );
			@imagecopyresampled ( $newimg, $this->_img, 0,0,0,0, $width, $height, $this->imgWidth,$this->imgHeight);
			if($newfile===HAR_AUTO_NAME)
			{
				if(@preg_match("/\..*+$/",@basename($this->imgFile),$matches))
			   		$newfile=@substr_replace($this->imgFile,"_har",-@strlen($matches[0]),0);			
			}
			elseif(!empty($newfile))
			{
				if(!@preg_match("/\..*+$/",@basename($newfile)))
				{
					if(@preg_match("/\..*+$/",@basename($this->imgFile),$matches))
					   $newfile=$newfile.$matches[0];
				}
			}

			if($this->imgType=='GIF')
			{
				if(!empty($newfile))
					@imagegif($newimg,$newfile);
				else
				{
					@header("Content-type: image/gif");
					@imagegif($newimg);
				}
			}
			elseif($this->imgType=='JPG')
			{				
				if(!empty($newfile))
					imagejpeg($newimg,$newfile);
				else
				{
					@header("Content-type: image/jpeg");
					@imagejpeg($newimg);
				}
			}
			elseif($this->imgType=='PNG')
			{
				if(!empty($newfile))
					@imagepng($newimg,$newfile);
				else
				{
					@header("Content-type: image/png");
					@imagepng($newimg);
				}
			}
			@imagedestroy($newimg);
		}
	}
?>