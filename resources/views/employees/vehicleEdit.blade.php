@extends('layouts.empbar')

@section('title')
    TS | Edit Vehicle
@endsection

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-12 offset-md-0">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title" style="color: red;">Edit Vehicle</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.update', $vehicle->unitID) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="pic" class="custom-file-upload">
                                <span class="icon"> </span> Upload New Photo
                            </label><hr>
                            <input type="file" name="pic" id="pic" class="form-control @error('pic') is-invalid @enderror" accept="image/*" onchange="displayImage(this)">
                            @error('pic')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                            <img id="picPreview" src="{{ asset('storage/' . $vehicle->pic) }}" alt="Selected Image" style="max-width: 100%; max-height: 200px;">
                        </div>
                        
                        <div class="form-group">
                            <label for="registrationnumber">Registration Number</label>
                            <input type="text" name="registrationnumber" id="registrationnumber" class="form-control @error('registrationnumber') is-invalid @enderror" value="{{ $vehicle->registrationNumber }}" required disabled>
                            @error('registrationnumber')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unitname">Unit/Name</label>
                            <input type="text" name="unitname" id="unitname" class="form-control @error('unitname') is-invalid @enderror" value="{{ $vehicle->unitName }}" required>
                            @error('unitname')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pax">Pax</label>
                            <input type="number" name="pax" id="pax" class="form-control @error('pax') is-invalid @enderror" value="{{ $vehicle->pax }}" required>
                            @error('pax')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="specification">Specifications</label>
                            <textarea name="specification" id="specification" class="form-control @error('specification') is-invalid @enderror" rows="4">{{ $vehicle->specification }}</textarea>
                            @error('specification')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="Available">Available</option>
                                    <option value="Booked">Booked</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                    **{{ $message }}
                                </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="vehicleType">Vehicle Type</label>
                                <select name="vehicleType" id="vehicleType" class="form-control @error('vehicleType') is-invalid @enderror">
                                    @foreach($vehicleTypes as $type)
                                        @if ($type->vehicle_Type_ID == $vehicle->vehicle_Type_ID)
                                            <option value="{{ $type->vehicle_Type_ID }}" selected>{{ $type->vehicle_Type }}</option>
                                        @else
                                            <option value="{{ $type->vehicle_Type_ID }}">{{ $type->vehicle_Type }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('vehicleType')
                                <span class="invalid-feedback" role="alert">
                                    **{{ $message }}
                                </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-2" style="padding: 10px">
                                <a href="{{route ('vehicleTypes.view')}}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add New Type
                                </a>
                            </div>
                            
                        </div>

                        

                        

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Vehicle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function displayImage(input) {
        const picPreview = document.getElementById('picPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                picPreview.src = e.target.result;
                picPreview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            picPreview.style.display = 'none';
        }
    }
</script>
@endsection
