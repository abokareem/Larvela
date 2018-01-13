<!-- START:slider -->

<style>

.store-slider { margin: 20px; }
.carousel { background-color: white; margin-top: 20px; }
.carousel .item img{ margin: 0 auto; }
header.carousel { height: 250px; }
header.carousel .item,
header.carousel .item.active,
header.carousel .carousel-inner { height: 250px; }
header.carousel .fill {
width: 1172px;
height: 350px;
background-position: center;
background-size: cover;
}
.carousel-inner > .item > img, .carousel-inner > .item > a > img { width: 950px; height: 250px; }
</style>
<?php
$slides = array();
if($handle = opendir('./media/slides'))
{
	while(false !== ($file = readdir($handle)))
	{
		if($file != "." && $file != ".." && strtolower(substr($file, 0,6)) == 'slide-')
		{
			array_push($slides, $file);
		}
	}
	closedir($handle);
}
sort($slides);

?>
<div class="container">
<div class="row">
	<div class="store-slider">
		<div id="carousel" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators"><?php $cnt=0; ?>
			@foreach($slides as $slide)
				<li data-target="#Carousel" data-slide-to="{{ $cnt }}" class="<?php if($cnt==0) echo "active"; ?>"</li>
				<?php $cnt++; ?>
			@endforeach
			</ol>
			<div class="carousel-inner"><?php $cnt=0; ?>
			@foreach($slides as $slide)
				<div class="item <?php if($cnt==0) echo "active";?>">
					<img src="/media/slides/{{ $slide }}">
				</div>
				<?php $cnt++; ?>
			@endforeach
			</div>
		</div>
		<!-- Carousel controls -->
		<a class="carousel-control left" href="#Carousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
		<a class="carousel-control right" href="#Carousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
	</div>
</div>
</div>
<script>
function InitSlider()
{
$('#carousel').carousel({ interval: 3500 });
}
</script>
