<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width"/>
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
       if($entry != '.' && $entry !='..' && strpos($entry, ".jpg") == strlen($entry)-strlen(".jpg"))
        $pics[] =  $dir.'/'.$entry;
    }
    closedir($handle);
    sort($pics);
   }
?>
   
<link rel="stylesheet" href="jquery-ui.css">
<script src="jquery-1.10.2.js"></script>
<? $version = "1.4.5"; ?>
<link rel="stylesheet" href="jquery.mobile-<?=$version?>.min.css">
<script src="jquery.mobile-<?=$version?>.min.js"></script>
<script>
var imgURLs =  [ <?
  $i=0; $len=count($pics);
  foreach($pics as $pic) {
    echo '"'.$pic.'"'.($i<$len-1 ? ', ':'');
    $i++;
  } 
 ?> ];
 
moveHandler = function(e){
    //$("#magnifier").html($('#originalImage').height);
    //imgX = e.pageX - e.target.x;
    //imgY = e.pageY - e.target.y;
    originalImage = $("#originalImage");
    magnifier = $("#magnifier");
    
    if(originalImage.width() >= imgObjs[curIndex].naturalWidth) {
      magnifier.css({ display: "none" });
      return;
    }
    
    offset = originalImage.offset(); 

    imgX = e.pageX - offset.left;
    imgY = e.pageY - offset.top;
    
    // hide magnifier if out of bounds
    if(imgX < 0 || imgY < 0 || imgX > originalImage.width() || imgY > originalImage.height()) {
      magnifier.css({ display: "none" });
      return;
    }
    
    magnWidth = magnifier.width();
    magnHeight = magnifier.height();
    
    scale = imgObjs[curIndex].naturalWidth / originalImage.width();
    px = -Math.max(0, imgX * scale - magnWidth / 2);
    py = -Math.max(0, imgY * scale - magnHeight / 2);
    //magnifier.html(imgX + ";" + imgY);  
    
    var posX = e.pageX - magnWidth/2;
    var posY = e.pageY - magnHeight/2;
    
    var bgp = px + "px " + py + "px";
    magnifier.css({left: posX, top: posY, backgroundPosition: bgp, display: "block" });
  }; 
 
$(function(){   
   $("#scene").change( function(event, ui) {
     location.href="?d="+$(this).val();
   });
});  

 var imgObjs = [];
 var imgsToLoad = 0;
 jQuery.each(imgURLs, function(i, imgURL) {
   imgsToLoad++;
   var img = new Image();
   img.src = imgURL;
   //img.width = 480;
   img.id = 'originalImage';
   img.onmousemove = moveHandler;
   img.className = 'small';
   img.onload = function() {
       imgsToLoad--;
       if(imgsToLoad > 0) {
          $("#progressbar").html("<span class='alert'>Loading ... ("+imgsToLoad + " left)</span>");
          //progressValue =  100*(imgObjs.length-imgsToLoad)/imgObjs.length;
          //$( "#progressbar" ).progressbar("value", progressValue);
       } else  {
          $("#progressbar").html("");
          $("#output").html("");
       }
   };
   imgObjs.push(img); 
  });

curIndex = <?=floor(sizeof($pics)/2); ?>;

$(function() {
  $('#slider').change(function(event) {
        $( "#display" ).html(imgObjs[event.target.value]);
        curIndex = event.target.value;
        $(".magnifier").css("background","url('" + $("#originalImage").attr("src") + "') no-repeat");
  });
});

$(document).ready(function() {
  $("#originalImage").mousemove(moveHandler );
  $("#magnifier").mousemove(moveHandler );
  $(".magnifier").css("background","url('" + $("#originalImage").attr("src") + "') no-repeat");
  
  // load textual information
  $.getJSON('<?=$dir ?>/info.json', function(data) {
      $.each(data, function(index, element) {
          $.each(element, function(key, value) {
            $('#infotable').append('<tr><td class="tablekey">' + key + ':</td><td class="tablevalue">'+value+'</td></tr>');  
          });
      })
  });
});

</script>
<style>
img { text-align: center; width: 80%; max-width: 640px; padding: 0px; }
.ui-slider .ui-slider-handle { height: 20px; width: 20px; padding-top: 10px; padding-left: 10px}
body { font-family: sans-serif; text-align: center; }
select { width: 360px; } 
label { margin: 30px 0 0 0; display: block; font-size: 12pt;   }
.alert { font-family: sans-serif; font-size: 14pt; color: #ff0000; }
.magnifier { width: 360px; max-width: 25%; height: 270px; max-height: 25%; background-color: #bbbbbb; position: absolute; display: none }
#infotable { text-align: left; border-spacing: 5px; margin: 0 auto }
.tablekey { background-color: #dddddd }
.tablevalue { background-color: #eeeeee }

</style>   
  </head>
  <body>
    <div class="ui-field-contain">
    <select name="scene" id="scene" class="select">
    <? foreach($dirs as $ldir): ?>
      <option <?= ($ldir==$dir ? 'selected="selected"' : '') ?>><?=$ldir?></option>
    <? endforeach; ?>  
    </select>
    </div>
    <br><br>
    <div id="progressbar"></div>
      <div id="display" style="width: 100%; ">
        <img style="text-align: center; border: 1px" id="originalImage" src="<?=$pics[sizeof($pics)/2] ?>">
      </div>
    <input type="range" name="slider" id="slider" value="<?=floor($len/2) ?>" min="0" max="<?=$len-1 ?>" />
    <br>
    <div id="magnifier" class="magnifier"></div>
    <table id="infotable">
    </table>
  </body>
</html>