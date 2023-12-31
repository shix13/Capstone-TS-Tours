<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleTypeBooked;
use App\Models\Tariff;
use App\Models\Booking;
use App\Models\Rent;
use App\Models\Feedback;
use Illuminate\Support\Facades\Storage;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;
use App\Mail\FeedbackReceived;
use Illuminate\Support\Facades\Auth;


class TestController extends Controller
{
    public function getVehicles(){
        $vehicleTypes = VehicleType::all();

        return view('tests.selectvehicles', compact('vehicleTypes'));
    }

    public function proceedBooking(Request $request){
        $selectedVehicleTypes = $request->input('selectedVehicleTypes');
    
        // Convert the comma-separated string to an array
        $selectedVehicleTypes = explode(',', $selectedVehicleTypes);
    
        $vehicleTypes = [];
        $smallestPaxCounts = [];
    
        foreach ($selectedVehicleTypes as $selectedType) {
            $vehicleType = VehicleType::find($selectedType); // Assuming your model is named VehicleType
            if ($vehicleType) {
                $vehicleTypes[] = $vehicleType;
    
                // Get the smallest pax count for the current vehicle type directly from the database
                $smallestPaxCount = Vehicle::where('vehicle_Type_ID', $vehicleType->vehicle_Type_ID)->min('pax');
                $smallestPaxCounts[$vehicleType->vehicle_Type_ID] = $smallestPaxCount; 
            }
        }

        $tariffData = Tariff::all();
        return view('tests.createbooking', compact('vehicleTypes', 'tariffData', 'smallestPaxCounts'));
    }
    
    

    public function processBooking(Request $request)
    {  
        // Validate your request as needed...
        $location = $request->input('location');
        $tariff = Tariff::where('location', 'LIKE', "%{$location}%")->first();

        if (!$tariff) {
            return redirect()->back()->with('error', 'Tariff not found for the specified location.');
        }

        $bookingType = $request->input('bookingType');
        $startDate = $request->input('StartDate');
        $pickupTime = $request->input('PickupTime');

        $startDateTime = Carbon::parse($startDate . ' ' . $pickupTime);
        $formattedStartDate = $startDateTime->format('Y-m-d H:i:s');

        $EndDate = $request->input('EndDate');

        // Create a DateTime object from the string
        $endTime = new \DateTime($EndDate);

        // Set time to 12:00:00 PM
        $endTime->setTime(12, 0, 0);

        $formattedEndDate = $endTime->format('Y-m-d H:i:s');

       
        $numVehiclesAssigned = 0;

        foreach ($request->input('TypeQuantity') as $vehicleTypeId => $quantity) {
            $numVehiclesAssigned += $quantity;
        }

        if ($bookingType === 'Rent') {
            $subtotal = $request->input('subtotalInput');
        } elseif ($bookingType === 'Pickup/Dropoff') {
            $subtotal = $tariff->do_pu;
            $formattedStartDate = Carbon::parse($startDate . ' ' . $pickupTime)->format('Y-m-d H:i:s');
           
        } else {
            return redirect()->back()->with('error', 'Invalid booking type.');
        }
      
        $downpayment = 0; // Calculate downpayment logic can be added here if needed

        $booking = new Booking([
            'cust_first_name' => $request->input('FirstName'),
            'cust_last_name' => $request->input('LastName'),
            'cust_email' => $request->input('Email'),
            'bookingType' => $bookingType,
            'tariffID' => $tariff->tariffID,
            'mobileNum' => $request->input('MobileNum'),
            'pickUp_Address' => $request->input('PickUpAddress'),
            'pax' => $request->input('Pax'),
            'note' => $request->input('Note'),
            'downpayment_Fee' => $downpayment,
            'subtotal' => $subtotal,
            'status' => 'Pending',
            'startDate' => $formattedStartDate,
            'endDate' => $formattedEndDate,
            // Include other necessary attributes here...
        ]);

        $booking->save();

        // Process vehicle types booked (assuming 'TypeQuantity' is an array of vehicle type IDs and quantities)
        foreach ($request->input('TypeQuantity') as $vehicleTypeId => $quantity) {
            $vehicleTypeBooked = new VehicleTypeBooked([
                'vehicle_Type_ID' => $vehicleTypeId,
                'quantity' => $quantity,
                'reserveID' => $booking->reserveID,
                // Include other necessary attributes here...
            ]);
            $vehicleTypeBooked->save();
        }

        // Send booking confirmation email
        try {
            Mail::to($booking->cust_email)->send(new BookingConfirmation($booking->toArray()));
        } catch (\Exception $e) {
           
            return redirect()->back()->with('error', 'An error occurred while sending the booking confirmation email.');
        }

        return redirect()->route('checkbookingstatus', ['booking' => $booking])->with('success', 'Your booking details have been saved successfully');
    }
    

    public function processRate($tariffRate, $startDate, $endDate, $numVehiclesAssigned){
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $numberOfDays = $end->diffInDays($start);
        
        //$subtotal = $numberOfDays * $tariffRate;
        $subtotal = $tariffRate * ($numberOfDays + 1) * $numVehiclesAssigned; // Add 1 to include both the start and end dates

        return $subtotal;
    }

    /*public function processDownpayment($subtotal){
        $downpayment = $subtotal * 0.10;

        return $downpayment;
    }*/

    public function searchView(){
        return view('tests.searchbooking');
    }

    public function processSearch(Request $request){
      
        $reserveID = $request->input('reserveID');
    $mobile = $request->input('mobile_number');

    // Check if the reserveID and mobile match in the Booking model
    $booking = Booking::where('reserveID', $reserveID)
        ->where('mobileNum', $mobile)
        ->first();

    if ($booking) {
        return redirect()->route('checkbookingstatus', ['booking' => $booking,'reserveID' => $reserveID, 'mobile' => $mobile]);
    } else {
        return redirect()->route('search')->with('error', 'Booking not found, check the details you inputted');
    }
    }
    

    public function bookingStatus(Booking $booking){
        $vehicleTypesBooked = $booking->vehicleTypesBooked;
        $vehiclesAssigned = $booking->vehiclesAssigned;
        if ($vehiclesAssigned->isEmpty()) {
            $vehiclesAssigned = null;
        }
        //dd($vehicleTypesBooked);
        $tariffs = $booking->tariff;
        //dd($booking);
        //dd($type);
        return view('tests.bookingstatus', compact('booking', 'vehicleTypesBooked', 'tariffs', 'vehiclesAssigned'));
    }

    public function checkout(Request $request){
        //dd($request->input('bookingID'));
        $booking = Booking::where('reserveID', $request->input('bookingID'))->first();
        ($request);

        $booking->gcash_RefNum = $request->input('gcash_RefNum');
        $booking->downpayment_Fee = $request->input('amount');
        
        $booking->save();
        return redirect()->route('checkbookingstatus', $booking);
    }

    public function sendEmail(Request $request)
{
    $data = [
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone' => $request->input('phone'),
        'message' => $request->input('message'),
    ];

    try {
        // Send email
        Mail::to('tstoursduma@gmail.com')->send(new ContactUsMail($data));
        
        // Redirect back with success message
        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    } catch (\Exception $e) {
        // Handle email sending error
        return redirect()->back()->with('error', 'An error occurred while sending the email. Please try again later.');
    }
}


    public function feedback($id)
    {
        // Retrieve the feedback record based on the provided ID
        $feedback = Rent::find($id);

        if (!$feedback) {
            // Handle the case where the feedback record is not found (e.g., show an error)
            return view('errors.404'); // You can create an error view
        }

        return view('tests.feedback', ['feedback' => $feedback]);
    }

    public function store(Request $request)
{
    // Validate the form data
    $this->validate($request, [
        'rentID' => 'required', // Update the validation rule for TrackingID
        'rating' => 'required|integer|min:1|max:5', // Add validation rules for rating
        'feedback_Message' => 'required',
    ]);
  

    // Attempt to create a new feedback record in the database
    try {
        $rent = Rent::find($request->input('rentID'));

        $feedback = Feedback::create([
            'rentID' => $request->input('rentID'), // Assuming rentID corresponds to reserveID
            'rating' => $request->input('rating'), // Save the rating
            'feedback_Message' => $request->input('feedback_Message'),
        ]);
        
        $companyEmail = 'tstoursduma@gmail.com';
        Mail::to($companyEmail)->send(new FeedbackReceived($feedback, $rent));

        return redirect()->back()->with('status', 'Feedback sent successfully.');
    } catch (\Exception $e) {
        // Handle any errors that occur during the creation of the feedback record
        return back()->withErrors(['error' => 'An error occurred while sending feedback. Please try again.']);
    }
}




}
