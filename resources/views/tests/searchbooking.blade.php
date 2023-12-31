@extends('layouts.index')

@section('content')
<div class="container1">
    <img src="{{ asset('images/search-booking.png') }}" style="border-radius: 10px;">
    <hr>
    <form method="POST" action="{{ route('searchbooking') }}">
        @csrf
        <br>
        @if(session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif
        <div class="col-md-6 mx-auto" style="font-size: 20px">
            <label for="reserveID">Booking Number:</label>
            <input name="reserveID" type="text" class="form-control" placeholder="Enter Booking Number" style="background: white; padding: 20px; border: 2px solid midnightblue;">
        </div>
        <br>
        <div class="col-md-6 mx-auto"  style="font-size: 20px">
            <label for="mobile_number">Mobile Number:</label>
            <input name="mobile_number" type="tel" class="form-control" placeholder="To make sure it's you, enter the mobile number you provided" style="background: white; padding: 20px; border: 2px solid midnightblue;">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    </form>
</div>
@endsection
