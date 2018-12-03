@extends('Templates.admin-master')
@section('title','Product Attributes Management')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-tool"></i> Product Attributes</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>Token</th>
				<th>Attribute Name</th>
				<th>Assigned Store</th>
				<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach($attributes as $a)
				<tr>
				<td onclick='select({{ $a->id }})'  style="cursor: pointer">{{ $a->id }}</td>
				<td onclick='select({{ $a->id }})'  style="cursor: pointer">{{ $a->attribute_token }}</td>
				<td onclick='select({{ $a->id }})'  style="cursor: pointer">{{ $a->attribute_name }}</td>
				@foreach($stores as $s)
					@if($s->id == $a->store_id)
					<td>{{ $s->store_name }}</td>
					@endif
				@endforeach
				<td><a href='#' id='deletemodalbox' data-id='{{$a->id}}' data-txt='{{ $a->attribute_name }}' data-toggle="modal" data-target="#deletemodal"><i class='fa fa-trash'></i> Delete</a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>

	<div class="row">
		<a href='/admin/attribute/addnew'><button class="btn btn-success">
			<i class="fa fa-user-plus"></i> Add New Attribute </button></a>
	</div>

</div>



<div class="modal fade" id="deletemodal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
				<span aria-hidden="true">×</span>
				<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title">Delete Attribute</h4>
			</div>
			<div class="modal-body">Confirm delete of Attribute, if attribute is in use delete will not occur!
				<form id='deleteform' name='deleteform' method='post'>
					<div class="form-group">
						<div id='attribute_txt' name="attribute_txt"></div>
						<input type="hidden" name="attribute_id" id="attribute_id" value=""/>
					</div>
					{!! Form::token() !!}
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id='btndeletenote' class="btn btn-danger"> Delete Note </button>
				<button type="button" class="btn btn-default" data-dismiss="modal"> Cancel </button>
			</div>
		</div>
	</div>
</div>





<script>

$(document).on("click", "#deletemodalbox", function()
{
	var Id = $(this).data('id');
	var Txt = $(this).data('txt');
	$("#attribute_id").val( Id );
	$("#attribute_txt").text( Txt );
});
	
$('#btndeletenote').click(function()
{
	$('#deleteform').attr('action','/admin/attribute/delete');
	$('#deleteform').submit();
});


function select(id)
{
var url = '/admin/attribute/edit/'+id;
window.location.href = url;
}


</script>
@stop
