@extends('layouts.empbar')

@section('title')
    TS | Accounts
@endsection

@section('content')
<br>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <a href="{{ route('employee.register') }}" class="btn btn-success"><i class="fas fa-plus"></i> Create Account</a>
        </div>
        <div class="col-md-8">
            <div class="form-row" style="background-color: hsla(0, 0%, 100%, 0.7); padding: 10px; margin-right: -180px; border-radius: 5px; margin-bottom: 20px;">
                <div class="form-group col-md-8">
                    <input type="text" id="nameSearch" class="form-control" placeholder="Enter Name">
                </div>
                <div class="form-group col-md-4">
                    <select id="companyRoleFilter" class="form-control">
                        <option value="All">All Roles</option>
                        <option value="Manager">Manager</option>
                        <option value="Clerk">Clerk</option>
                        <option value="Driver">Driver</option>
                        <option value="Mechanic">Mechanic</option>
                        <option value="Driver Outsourced">Driver Outsourced</option>
                        <option value="Mechanic Outsourced">Mechanic Outsourced</option>
                    </select>
                </div>
            </div>
                  
        </div>
    </div>


    @if(session('success'))
        <div class="custom-success-message alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
        @endif

        @if(session('error'))
        <div class="custom-error-message alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
        @endif
        


    <div id="employeeCard" class="card">
        <div class="card-header">
            <h4 class="card-title">Employee Accounts</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="emptable" class="table table-hover table-striped">
                    <thead class="text-primary font-montserrat">
                        <th class="bold-text">
                            <strong>#</strong>
                        </th>
                        <th class="bold-text">
                            <strong>Profile</strong>
                        </th>
                        <th class="bold-text">
                            <strong>Role</strong>
                        </th>
                        <th class="bold-text">
                            <strong>Mobile Number</strong>
                        </th>
                        <th class="bold-text">
                            <strong>Email</strong>
                        </th>
                        <th class="bold-text">
                            <strong>Action</strong>
                        </th>
                    </thead>
                    <tbody>
                        @php
                            $counter = 1; // Initialize a counter variable
                        @endphp

                        @if ($employees !== null && $employees->count() > 0)
                        @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td class="text-center col-md-3">
                                @if ($employee->profile_img)
                                    <img src="{{ asset('storage/' . $employee->profile_img) }}" alt="Profile Image" width="200"> <br>
                                <span style="font-size: 20px"> <strong>  {{ $employee->firstName }} {{ $employee->lastName }} </strong> </span>
                                @else
                                    <!-- If there is no profile image, display a default image -->
                                     <img src="{{ asset('images/TsTours.jpg') }}" alt="Default Image" width="200"> <br>
                                   <span style="font-size: 20px"><strong> {{ $employee->firstName }} {{ $employee->lastName }} </strong></span> 
                                @endif
                            </td>
                            <td style="font-size:18px;text-align:center">{{ $employee->accountType }}</td>
                            <td style="font-size:16px;text-align:center">{{ $employee->mobileNum }}</td>
                            <td style="font-size:16px;text-align:center">{{ $employee->email }}</td>
                            <td class="text-center">
                                <a href="{{ route('employee.edit', $employee->empID) }}" class="btn btn-primary" style="padding: 10px 32px;margin:5px"><i class="fas fa-edit"></i> EDIT</a>
                                
                                <br>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $employee->empID }}"> <i class="fas fa-trash"></i> DELETE</button>
                                <div class="modal fade" id="deleteModal{{ $employee->empID }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this employee?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form method="POST" action="{{ route('employee.delete', $employee->empID) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                        </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="12">No accounts available.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $employees -> links() }}
            </div>
        </div>
    </div>    
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
    // Function to filter the employee table rows based on name and company role
    function searchEmployeeByNameAndRole(nameQuery, role) {
        var empTable = $('#emptable'); // Update the table ID to 'emptable'
        empTable.find('tbody tr').each(function () {
            var row = $(this);
            var nameCell = row.find('td:nth-child(2)'); // Adjust the column index if needed
            var roleCell = row.find('td:nth-child(3)'); // Adjust the column index if needed
            var name = nameCell.text().trim();
            var employeeRole = roleCell.text().trim();
            var found = false;

            // Check if the name contains the query and matches the selected role
            if ((name.toLowerCase().includes(nameQuery.toLowerCase()) || nameQuery === '') && (employeeRole === role || role === 'All')) {
                found = true;
            }

            if (found) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Function to filter the customer table rows based on name
    function searchCustomerByName(nameQuery) {
        var custTable = $('#custtable'); // Update the table ID to 'custtable'
        custTable.find('tbody tr').each(function () {
            var row = $(this);
            var nameCell = row.find('td:nth-child(3)'); // Adjust the column index if needed
            var name = nameCell.text().trim();
            var found = false;

            // Check if the name contains the query
            if (name.toLowerCase().includes(nameQuery.toLowerCase()) || nameQuery === '') {
                found = true;
            }

            if (found) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Function to show/hide cards based on the selected filter
    function toggleCards(selectedFilter, employeeRole ) {
        var employeeCard = $('#employeeCard');
        var customerCard = $('#customerCard');

        if (selectedFilter === 'Employee' && employeeRole!== 'All Roles') {
            employeeCard.show();
            customerCard.show();
        } else if (selectedFilter === 'Employee' && employeeRole === 'All Roles') {
            employeeCard.show();
            customerCard.hide();
        }else if (selectedFilter === 'Customer') {
            employeeCard.hide();
            customerCard.show();
        } else if (selectedFilter === 'All') {
            employeeCard.show();
            customerCard.show();
        }
    }

    // Initial filter when the page loads
    toggleCards('All');
    searchEmployeeByNameAndRole('', 'All');

    // Handle search input keyup and role dropdown change for employee table
    $('#nameSearch, #companyRoleFilter').on('input change', function () {
        var nameQuery = $('#nameSearch').val();
        var role = $('#companyRoleFilter').val();
        searchEmployeeByNameAndRole(nameQuery, role);
    });

    // Handle search input keyup for customer table
    $('#nameSearch').on('input', function () {
        var nameQuery = $('#nameSearch').val();
        searchCustomerByName(nameQuery);
    });

    // Handle filter dropdown change
    $('#tableFilter').on('change', function () {
        var selectedFilter = $(this).val();
        toggleCards(selectedFilter);
    });
});

</script>

@endsection
