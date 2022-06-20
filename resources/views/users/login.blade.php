@extends('layouts.default')

@section('title')
Login page
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
	<h2>Login</h2>
		{!! Form::open(array('url' => '/users/login', 'class' => 'form-horizontal')) !!}
		{{ csrf_field() }}
		<div class="form-group">
			 {!! Form::label('name', 'Email', array('class' => 'col-sm-3 control-label')) !!}
			 <div class="col-sm-9">
				{!! Form::text('email', '', array('class' => 'form-control')) !!}
			 </div>
		</div>
		<div class="form-group">	
			{!! Form::label('name', 'Password', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-9">
				{!! Form::input('password', 'password', '', array('class' => 'form-control')) !!}
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-3">
			</div>
			<div class="col-sm-9">
				<div class="checkbox">
					<label><input type="checkbox"> remenber</label>
				</div>
				 <div class="col-sm-offset-2 col-sm-10">
					{!! Form::submit('Login', array('class' => 'btn btn-success')) !!}
				 </div>
			 </div>
		</div>
	{!! Form::close() !!}
@endsection