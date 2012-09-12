<script type="text/javascript" src="/js/vendor/jquery.ad-gallery.min.js"></script>
<link rel="stylesheet" href="../css/vendor/jquery.ad-gallery.css" />


<script type="text/javascript">
$(document).ready(function(){
	var galleries = $('.ad-gallery').adGallery({
		  loader_image: '/css/vendor/loader.gif',
		  // Width of the image, set to false and it will 
		  // read the CSS width
		  width: 430, 
		  // Height of the image, set to false and it 
		  // will read the CSS height
		  height: 400, 
		  // Opacity that the thumbs fades to/from, (1 removes fade effect)
		  // Note that this effect combined with other effects might be 
		  // resource intensive and make animations lag
		  thumb_opacity: 0.7,
		  // Which image should be displayed at first? 0 is the first image  
		  start_at_index: 0, 
		  // Whether or not the url hash should be updated to the current image
		  update_window_hash: true, 
		  // Either false or a jQuery object, if you want the image descriptions
		  // to be placed somewhere else than on top of the image
		  // Should first image just be displayed, or animated in?
		  animate_first_image: true,
		  // Which ever effect is used to switch images, how long should it take?  
		  animation_speed: 400, 
		  // Can you navigate by clicking on the left/right on the image?
		  display_next_and_prev: true, 
		  // Are you allowed to scroll the thumb list?
		  display_back_and_forward: true, 
		  // If 0, it jumps the width of the container
		  scroll_jump: 0, 
		  slideshow: {
		    enable: false,
		    autostart: true,
		    speed: 5000,
		    start_label: 'Start',
		    stop_label: 'Stop',
		    // Should the slideshow stop if the user scrolls the thumb list?
		    stop_on_scroll: true, 
		    // Wrap around the countdown
		    countdown_prefix: '(', 
		    countdown_sufix: ')',
		    onStart: function() {
		      // Do something wild when the slideshow starts
		    },
		    onStop: function() {
		      // Do something wild when the slideshow stops
		    }
		  },
		  // or 'slide-vert', 'resize', 'fade', 'none' or false
		  effect: 'slide-vert', 
		  // Move to next/previous image with keyboard arrows?
		  enable_keyboard_move: true, 
		  // If set to false, you can't go from the last image to the first, and vice versa
		  cycle: true, 
		  // All hooks has the AdGallery objects as 'this' reference
		  hooks: {
		    // If you don't want AD Gallery to handle how the description
		    // should be displayed, add your own hook. The passed image
		    // image object contains all you need
		  },
		  // All callbacks has the AdGallery objects as 'this' reference
		  callbacks: {
		    // Executes right after the internal init, can be used to choose which images
		    // you want to preload
		    init: function() {
		      // preloadAll uses recursion to preload each image right after one another
		      this.preloadAll();
		      // Or, just preload the first three
		      this.preloadImage(0);
		      this.preloadImage(1);
		      this.preloadImage(2);
		    },
		    // This gets fired right after the new_image is fully visible
		    afterImageVisible: function() {
		      // For example, preload the next image
		      var context = this;
		      this.loading(true);
		      this.preloadImage(this.current_index + 1,
		        function() {
		          // This function gets executed after the image has been loaded
		          context.loading(false);
		        }
		      );

		      // Want slide effect for every other image?
		      if(this.current_index % 2 == 0) {
		        this.settings.effect = 'slide-hori';
		      } else {
		        this.settings.effect = 'fade';
		      }
		    },
		    // This gets fired right before old_image is about to go away, and new_image
		    // is about to come in
		    beforeImageVisible: function(new_image, old_image) {
		      // Do something wild!
		    }
		  }
		});

});
</script>


<div class="ad-gallery">
	<div class="ad-image-wrapper"></div>
	<div class="ad-controls"></div>
	<div class="ad-nav">
		<div class="ad-thumbs">
			<ul class="ad-thumb-list">
				<?php
				foreach($pictures as $picture){
					if ( $picture->sharerUsername == ""){
						$pictureIsShared = false;
						$pictureFolder = $username;
					} else{
						$pictureIsShared = true;
						$pictureFolder = $picture->sharerUsername;
					}

					$pictureSrc =  '/pictures/'. $pictureFolder.'/thumbnails/'.$picture->filename;
					$mainPicUrl =  '/pictures/'.$pictureFolder.'/optimized/'.$picture->filename;
					$pictureDescription = ($picture->description == "" ? "" : "\""). $picture->description . ($picture->description == "" ? "" : "\"");
					?>

				<li><a href="<?php echo $mainPicUrl?>"> <img src="<?php echo $pictureSrc?>" title="<?php echo $picture->description?>">
				</a>
				</li>

				<?php }?>
			</ul>
		</div>
	</div>
</div>
<div id="descriptions">

    </div>
<?php
			if ($picturesFound) {
				$pictureTime = date("g:i a", strtotime($picture->timetaken));
				$pictureIsShared = isset($picture->sharerUsername) ? true : false;
			
		?>
				
		 
		<!-- <div class="formlabel" style="display: <?php echo$picturesFound? "none" : "block"; ?>;">No Pictures taken on this day.</div>  -->
		<div class="ui-state-default" style="height: 30px; display: <?php echo $pictureIsShared ? "block" : "none"?>; ?>;"><span style="top: 5px; position: relative;"><?php echo ("Shared to you by '".($securityService->getUserByUsername($picture->sharerUsername)->name))."'" ?></span></div>
		<div class="ui-datepicker-inline ui-widget ui-widget-content ui-helper-clearfix" style="padding-top: 5px; position: relative; left: 0px; width:450px; display: <?php echo $pictureIsShared ? "none" : "block"?>; ?>;">
			<a href="#" onclick="callPictureRotate('left');" title="Rotate picture left"><img src="/images/rotate-left.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="callPictureRotate('right');" title="Rotate picture right"><img src="/images/rotate-right.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showCaptionOverlay();" title="Edit caption"><img src="/images/comment.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showDateTakenOverlay();" title="Edit picture date"><img src="/images/calendar.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showPictureShareOverlay();" title="Share pictures"><img src="/images/share.png"width="22"/></a>
		</div>
		<br/><br/>
		
		<?php }?>
