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
       if($entry != '.' && $entry !='..')
        $pics[] =  $dir.'/'.$entry;
    }
    closedir($handle);
    sort($pics);
   }
?>
   
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<? $version = "1.4.5"; ?>
<link rel="stylesheet" href="https://code.jquery.com/mobile/<?=$version?>/jquery.mobile-<?=$version?>.min.css">
<script src="https://code.jquery.com/mobile/<?=$version?>/jquery.mobile-<?=$version?>.min.js"></script>
<script>
var imgURLs =  [ <?
  $i=0; $len=count($pics);
  foreach($pics as $pic) {
    echo '"'.$pic.'"'.($i<$len-1 ? ', ':'');
    $i++;
  } 
 ?> ];
 
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
   img.width = 480;
   img.id = 'img' + i;
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

$(function() {
  $('#slider').change(function(event) {
        $( "#display" ).html(imgObjs[event.target.value]);
  });
});

</script>
<style>
img { text-align: center; width: 80%; max-width: 640px; padding: 10px; }
.ui-slider .ui-slider-handle { height: 20px; width: 20px; padding-top: 10px; padding-left: 10px}
body { font-family: sans-serif; text-align: center; }
select { width: 360px; } 
label { margin: 30px 0 0 0; display: block; font-size: 12pt;   }
.alert { font-family: sans-serif; font-size: 14pt; color: #ff0000; }
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
        <img style="text-align: center; border: 1px" src="<?=$pics[sizeof($pics)/2] ?>">
      </div>
    <input type="range" name="slider" id="slider" value="<?=floor($len/2) ?>" min="0" max="<?=$len-1 ?>" />
    <br>
  </body>
</html>