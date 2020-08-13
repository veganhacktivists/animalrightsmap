

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Animal Rights Activism Map - The largest map of animal rights organization groups!</title>
    <link rel="shortcut icon" href="favicon.ico" />
<style>
body {
  margin: 0;
  font-family: "Lato", sans-serif;
}

.sidebar {
  margin: 0;
  padding: 0;
  width: 200px;
  background-color: #f1f1f1;
  position: fixed;
  height: 100%;
  overflow: auto;
}

.sidebar a {
  display: block;
      background-color: #a12e38;
    color: white;
  padding: 16px;
  text-decoration: none;
}
 
.sidebar a.active {
  background-color: #4CAF50;
  color: white;
}

.sidebar a:hover:not(.active) {
  background-color: #555;
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

<div class="sidebar" style="background-color: #272525;">
<img src="https://i.imgur.com/KYMGdJP.png" style="width: 200px;margin-bottom: -4px;">
  <a href="#about">Home</a>
    <a href="#about">Learn more</a>
	  <a href="#about">Submit a group</a>
	  	  <a href="#about">Donate</a>
		  <p style="
    padding: 13px;
    color: white;
    line-height: 24px;
">With over 2,500 groups, browse the largest collection of animal rights activist groups all located in one single map! </p>
</div>

<div class="content" style="padding:0px;overflow:hidden">
	<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1riR6Rl0KTltNrzpfddesLauOwk0QnIs5" frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>
<script type="text/javascript">
  // Hack to open the sidebar by default. Whenever Google updates its UI or
  // we update the name, this will no longer work ¯\_(ツ)_/¯
  // Also, if you are reading this come join us at https://veganhacktivists.org!
  window.onload = function () {
    const [, $el] = Array.from(document.querySelectorAll("[aria-label='Animal Rights Map - VeganHacktivists.org']"));

    if ($el) {
      $el.parentNode.childNodes[0].click();
    }
  }
</script>
</div>

</body>
</html>
