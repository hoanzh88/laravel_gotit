@extends('layouts.default')

@section('title')
Rút Thăm Trúng Thưởng
@endsection

@section('content')
	@if (count($errors) >0)
		 <ul>
			 @foreach($errors->all() as $error)
				 <li class="text-danger"> {{ $error }}</li>
			 @endforeach
		 </ul>
	 @endif

	 @if (session('status'))
		 <ul>
			 <li class="text-danger"> {{ session('status') }}</li>
		 </ul>
	 @endif
	<h2>Rút Thăm Trúng Thưởng</h2>
		{!! Form::open(array('url' => '/luckdraw/take', 'class' => 'form-horizontal')) !!}
		{{ csrf_field() }}
		<div class="form-group">
			 {!! Form::label('name', 'Nhập mã dự thưởng', array('class' => 'col-sm-3 control-label')) !!}
			 <div class="col-sm-9">
				{!! Form::text('giftcode', '', array('class' => 'form-control giftcode')) !!}
			 </div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-3">
			</div>
			<div class="col-sm-9">
				 <div class="col-sm-offset-2 col-sm-10">
					{!! Form::button('Rút Thăm', array('class' => 'btn btn-success takeluckdraw')) !!}
				 </div>
			 </div>
		</div>
	{!! Form::close() !!}

<!-- small modal -->
<div class="modal fade" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
			Kết quả bốc thăm
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">x</span>
				</button>
			</div>
			<div class="modal-body" id="smallBody">
				<div>				
					<!-- the result to be displayed apply here -->
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(".takeluckdraw").click(function(){
		var giftcode = $(".giftcode").val();
		$.ajax({
			type: "POST",
			url: "{{ url('/luckdraw/take') }}",	
			data: { "_token": "{{ csrf_token() }}", giftcode: giftcode},
			success: function(result) {
				$('#smallModal').modal("show");
				$('#smallBody').html(result.msg).show();
			}
		});
    });
});
</script>
@endsection