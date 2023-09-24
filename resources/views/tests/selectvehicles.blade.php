@extends('layouts.index')

@section('content')

<div class="container1" style="padding: 20px;background:none;box-shadow:none;width:100%">
  
  
  <!-- Add search and filter section on the right side -->
  <div class="row">
  <!-- Browse Vehicle text on the left -->
  <div class="col-md-6">
    <h1 style="text-align: left; padding-left: 30px; font-size: 30px; font-weight: 700">Vehicles We Offer</h1>
  </div>

  <!-- Search bar on the right -->
  <div class="col-md-6">
    <div class="input-group mb-3">
      <input type="text" class="form-control" style="background: white;border-radius:10px" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2">
      <div class="input-group-append">
        <button style="background: midnightblue;padding:10px;color:white;border-radius:5px;"> <i class="fas fa-search"></i> Search</button>
      </div>
    </div>
  </div>
</div>

<hr>
<div class="row" >
  @foreach($vehicleTypes as $v)
    <div class="col-md-3" >
        <div class="vehicle-card" style="width: 18rem;border-radius:10px;height:380px;background-color:white"
            data-id="{{ $v->id }}">

            <div style="max-height: 250px; overflow: hidden;" >
                <img class="card-img-top" src="" alt="Card image cap" style="width: 100%;" height="auto" >
            </div>
            <div class="card-content" style="height: 220px; display: flex; flex-direction: column; justify-content: space-between;">
                <br>
                <h5 class="card-title" style="font-size: 30px;"><strong>{{ $v->vehicle_Type }}</strong></h5>
            </div>
        </div>
    </div>
  @endforeach
</div>
<form id="vehicleSelectionForm" action="#" method="POST">
    @csrf
    <input type="hidden" id="selectedVehicleTypes" name="selectedVehicleTypes" value="">
    <button type="button" id="submitSelectionButton">Submit Selection</button>
</form>

</div>
<script>
// JavaScript to handle vehicle selection
const selectedVehicleTypes = [];

document.querySelectorAll('.vehicle-card').forEach(card => {
    card.addEventListener('click', () => {
        const vehicleId = card.getAttribute('data-id');

        // Toggle selection
        if (selectedVehicleTypes.includes(vehicleId)) {
            selectedVehicleTypes.splice(selectedVehicleTypes.indexOf(vehicleId), 1);
            card.style.backgroundColor = 'white'; // Deselect
        } else {
            selectedVehicleTypes.push(vehicleId);
            card.style.backgroundColor = 'lightgreen'; // Select
        }
    });
});

document.getElementById('submitSelectionButton').addEventListener('click', () => {
    // Update the hidden form field with selected data
    document.getElementById('selectedVehicleTypes').value = selectedVehicleTypes.join(',');

    // Submit the form
    document.getElementById('vehicleSelectionForm').submit();
});
</script>
@endsection
