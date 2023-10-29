<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleAssigned;
use App\Models\Rent;
use GuzzleHttp\Client;

class VehicleTracking extends Controller
{
    //
    public function vehicleIndex(){
        $activeTasks = Rent::whereIn('rent_Period_Status', ['Ongoing'])
        ->with(['assignments.geolocation'])
        ->get();

        foreach($activeTasks as $activeTask){
            $assignments = $activeTask->assignments;
        }
        
        return view('employees.vehicleTracking', compact('activeTasks', 'assignments'));
    }

    public function getUpdatedData() {
        // Your logic to fetch updated data here
        // This can be similar to your "vehicleIndex" method, but return JSON data instead
        $activeTasks = Rent::whereIn('rent_Period_Status', ['Ongoing'])
        ->with(['assignments.geolocation'])
        ->get();; // Retrieve the updated data
    
        foreach($activeTasks as $activeTask){
            $assignments = $activeTask->assignments;
        }

        return response()->json(['data' => $assignments]);
    }    

    /*
    // Set up the Guzzle client
    $client = new Client();
    $apiKey = env('MAPQUEST_API_KEY');; // Replace with your MapQuest API key
    foreach($assignments as $assignment) {
        $geolocation = $assignment->geolocation()->latest('created_at')->first();
        
        if($geolocation){
            // Extract latitude and longitude
            $latitude = $geolocation->latitude;
            $longitude = $geolocation->longitude;
                
            /*
            // Make a request to MapQuest API to convert geocode into an address
            $response = $client->request('GET', "https://www.mapquestapi.com/geocoding/v1/reverse", [
                'query' => [
                    'key' => $apiKey,
                    'location' => $latitude . ',' . $longitude,
                ],
            ]);

            // Process the response and get the data you need
            $locationData = json_decode($response->getBody());
            
            // Collect the data you need, including assignmentID, unitID, and the converted address
            $geolocationData[] = [
                'assignmentID' => $assignment->assignmentID,
                'unitID' => $assignment->unitID,
                'address' => $locationData->results[0]->locations[0]->street . ', ' . $locationData->results[0]->locations[0]->adminArea6 . ', ' . $locationData->results[0]->locations[0]->adminArea5 . ', ' . $locationData->results[0]->locations[0]->adminArea4 // Adjust this to match your response structure
            ];
            
        }
        else{
            // Collect the data you need, including assignmentID, unitID, and the converted address
            $geolocationData[] = [
                'assignmentID' => $assignment->assignmentID,
                'unitID' => $assignment->unitID,
                'address' => 'No location sent'
            ];
        }
    }
    */
}
