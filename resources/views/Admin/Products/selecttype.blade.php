@extends('Templates.admin-master')
@section('title','Select type')
@section('content')


<div class="container-fluid">
	<div class="row">
		<div class='col-lg-12'>
			<h3 class='page-header'><i class="fa fa-users"></i> Product Types</h3>
		</div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<div class="col-lg-4">&nbsp;</div>

		<div class="col-lg-4">
			<form class="form-horizontal" name="ptselect" id="ptselect" method="post">
			<table class="table table-striped">
			@foreach($product_types as $pt)
				<tr  onclick='select({{ $pt->id }})'  style="cursor: pointer">
					<td>
					@if($pt->product_type == "Basic Product")
						<input id="PT{{$pt->id}}" checked="checked" name="PT" type="radio" value="{{$pt->id}}"> {{$pt->product_type}}
					@else
						<input id="PT{{$pt->id}}" name="PT" type="radio" value="{{$pt->id}}"> {{$pt->product_type}}
					@endif
					</td>
				</tr>
			@endforeach
			</table>
			{!! Form::token() !!}
			{!! Form::close() !!}
		</ul>
		</div>

		<div class="col-lg-4">&nbsp;</div>
	</div>
</div>
<script>
function select(id)
{
var url = '/admin/select/type/'+id;
window.location.href = url;
}
</script>
@stop
