@extends('layouts.main')


@section('content')
	@if ($ngTemplate)
		<script type="text/ng-template" id="{{ $ngTemplate }}">
			@include($ngTemplate)
		</script>
	@endif
@endsection