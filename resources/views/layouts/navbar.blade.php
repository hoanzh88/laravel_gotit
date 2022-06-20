 <div class="example">
	<div id="header">
		<nav class="navbar navbar-inverse">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="" class="navbar-brand">Got It</a>
			</div>

			<div class="navbar-collapse collapse" id="menu">
				<ul class="nav navbar-nav">
					<li><a href="">Trang chủ</a></li>
					<li><a href="">Giới thiệu</a></li>
					<li><a href="">Liên hệ</a></li>
					@if(Auth::check())
					<li><a href="luckdraw/">Rút Thăm Trúng Thưởng</a></li>
					@endif
				</ul>
				<ul class="nav navbar-nav navbar-right">
				@guest
					<li><a href="/users/login">Login</a></li>
				@else
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
						   Xin Chào:  {{ Auth::user()->name }}
						</a>		
					</li>
				@endif	
			</ul>
			</div>
		</nav>
	</div>			
</div>