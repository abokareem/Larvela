		@if(Session::has('flash_error'))
			<script>$.notify({ message: '{{ Session::get('flash_error') }}' },{ type: 'danger' });</script>
		@endif
		@if(Session::has('flash_message'))
			<script>$.notify({ message: '{{ Session::get('flash_message') }}' },{ type: 'success' });</script>
		@endif

