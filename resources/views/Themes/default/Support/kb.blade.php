@extends( $THEME_HOME.'master-storefront')
@section('content')

<?php
	$content = "";
	$query= \Input::all();
	$path = storage_path("app/*.txt");
	$files = glob($path);

	if(sizeof($query)==1)
	{
		$f = key($query);
		$path = storage_path("app/".$f.".txt");
		if(file_exists($path) && is_file($path))
		{
			$content = file_get_contents($path);
			$files = array();
		}
	}
?>
<div class="container-fluid"

	<div class="row">
		<div class="col-xs-2">
		</div>

		<div class="col-xs-8">
		@if(sizeof($files) == 0)
			@if(strlen($content)==0)
				<b>There are no articles available at this time....</b>
			@else
				{!! $content !!}
			@endif
		@else
			<h2>Knowledge Base Articles</h2>
			<table class="table table-striped">
			<thead>
				<th>KB Number</th>
				<th>Description</th>
				<th>Date Released</th>
			</thead>
			<tbody>
			@foreach($files as $file)
			<?php
				$content = file_get_contents($file); # extract filename, use as referenece to file and title tag
				$regex = '#<title>(.*?)</title>#';
				$title = preg_match( $regex, $content, $matches);
				$file_data = pathinfo($file);
				$file_time = filemtime($file);
			?>

				<tr>
					<td>{{ $file_data['filename'] }}</td>
					<td><a href="/kb?{{$file_data['filename'] }}">{{ $matches[1] }}</a></td>
					<td>{{ date("d-m-Y", $file_time) }}</td>
				</tr>
			@endforeach
			</table>
		@endif
		</div>


		<div class="col-xs-2">
		</div>
	</div>
</div>
<br/>
<br/>
<br/>
@stop
