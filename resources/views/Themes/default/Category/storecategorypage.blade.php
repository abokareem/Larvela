@extends($THEME_HOME.'master-storefront')
@section('content')

<!-- START:storecategorypage -->

<div class="category-description">{{ $category->category_description }}</div>

@if(view()->exists($THEME_HOME.'product-loop'))
<script src="https://npmcdn.com/isotope-layout@3.0.0/dist/isotope.pkgd.min.js"></script>
@include($THEME_HOME.'product-loop')
@else
Category Page is currently available<br>
Signup below for notification email when products are available
@endif

@if(view()->exists($THEME_INCLUDES.'capture'))
@include($THEME_INCLUDES.'capture')
@endif


<!-- END:storecategorypage THEME: {{ $THEME_CATEGORY }}-->
@stop
