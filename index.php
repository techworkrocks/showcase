<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Gem Display</title>
<?
   // determine available dirs
   if ($handle = opendir(".")) {
   $dirs = array ();
    while (false !== ($entry = readdir($handle))) {
       if(strpos($entry, '.')!==0 && is_dir($entry))
        $dirs[] =  $entry;
    }
    closedir($handle);
    sort($dirs);
   }
   

   // this variable defines the path relative to this script's path were to look for 
   // image files
   $dir = isset($_GET['d']) ? $_GET['d'] : $dirs[0];
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
<script src="jquery.ui.touch-punch.min.js"></script>

<script>
var imgURLs =  [ <?
  $i=0; $len=count($pics);
  foreach($pics as $pic) {
    echo '"'.$pic.'"'.($i<$len-1 ? ', ':'');
    $i++;
  } 
 ?> ];
 
 var imgObjs = [];
 var imgsToLoad = 0;
 jQuery.each(imgURLs, function(i, imgURL) {
   imgsToLoad++;
   var img = new Image();
   img.src = imgURL;
   img.width = 480;
   img.id = 'img' + i;
   img.className = 'small';
   img.onload = function() {
       imgsToLoad--;
       if(imgsToLoad > 0)
          $("#output").html("<span class='alert'>"+imgsToLoad + " images left to load</span>");
       else
          $("#output").html("");
   };
   imgObjs.push(img); 
  });
  
$(function() {
    $( "#scene" ).selectmenu({
      change: function(event, data) {
        location.href="?d="+data.item.value;
      }
      });
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
  
$("#slider").draggable();
  
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
.magnify {position: relative; } // cursor: none;}
.magnified {
  width: 240px;
  height: 200px;
  position: absolute;
  z-index:4;
  border-radius: 1%;
  box-shadow: 0 0 0 5px rgba(128, 128, 128, 0.85), 0 0 5px 5px rgba(0, 0, 0, 0.25), inset 0 0 30px 2px rgba(0, 0, 0, 0.25);
  display: none;
  }
.ui-slider .ui-slider-handle { height: 20px; width: 20px; padding-top: 10px; padding-left: 10px}
body { font-family: sans-serif; }
select { width: 200px; } 
    label {
      margin: 30px 0 0 0;
      display: block;
      font-size: 12pt;
    }
.alert { color: #ff0000; }
</style>   
  </head>
  <body>
  <label for="scene">Select Scene</label>
    <select name="scene" id="scene">
    <? foreach($dirs as $ldir): ?>
      <option <?= ($ldir==$dir ? 'selected="selected"' : '') ?>><?=$ldir?></option>
    <? endforeach; ?>  
    </select>&nbsp; &nbsp;
    <span id="output">
    </span>
    <br><br>
  
    <div style="width: 480px" class="magnify">
      <div class="magnified">
      </div>
      <div id="display">
        <img class="small" style="width: 480px" src="<?=$pics[sizeof($pics)/2] ?>">
      </div>
    </div>
    <p>&nbsp;</p>
    <div style="width: 480px" id="slider">
    </div>
    <br>
  </body>
</html>