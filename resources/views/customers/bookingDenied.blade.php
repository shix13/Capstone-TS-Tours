@extends('layouts.index')

@section('content')
<div class="container">
<h1 style="font-weight: 700;">Booking Denied</h1>
    <div class="row" style="padding: 10px;">
        <p style="font-weight:700;font-size:20px">Your booking downpayment is subject for refund. </p>
            <p><hr>You can check your email regarding the refund transfer. <br> We are sorry for this inconvenience, it seems there were some unforeseen problems. You can contact our team for any concern or assistance on this matter.  </p> 
        <p style="margin-top:30px;font-weight:700">Thank You for choosing TS Tours Services!</p>
    </div>
    <div class="row">
        <div class="col">
            <h4>Booking Details</h4>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <i class="fas fa-calendar-alt"></i> Schedule Date <hr>
                        <div class="row">
                            <div class="col">
                                Start Date:
                            </div>
                            <div class="col">
                                {{ $bookingData['startDate'] }}
                            </div>
                            <div class="col">
                                End Date:
                            </div>
                            <div class="col">
                                {{ $bookingData['endDate'] }}
                            </div>
                        </div><hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-phone"></i> Phone Number
                    </div>
                    <div class="col">
                        {{ $bookingData['mobileNum']}}
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-map-pin"></i> Pickup Address
                    </div>
                    <div class="col">
                        {{ $bookingData['pickUp_Address']}}
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <i class="fas fa-sticky-note"></i> Additional Notes
                    </div>
                    <div class="col">
                    {{ $bookingData['note']}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 20px">
        <h4>Payment Details</h4>
        <div class="container">
            <div class="row">
                <div class="col">
                    Subtotal {{ $bookingData['subtotal']}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    Downpayment Fee (10%) {{ $bookingData['downpayment_Fee']}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    Gcash Ref. Number {{ $bookingData['gcash_RefNum']}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

