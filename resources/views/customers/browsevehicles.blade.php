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
  @foreach($vehicles as $v)
  <div class="col-md-3" >
      <div class="card" style="width: 18rem;border-radius:10px;height:380px;">
          <div style="max-height: 250px; overflow: hidden;" >
              <img class="card-img-top" src="{{ asset('storage/' . $v->pic) }}" alt="Card image cap" style="width: 100%;" height="auto" >
          </div>
          <div class="card-content" style="height: 220px; display: flex; flex-direction: column; justify-content: space-between;">
            <br>
              <h5 class="card-title" style="font-size: 30px;"><strong>{{ $v->unitName }}</strong></h5>
              <div class="specification">
                  @if ($v->specification)
                      <p style="font-weight: 400; font-size: 15px;">{{ strlen($v->specification) > 100 ? substr($v->specification, 0, 109) . '...' : $v->specification}}</p>
                  @else
                      <p style="font-weight: 400; font-size: 15px;">--No Description--</p>
                  @endif
              </div>
              <div style="justify-content: center">
              <a href="{{ route('bookvehicle', ['vehicle' => $v->unitID]) }}" class="btn btn-primary" style="align-self: flex-end;">Book Now</a>
          </div>
        </div>
      </div>
  </div>
  @endforeach
</div>

</div>
@endsection
