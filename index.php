<!DOCTYPE html>
<html>
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-150398169-2"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-150398169-2');
  </script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Animal Rights Activism Map - The largest map of animal rights organization groups!</title>

  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
      integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
      integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
      crossorigin=""/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"
      integrity="sha384-lPzjPsFQL6te2x+VxmV6q1DpRxpRk0tmnl2cpwAO5y04ESyc752tnEWPKDfl1olr"
      crossorigin=""/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"
      integrity="sha384-5kMSQJ6S4Qj5i09mtMNrWpSi8iXw230pKU76xTmrpezGnNJQzj0NzXjQLLg+jE7k"
      crossorigin=""/>

  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
      integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
      crossorigin=""></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"
      integrity="sha384-RLIyj5q1b5XJTn0tqUhucRZe40nFTocRP91R/NkRJHwAe4XxnTV77FXy/vGLiec2"
      crossorigin=""></script>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>

  <link rel="stylesheet" href="/main.css" />
  <script src="/index.js"></script>
</head>

<body>
  <div class="sidebar" style="background-color: #272525;overflow-x: hidden; /* Hide horizontal scrollbar */">
    <img src="https://i.imgur.com/KYMGdJP.png" style="width: 100%;margin-bottom: -5px;">

    <div class="sidebarlinks">
      <a href="https://animalrightsmap.org" style="font-size: 18px;">Home</a>
      <a href="https://activisthub.org/" style="font-size: 18px;">Track your Impact</a>
      <a href="https://veganhacktivists.org/grants" style="font-size: 18px;">Get Funding</a>
      <a href="https://veganhacktivists.org/contact" style="font-size: 18px;">Submit a group</a>
      <a href="https://www.patreon.com/veganhacktivists" target="_blank" style="font-size: 18px;padding: 10px 16px 12px 16px;">♡ Donate</a>
    </div>

    <p style="padding: 13px;color: white;line-height: 27px;text-align:center;">Browse the largest collection of animal rights activist groups from around the world, all in one single map! </p>

    <div class="footer"><center>
      <a href="https://veganhacktivists.org/" target="_blank" style="padding: 0px 16px 5px 16px;background-color: #272525;"><img src="https://i.imgur.com/S0jY6S6.png" style="padding-top: 25px;width: 100px;"></a>

      <p style="background-color: #272525 !important; line-height: 15px;color: white;">A project by the <a href="https://veganhacktivists.org/" style="background-color: #272525;" target="_blank"><u>Vegan Hacktivists</u></p></a>

      <a href="https://www.instagram.com/veganhacktivists/" target="_blank" style="display: contents;"><i class="fab fa-instagram" style="font-size:30px;padding-right: 10px;padding-bottom: 20px;"></i></a>

      <a href="https://www.patreon.com/veganhacktivists" target="_blank" style="display: contents;"><i class="fas fa-heart" style="font-size:30px;padding-right: 10px;padding-bottom: 20px;"></i></a>
      </center>
    </div>

  </div>

  <div class="content" style="padding:0px;overflow:hidden">
    <div id="ahmap" allowfullscreen style="display:none;height: 100%"></div>
    <iframe id="umap" width="100%" height="300px" frameborder="0" allowfullscreen src="//umap.openstreetmap.fr/en/map/animal-rights-map_487135?scaleControl=false&miniMap=false&scrollWheelZoom=true&zoomControl=true&allowEdit=false&moreControl=true&searchControl=true&tilelayersControl=false&embedControl=null&datalayersControl=expanded&onLoadPanel=caption&captionBar=false&fullscreenControl=false&locateControl=true&measureControl=false" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>
  </div>

  <div class="footermobile" style="background-color: #272525;">
    <center>
      <a href="https://veganhacktivists.org/" target="_blank" style="padding: 0px 16px 5px 16px;background-color: #272525;"><img src="https://i.imgur.com/Dn4T6BC.png" style="padding-top: 25px;width: 100px;"></a>

      <p style="background-color: #272525 !important; line-height: 15px;color: white;">A project by the <a href="https://veganhacktivists.org/" style="background-color: #272525;color:#fff;" target="_blank"><u>Vegan Hacktivists</u></p></a>

      <a href="https://www.instagram.com/veganhacktivists/" target="_blank" style="display: contents;color:#fff;"><i class="fab fa-instagram" style="font-size:30px;padding-right: 10px;padding-bottom: 20px;"></i></a>

      <a href="https://www.patreon.com/veganhacktivists" target="_blank" style="display: contents;color:#fff;"><i class="fas fa-heart" style="font-size:30px;padding-right: 10px;padding-bottom: 20px;"></i></a>
    </center>
  </div>

</body>
</html>
