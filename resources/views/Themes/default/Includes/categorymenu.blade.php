<!-- START:categorymenu -->

<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<?php
#<link href="https://fonts.googleapis.com/css?family=Francois+One" rel="stylesheet">
#<link href="https://fonts.googleapis.com/css?family=Catamaran:200,700" rel="stylesheet">
?>
<style>

#menu-bar
{
font-family:Roboto;
width:95%;
margin:auto;
padding: 6px 6px 4px 6px;
height: 40px;
line-height: 100%;
background-color:white;
position:relative;
z-index:999;
}


#menu-bar li
{
margin: 0px 0px 6px 0px;
padding: 0px 6px 0px 6px;
float: left;
position: relative;
list-style: none;
background-color:white;
}
<?php # color: #FF1493; ?>
#menu-bar a { font-weight:regular; font-family:Roboto; font-style:normal; font-size:15px; color:black; text-decoration:none; display:block; padding: 6px 20px 6px 20px; margin: 0; margin-bottom:6px; border-radius:4px; -webkit-border-radius:4px; -moz-border-radius:4px; }
#menu-bar li ul li a { margin: 0; }
#menu-bar .active a, #menu-bar li:hover > a { background:white; color:red; }
#menu-bar ul li:hover a, #menu-bar li:hover li a { background: none; border:none; color:blue; }
#menu-bar ul a:hover { background:#f2f2f2; !important; color:red !important; border-radius: 0; -webkit-border-radius: 0; -moz-border-radius: 0; text-shadow: 0px 0px 0px green; }
#menu-bar li:hover > ul { display: block; }
#menu-bar ul { background:white; display: none; margin: 0; padding: 0; width: 185px; position: absolute; top: 30px; left: 0;}
#menu-bar ul li { float: none; margin: 0; padding: 0; }

/* hover over the anchor and text colour changes needed */
#menu-bar ul a { padding:10px 0px 10px 15px; color:black !important; font-size:14px; font-style: bold;
font-family: 'Roboto'; font-weight: normal; text-shadow: 2px 2px 3px white; }

#menu-bar ul li:first-child > a { border-top-left-radius: 4px; -webkit-border-top-left-radius: 4px; -moz-border-radius-topleft: 4px; border-top-right-radius: 4px; -webkit-border-top-right-radius: 4px; -moz-border-radius-topright: 4px; }

#menu-bar ul li:last-child > a { border-bottom-left-radius: 4px; -webkit-border-bottom-left-radius: 4px; -moz-border-radius-bottomleft: 4px; border-bottom-right-radius: 4px; -webkit-border-bottom-right-radius: 4px; -moz-border-radius-bottomright: 4px; }

#menu-bar:after { content: "."; display: block; clear: both; visibility: hidden; line-height: 0; height: 0; }

#menu-bar { display: inline-block; }

html[xmlns] #menu-bar { display: block; }

* html #menu-bar { height: 1%; }

</style>
<nav id="menu-bar-div" class="row text-center">
{!! StoreHelper::CategoryMenu() !!}
</nav>


<script>


function InitCategoryMenu()
{
console.log("Init Category Menu");
$('ul.menu-bar').prepend('<li><i class="fa fa-home fa-fw fa-2x"></i></li>');
}

</script>
<!-- END:categorymenu -->

