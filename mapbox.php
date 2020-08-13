

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Animal Rights Activism Map - The largest map of animal rights organization groups!</title>
   <script src='https://api.mapbox.com/mapbox-gl-js/v1.11.1/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v1.11.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="shortcut icon" href="favicon.ico" />
	 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<style>
body {
  margin: 0;
  font-family: "Lato", sans-serif;
}

.sidebar {
  margin: 0;
  padding: 0;
  width: 215px;
  background-color: #f1f1f1;
  position: fixed;
  height: 100%;
  overflow: auto;
}

.sidebar a {
  display: block;
      background-color: #a12e38;
    color: white;
    padding: 12px 16px 12px 16px;
  text-decoration: none;
}
 
.sidebar a.active {
  background-color: #4CAF50;
  color: white;
}

.sidebar a:hover:not(.active) {
  background-color: #272525;
  color: white;
}

div.content {
  margin-left: 200px;
  padding: 1px 16px;
  height: 1000px;
}

@media screen and (max-width: 700px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  .sidebar a {float: left;}
  div.content {margin-left: 0;}
}

@media screen and (max-width: 400px) {
  .sidebar a {
    text-align: center;
    float: none;
  }
}
</style>
</head>
<body>

<div class="sidebar" style="background-color: #272525;overflow-x: hidden; /* Hide horizontal scrollbar */">
<img src="https://i.imgur.com/KYMGdJP.png" style="width: 215px;margin-bottom: -5px;">
<a href="https://animalrightsmap.org" style="font-size: 18px;">Home</a>
<a href="#" style="font-size: 18px;">Learn more</a>
<a href="mailto:map@veganhacktivists.org" style="font-size: 18px;">Submit a group</a>
<a href="https://www.patreon.com/veganhacktivists" target="_blank" style="font-size: 18px;padding: 10px 16px 12px 16px;">♡ Donate</a>


<p style="padding: 13px;color: white;line-height: 27px;text-align:center;">Browse the largest collection of animal rights activist groups from around the world, all in one single map! </p>
<center><a href="https://veganhacktivists.org/" target="_blank" style="padding: 0px 16px 5px 16px;background-color: #272525;"><img src="https://i.imgur.com/xSHDo4E.png" style="width: 100px;"></a>
<p style="background-color: #272525 !important; line-height: 15px;color: white;">A project by the <a href="https://veganhacktivists.org/" style="background-color: #272525;" target="_blank"><u>Vegan Hacktivists</u></p></a>

<a href="https://www.instagram.com/veganhacktivists/" target="_blank" style="display: contents;">
                <i class="fab fa-instagram" style="font-size:30px;padding-right: 10px;"></i>
            </a>
            <a href="https://www.patreon.com/veganhacktivists" target="_blank" style="display: contents;">
                <i class="fas fa-heart" style="font-size:30px;padding-right: 10px;"></i>
            </a>
			
			</center>


</div>

<div class="content" style="padding:0px;overflow:hidden">
<div id='map' style='width: 100%; height: 100vh;'></div>
<script>
mapboxgl.accessToken = 'pk.eyJ1IjoiYW5pbWFscmlnaHRzbWFwIiwiYSI6ImNrZHNseW43NzE0NDAyeG1zczR6NGxyYmMifQ.ehhW_TXPpPwK9BbfZzhLug';
var map = new mapboxgl.Map({
container: 'map',
style: 'mapbox://styles/animalrightsmap/ckdsoof6b15iy19nztyzyfmab'
});
</script>

<!--<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1riR6Rl0KTltNrzpfddesLauOwk0QnIs5" frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>
<script type="text/javascript">
  // Hack to open the sidebar by default. Whenever Google updates its UI or
  // we update the name, this will no longer work ¯\_(?)_/¯
  // Also, if you are reading this come join us at https://veganhacktivists.org!
  window.onload = function () {
    const [, $el] = Array.from(document.querySelectorAll("[aria-label='Animal Rights Map - VeganHacktivists.org']"));

    if ($el) {
      $el.parentNode.childNodes[0].click();
    }
  }
</script>
-->
</div>

</body>
</html>