@extends('layout.app')
@section('style')
@vite(['resources/css/dashboard.css'])
@endsection
@section('title','Create account')
@section('content')
<main class="auth-page">
	<form class="auth-form" method="POST" action="{{ route('register') }}">
		@csrf
		<h1>Create account</h1>

		<label for="name">Name</label>
		<input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
		@error('name')
			<p class="form-error">{{ $message }}</p>
		@enderror

		<label for="email">Email</label>
		<input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
		@error('email')
			<p class="form-error">{{ $message }}</p>
		@enderror

		<label for="role">Account type</label>
		<select id="role" name="role" required>
			<option value="cashier" @selected(old('role') === 'cashier')>Cashier</option>
			<option value="staff" @selected(old('role', 'staff') === 'staff')>Staff</option>
		</select>
		@error('role')
			<p class="form-error">{{ $message }}</p>
		@enderror

		<label for="password">Password</label>
		<input id="password" name="password" type="password" required autocomplete="new-password">
		@error('password')
			<p class="form-error">{{ $message }}</p>
		@enderror

		<label for="password_confirmation">Confirm password</label>
		<input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

		<button type="submit">Create account</button>
		<p>Already registered? <a href="{{ route('login') }}">Sign in</a></p>
	</form>
</main>
@endsection