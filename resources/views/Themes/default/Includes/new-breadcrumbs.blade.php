<div class="row">
	<ul class="store-breadcrumbs breadcrumb">
	<ul class="breadcrumb">
		<li><a href="/">Home</a></li>
		@if($category->category_parent_id == 0)
			<li>{{ $category->category_title }}</li>
		@else
			@foreach($categories as $cat)
				@if($cat->id == $category->category_parent_id)
					<li><a href="/category/{{ $cat->id }}">/{{ $cat->category_title }}</a></li>
				@endif
			@endforeach
			<li>{{ $category->category_title }}</li>
		@endif
	</ul>
</div>
