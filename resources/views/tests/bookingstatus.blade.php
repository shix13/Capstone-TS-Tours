@extends('layouts.index')

@section('content')
<div class="container">
<div id="pendingMessage" style="display: none;">
    <h1 style="font-weight: 700;text-align:center">Booking Under Review</h1>
    <!--div class="row" style="padding: 10px;"-->
        <p style="font-weight:700;font-size:20px">Your booking is currently under review. </p>
        <p><hr>You can check your email regarding the confirmation of your booking. <br>Advisory Letter(?) Cancellation of bookings can only take place up until 3 days before pickup date and time and is non-refundable.</p>
        <p style="margin-top:30px;font-weight:700">Thank You for choosing TS Tours Services!</p>
    <!--/div-->
</div>

<div id="deniedMessage" class="row" style="display: none;padding: 10px;">
    <h1 style="font-weight: 700;">Booking Denied</h1>
    <div class="row" style="padding: 10px;">
        <p style="font-weight:700;font-size:20px">Your booking has been denied. </p>
            <p><hr>We are sorry for this inconvenience, it seems there were some unforeseen problems. You can contact our team for any concern or assistance on this matter.</p>
        <p style="margin-top:30px;font-weight:700">Thank You for choosing TS Tours Services!</p>
    </div>
</div>

<div id="preApprovedMessage" class="row" style="display: none;">
    <h1 style="font-weight: 700;text-align: center;">Booking Approved</h1>
    <div class="row" style="padding: 10px;">
        <p style="font-weight:700;font-size:20px">Your booking has been approved. </p>
        <p><hr>You can check your email regarding the confirmation of your booking. <br>Advisory Letter(?) Cancellation of bookings can only take place up until 3 days before pickup date and time and is non-refundable.</p>
        <p style="margin-top:30px;font-weight:700">Thank You for choosing TS Tours Services!</p>
    </div>
</div>

<div id="paymentForm" class="row" style="display: none;padding: 10px;">
<div class="row container1" style="text-align: justify; width: 95%;padding:10px 80px">
        <div class="row">
            <div class="col-md-8">
                <h2 style="margin-bottom: 0px;margin-top:20px"><strong>Secure Payment</strong></h2>
                <div class="container">
                    <div class="row">
                        <div class="col">
                            Subtotal: {{ $booking['subtotal']}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            Downpayment Fee (10%): {{ $booking['downpayment_Fee']}}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('checkout') }}">
                        @csrf
                        Enter GCASH reference number: <input name="gcash_RefNum" required>
                            <div class="col text-center"> 
                                <button type="submit" class="btn btn-primary"  style="margin-top: 50px">Confirm Payment</button>
                            </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 ">
                <!-- Add your image here -->
                <img src="{{ asset('/storage/images/gcash.jpg') }}" alt="Your Image" style="width: 100%;margin-left:0%">
            </div>
        </div>
    </div>
</div>

<div class="row">
        <div class="col">
            <h4>Booking Details</h4>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <i class="fas fa-calendar-alt"></i> Vehicles booked <hr>
                        <div class="row">
                            @foreach($vehicleTypesBooked as $vehicleTypeBooked)
                                @php
                                
                                    $vehicleType = $vehicleTypeBooked->vehicleType;
                                    $type = $vehicleType->vehicle_Type;
                                    
                                @endphp

                                <p>Type: {{ $type }}<br>
                                Quantity: {{ $vehicleTypeBooked->quantity }}</p>
                            @endforeach
                        </div><hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-calendar-alt"></i> Schedule Date <hr>
                        <div class="row">
                            <div class="col">
                                Start Date:
                            </div>
                            <div class="col">
                                {{ $booking['startDate'] }}
                            </div>
                            <div class="col">
                                End Date:
                            </div>
                            <div class="col">
                                {{ $booking['endDate'] }}
                            </div>
                        </div><hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-phone"></i> Phone Number
                    </div>
                    <div class="col">
                        {{ $booking['mobileNum']}}
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-map-pin"></i> Pickup Address
                    </div>
                    <div class="col">
                        {{ $booking['pickUp_Address']}}
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-sticky-note"></i> Additional Notes
                    </div>
                    <div class="col">
                    {{ $booking['note']}}
                    </div>
                </div>
            </div>
        </div>
</div>
</div>
<script>
    // Assuming you have a variable or way to determine the booking status
    var bookingStatus = "{{ $booking->status }}"; // Change this to the actual booking status

    // Show the appropriate message based on the booking status
    if (bookingStatus === "Pending") {
        document.getElementById("pendingMessage").style.display = "flex";
    } else if (bookingStatus === "Denied") {
        document.getElementById("deniedMessage").style.display = "flex";
    } else if (bookingStatus === "Pre-approved") {
        document.getElementById("preApprovedMessage").style.display = "flex";
        document.getElementById("paymentForm").style.display = "flex";
    }
</script>

@endsection
