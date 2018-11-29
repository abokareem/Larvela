@extends( $THEME_HOME.'master-storefront')
<?php
#
# This is the main store front entry page called from the StoreFrontController class.
#
# Variables passed in:
#
# store
# advert 
# categories
# settings
# products (randon selection of products)
# attributes
# attribute_values
#
# sizes
# colours
#
#
?>
@section('content')

<!-- START:storefront -->
@if(view()->exists( $THEME_HOME.'product-loop'))
<script src="https://npmcdn.com/isotope-layout@3.0.0/dist/isotope.pkgd.min.js"></script>
<script src="https://unpkg.com/bricks.js/dist/bricks.js"></script>
@include( $THEME_HOME.'product-loop')
@else
We are currently doing some maintenance - please check back later! 
@endif


@if(view()->exists( $THEME_INCLUDES.'capture'))
@include( $THEME_INCLUDES.'capture')
@endif


@if(view()->exists( $THEME_INCLUDES.'slider'))
@include( $THEME_INCLUDES.'slider')
@endif
<!-- END:storefront -->
@stop
