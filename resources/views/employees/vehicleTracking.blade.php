@extends('layouts.empbar')

@section('title')
    TS | Vehicle Tracking
@endsection

@section('content')
<br>
<br>
<div class="container">
    <h3>Vehicle Tracking</h3>
    @if(!$activeTasks->isEmpty())
        <div class="row">
        @foreach($activeTasks as $activeTask)
            @foreach($activeTask->assignments as $assignment)
                    <div class="col-md-4">
                    @php
                        $vehicle = $assignment->vehicle;
                    @endphp
                    <div class="card custom-card mb-4" style="height:220px;">
                        <div class="row g-0">
                            <!--div class="col-md-4">
                                <img src="{{ asset('storage/' . $vehicle->pic) }}" style="width: 100%; height: 100%; object-fit: cover;" class="img-fluid rounded-start" alt="Vehicle Image">
                            </div-->
                            <div class="col">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $vehicle->unitName }} - {{ $vehicle->registrationNumber }}</h5>
                                    <p class="card-text">
                                        Driver: {{ $assignment->employee->firstName }} {{ $assignment->employee->lastName }}<br/>
                                        BookingID: {{ $assignment->rent->reserveID }}<br/>
                                        <hr>
                                        <div class="geolocationData" data-assignment-id="{{ $assignment->assignedID }}">
                                            @php
                                                $location = $assignment->geolocation()->latest('created_at')->first();
                                            @endphp
                                            @if($location)
                                                @php
                                                    $dateString = $location->created_at;
                                                    
                                                    $dateCarbon = \Carbon\Carbon::parse($dateString);
                                                    $date = $dateCarbon->format('F, j, Y g:i A');
                                                @endphp
                                                Latitude: {{ $location->latitude }} <br/>
                                                Longitude: {{ $location->longitude }} <br/> 
                                                Last Sent: {{ $date }}<br/>
                                            @else
                                                Location not yet sent
                                            @endif
                                        </div>
                                    </p>
                                </div>    
                            </div>
                        </div>
                    </div>
                    </div>
            @endforeach
        @endforeach
        </div>
    @else
        <h3 style="color:black; text-align:center;">There are currently no active vehicles.</h3>
    @endif
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateGeolocationData(assignmentId) {
    var geolocationContainer = $(`[data-assignment-id="${assignmentId}"]`);

    $.ajax({
        url: "{{ route('api.getLatestGeolocation') }}",
        method: "GET",
        data: { assignmentId: assignmentId }, // Send the assignment ID to the API
        success: function(data) {
            if (data.data) {
                var location = data.data;
                var dateCarbon = new Date(location.created_at);

                var content = 'Latitude: ' + location.latitude + '<br/>' +
                            'Longitude: ' + location.longitude + '<br/>' +
                            'Last Sent: ' + dateCarbon.toLocaleString() + '<br/>';

                geolocationContainer.html(content);
                console.log("update!");
            } else {
                geolocationContainer.html('Location not yet sent');
            }
        }
    });
}

// Call updateGeolocationData for each assignment individually
$('.geolocationData').each(function() {
    var assignmentId = $(this).data('assignment-id');
    setInterval(function() {
        updateGeolocationData(assignmentId);
    }, 10000); // Adjust the interval as needed
});
</script>
@endsection