<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Rent;
use App\Models\Tariff;
use App\Models\Vehicle;
use App\Models\VehicleTypeBooked;
use App\Models\VehicleAssigned;
use App\Models\Maintenance;
use App\Models\Remittance;
use App\Models\Feedback;
use Illuminate\Support\Facades\Storage;
use PDF;

class ReportsController extends Controller
{   

    public function processReports() {
    
        return view('employees.reports');
    }
    
    public function fetchData(Request $request) {
        $filter = $request->input('filter');
        $now = now()->startOfDay(); // Start of the current day

        // Define variables to hold the data
        $totalMaintenance = 0;
        $totalRentals = 0;
        $totalBookings = 0;
        $totalRevenue = 0;
        $downpaymentReceived = 0;
        $moneyRemitted = 0;
        $ratingAverage = 0;
        
        if ($filter === '1D') {
            $totalMaintenance = Maintenance::whereDate('created_at', $now)->count();
            $totalRentals = Rent::whereDate('created_at', $now)->count();
            $totalBookings = Booking::whereDate('created_at', $now)->count();
            $totalRevenue = Remittance::whereDate('created_at', $now)->sum('amount') + Booking::whereDate('created_at', $now)->sum('downpayment_fee');
            $downpaymentReceived = Booking::whereDate('created_at', $now)->sum('downpayment_fee');
            $moneyRemitted = Remittance::whereDate('created_at', $now)->sum('amount');
            $ratingAverage = Feedback::whereDate('created_at', $now)->avg('rating');
        } elseif ($filter === '5D') {
            $startDate = $now->subDays(5)->startOfDay();
            $totalMaintenance = Maintenance::whereDate('created_at', '>=', $startDate)->count();
            $totalRentals = Rent::whereDate('created_at', '>=', $startDate)->count();
            $totalBookings = Booking::whereDate('created_at', '>=', $startDate)->count();
            $totalRevenue = Remittance::whereDate('created_at', '>=', $startDate)->sum('amount') + Booking::whereDate('created_at', '>=', $startDate)->sum('downpayment_fee');
            $downpaymentReceived = Booking::whereDate('created_at', '>=', $startDate)->sum('downpayment_fee');
            $moneyRemitted = Remittance::whereDate('created_at', '>=', $startDate)->sum('amount');
            $ratingAverage = Feedback::whereDate('created_at', '>=', $startDate)->avg('rating');
        } elseif ($filter === '1M') {
            $startDate = $now->today()->subMonth()->startOfDay();
            $totalMaintenance = Maintenance::whereBetween('created_at', [$startDate, $now])->count();
            $totalRentals = Rent::whereBetween('created_at', [$startDate, $now])->count();
            $totalBookings = Booking::whereBetween('created_at', [$startDate, $now])->count();
            $totalRevenue = Remittance::whereBetween('created_at', [$startDate, $now])->sum('amount') + Booking::whereBetween('created_at', [$startDate, $now])->sum('downpayment_fee');
            $downpaymentReceived = Booking::whereBetween('created_at', [$startDate, $now])->sum('downpayment_fee');
            $moneyRemitted = Remittance::whereBetween('created_at', [$startDate, $now])->sum('amount');
            $ratingAverage = Feedback::whereBetween('created_at', [$startDate, $now])->avg('rating');
        } elseif ($filter === '6M') {
            $startDate = $now->subMonths(6)->startOfDay();
            $totalMaintenance = Maintenance::where('created_at', '>=', $startDate)->count();
            $totalRentals = Rent::where('created_at', '>=', $startDate)->count();
            $totalBookings = Booking::where('created_at', '>=', $startDate)->count();
            $totalRevenue = Remittance::where('created_at', '>=', $startDate)->sum('amount') + Booking::where('created_at', '>=', $startDate)->sum('downpayment_fee');
            $downpaymentReceived = Booking::where('created_at', '>=', $startDate)->sum('downpayment_fee');
            $moneyRemitted = Remittance::where('created_at', '>=', $startDate)->sum('amount');
            $ratingAverage = Feedback::where('created_at', '>=', $startDate)->avg('rating');
        } elseif ($filter === '1Y') {
            $startDate = $now->subYear()->startOfDay();
            $totalMaintenance = Maintenance::where('created_at', '>=', $startDate)->count();
            $totalRentals = Rent::where('created_at', '>=', $startDate)->count();
            $totalBookings = Booking::where('created_at', '>=', $startDate)->count();
            $totalRevenue = Remittance::where('created_at', '>=', $startDate)->sum('amount') + Booking::where('created_at', '>=', $startDate)->sum('downpayment_fee');
            $downpaymentReceived = Booking::where('created_at', '>=', $startDate)->sum('downpayment_fee');
            $moneyRemitted = Remittance::where('created_at', '>=', $startDate)->sum('amount');
            $ratingAverage = Feedback::where('created_at', '>=', $startDate)->avg('rating');
        } elseif ($filter === 'MAX') {
            $totalMaintenance = Maintenance::count();
            $totalRentals = Rent::count();
            $totalBookings = Booking::count();
            $totalRevenue = Remittance::sum('amount') + Booking::sum('downpayment_fee');
            $downpaymentReceived = Booking::sum('downpayment_fee');
            $moneyRemitted = Remittance::sum('amount');
            $ratingAverage = Feedback::avg('rating');
        }
        

        // Call the method for the second filter and merge its results
        $secondFilterData = $this->fetchDataSecondFilter($request, $filter);
       // Call the getTopVehicleAssignments function with filter and now parameters
        $topVehicleAssignments = $this->getTopVehicleAssignments($filter, $now);
        
    // Merge the data from both filters
    $response = [
        'totalMaintenance' => $totalMaintenance,
        //'maintenancePercentage' => $maintenancePercentage,
        'totalRentals' => $totalRentals,
        //'rentalsPercentage' => $rentalsPercentage,
        'totalBookings' => $totalBookings,
        //'bookingPercentage' => $bookingPercentage,
        'totalRevenue' => number_format($totalRevenue, 2),
        'downpaymentReceived' => number_format($downpaymentReceived, 2),
        'moneyRemitted' => number_format($moneyRemitted, 2),
        'secondFilterData' => $secondFilterData, // Include the second filter data
        'topVehicleAssignments' => $topVehicleAssignments, // Include top vehicle assignments
        'filter' => $filter,
    ];
    // Check if $ratingAverage is numeric
    if (is_numeric($ratingAverage)) {
        $response['ratingAverage'] = number_format($ratingAverage, 2);
    } else {
        $response['ratingAverage'] = 'No Rating Available'; // Replace with your desired text
    }
    
       
        return response()->json($response);
    }

    public function fetchDataSecondFilter(Request $request, $filter) {
        $filter = $request->input('filter');
           // dd($filter);
        $now = now(); // Get the current date and time
        $now->startOfDay();
        
        $topLocations = Booking::select('tariffs.location')
            ->selectRaw('COUNT(*) as booking_count')
            ->join('tariffs', 'booking.tariffID', '=', 'tariffs.tariffID')
            ->where(function ($query) use ($filter, $now) {
                if ($filter === '1D') {
                    $query->where('booking.created_at', '>=', $now->today());
                } elseif ($filter === '5D') {
                    $query->where('booking.created_at', '>=', $now->today()->subDays(5));
                } elseif ($filter === '1M') {
                    $query->where('booking.created_at', '>=', $now->today()->subMonth());
                } elseif ($filter === '6M') {
                    // Filter for the last 6 months
                    $query->where('booking.created_at', '>=', $now->today()->subMonths(6));
                } elseif ($filter === '1Y') {
                    // Filter for the last 1 year
                    $query->where('booking.created_at', '>=', $now->today()->subYear());
                }
            })
            ->groupBy('tariffs.location')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get();

        $mergedTopLocations = [];

        foreach ($topLocations as $location) {
            $locationName = $location->location;
            $bookingCount = $location->booking_count;

            // Generate a unique key for each location
            $key = $locationName;

            // If the key exists in the merged result, update the count
            if (isset($mergedTopLocations[$key])) {
                $mergedTopLocations[$key] += $bookingCount;
            } else {
                // If the key doesn't exist in the merged result, create an entry
                $mergedTopLocations[$key] = $bookingCount;
            }
        }

    
        $topVehicleAssignments = VehicleAssigned::select('unitName', 'registrationNumber', 'vehicles_Assigned.unitID', \DB::raw('COUNT(*) as assignment_count'))
        ->join('vehicles', 'vehicles_Assigned.unitID', '=', 'vehicles.unitID')
        ->where(function ($query) use ($filter, $now) {
            if ($filter === '1D') {
                // Filter for the last 1 day
                $query->where('vehicles_Assigned.created_at', '>=', $now->today());
            } elseif ($filter === '5D') {
                // Filter for the last 5 days
                $query->where('vehicles_Assigned.created_at', '>=', $now->subDays(5));
            } elseif ($filter === '1M') {
                // Filter for the last 1 month
                $query->where('vehicles_Assigned.created_at', '>=', $now->subMonth());
            } elseif ($filter === '6M') {
                // Filter for the last 6 months
                $query->where('vehicles_Assigned.created_at', '>=', $now->subMonths(6));
            } elseif ($filter === '1Y') {
                // Filter for the last 1 year
                $query->where('vehicles_Assigned.created_at', '>=', $now->subYear());
            } 
        })
        ->where('vehicles.status', 'active')
        ->groupBy('unitName', 'registrationNumber', 'vehicles_Assigned.unitID')
        ->orderBy('assignment_count', 'desc')
        ->limit(10)
        ->get();
    
            
        $mergedTopVehicles = $topVehicleAssignments->pluck('registrationNumber')->toArray();
    
        $vehicleData = Vehicle::whereIn('registrationNumber', $mergedTopVehicles)->get();
            
        $data = [
            'topLocations' => $mergedTopLocations,
            'topVehicles' => $topVehicleAssignments,
        ];
        
        return $data;
    }    

    public function getTopVehicleAssignments($filter, $now)
    {
        $topVehicleAssignments = Vehicle::select('unitName', 'registrationNumber', 'vehicles.unitID')
        ->selectSub(function ($query) use ($filter, $now) {
            $query->selectRaw('COUNT(*)')
                ->from('vehicles_assigned')
                ->whereColumn('vehicles.unitID', 'vehicles_assigned.unitID')
                ->where(function ($subQuery) use ($filter, $now) {
                    if ($filter === '1D') {
                        $subQuery->where('created_at', '>=', $now->today());
                    } elseif ($filter === '5D') {
                        $subQuery->where('created_at', '>=', $now->today()->subDays(5));
                    } elseif ($filter === '1M') {
                        $subQuery->where('created_at', '>=', $now->today()->subMonth());
                    } elseif ($filter === '6M') {
                        $subQuery->where('created_at', '>=', $now->today()->subMonths(6));
                    } elseif ($filter === '1Y') {
                        $subQuery->where('created_at', '>=', $now->today()->subYear());
                    }
                });
        }, 'assignment_count')
        ->selectSub(function ($query) use ($filter, $now) {
            $query->selectRaw('COUNT(*)')
                ->from('maintenances')
                ->whereColumn('vehicles.unitID', 'maintenances.unitID')
                ->where(function ($subQuery) use ($filter, $now) {
                    if ($filter === '1D') {
                        $subQuery->where('created_at', '>=', $now->today()->today());
                    } elseif ($filter === '5D') {
                        $subQuery->where('created_at', '>=', $now->today()->subDays(5));
                    } elseif ($filter === '1M') {
                        $subQuery->where('created_at', '>=', $now->today()->subMonth());
                    } elseif ($filter === '6M') {
                        $subQuery->where('created_at', '>=', $now->today()->subMonths(6));
                    } elseif ($filter === '1Y') {
                        $subQuery->where('created_at', '>=', $now->today()->subYear());
                    }
                });
        }, 'maintenance_count')
        ->where('vehicles.status', 'active')
        ->orderBy('assignment_count', 'desc')
        ->get();
        //dd($now, $now->today(),$now->today()->subDays(5),$now->today()->subMonth(),$now->today()->subMonth(6),  $now->today()->subYear());
        //dd($topVehicleAssignments);
        return [
            'topVehicleAssignments' => $topVehicleAssignments,
        ];
    }
    
    

    public function generatePDF(Request $request)
{    
    // Check if the 'data' parameter exists in the request
    if ($request->has('data')) {
        // Get the 'data' parameter value
        $dataParam = $request->input('data');
        
        // Decode and parse the data from the URL parameter
        $data = json_decode(urldecode($dataParam), true);

        // Format the current date and time
        $now = now();
        $nowFormatted = $now->format('Y-m-d H:i:s');

        // Combine the formatted date and time with the filename
        $filename = "TSReport_$nowFormatted.pdf";

        // Load the 'GenerateReport' view with the data
        $pdf = PDF::loadView('pdf.GenerateReport', compact('data'));

        return $pdf->stream($filename);
    } else {
        // 'data' parameter is not present in the request
        return response()->json(['error' => 'Data not provided in the request'], 400);
    }
}

}
