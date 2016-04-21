<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>JQuery Test
    </title>
<?
   $dir = '00000';
   if ($handle = opendir($dir)) {
   $pics = array ();
    while (false !== ($entry = readdir($handle))) {
       if($entry != '.' && $entry !='..')
        $pics[] =  $dir.'/'.$entry;
    }
    closedir($handle);
    sort($pics);
   }
    ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script>
var imgURLs =  [ <?
  $i=0; $len=count($pics);
  foreach($pics as $pic) {
    echo '"'.$pic.'"'.($i<$len-1 ? ', ':'');
    $i++;
  } 
 ?> ];
 
 var imgObjs = [];
 jQuery.each(imgURLs, function(i, imgURL) {
   var img = new Image();
   img.src = imgURL;
   img.width = 600;
   img.id = 'img' + i;
   img.className = 'small';
   imgObjs.push(img); 
  });
$(function() {
    $( "#slider" ).slider({
      value:<?=floor($len/2) ?>,
      min: 0,
      max: <?=$len-1 ?>,
      step: 1,
      slide: function( event, ui ) {
        $( "#display" ).html(imgObjs[ui.value]);
        $ ("#output").html(ui.value);
        native_width = 0;
        native_height = 0;        
        $(".magnified").fadeOut(100);
        $(".magnified").css("background","url('" + imgObjs[ui.value].src + "') no-repeat");
      }
    });
  });
 native_width = 0;
 native_height = 0;
 fade_threshold = 20;
  
  // Magnifier code
  $(document).ready(function(){
  $(".magnified").css("background","url('" + $(".small").attr("src") + "') no-repeat");
	$(".magnify").mousemove(function(e){
		if(!native_width && !native_height)
		{
			var image_object = new Image();
			image_object.src = $(".small").attr("src");
			native_width = image_object.width;
			native_height = image_object.height;
		}
		else
		{
			var magnify_offset = $(this).offset();
			var mx = e.pageX - magnify_offset.left;
			var my = e.pageY - magnify_offset.top;
			
			if(mx < $(this).width()-fade_threshold && my < $(this).height()-fade_threshold && mx > fade_threshold && my > fade_threshold)
				$(".magnified").fadeIn(500);
			else
				$(".magnified").fadeOut(500);
			if($(".magnified").is(":visible"))
			{
				var rx = Math.round(mx/$(".small").width()*native_width - $(".magnified").width()/2)*-1;
				var ry = Math.round(my/$(".small").height()*native_height - $(".magnified").height()/2)*-1;
				var bgp = rx + "px " + ry + "px";
				
				var px = mx - $(".magnified").width()/2;
				var py = my - $(".magnified").height()/2;
        $(".magnified").css({left: px, top: py, backgroundPosition: bgp});
			}
		}
	})
});
</script>
<style>
.magnify {position: relative; cursor: none;}
.magnified {
  width: 240px;
  height: 200px;
  position: absolute;
  z-index:4;
  border-radius: 1%;
  box-shadow: 0 0 0 7px rgba(128, 128, 128, 0.85), 0 0 7px 7px rgba(0, 0, 0, 0.25), inset 0 0 40px 2px rgba(0, 0, 0, 0.25);
  display: none;
  } 
</style>   
  </head>
  <body>
    <div style="width: 600px" class="magnify">
      <div class="magnified">
      </div>
      <div id="display">
        <img class="small" style="width: 600px" src="<?=$pics[sizeof($pics)/2] ?>">
      </div>
    </div>
    <div style="width: 600px" id="slider">
    </div>
    <div id="output">
    </div>
  </body>
</html>