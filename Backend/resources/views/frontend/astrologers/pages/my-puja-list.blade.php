@extends('frontend.astrologers.layout.master')
@section('content')
    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.astrologerindex') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.astrologers.puja-list') }}"
                                style="color:white;text-decoration:none">My Puja List</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="inpage">
                    <div class="text-left pb-md-4 pb-2">
                        <h1 class="h2 font-weight-bold colorblack">My Pujas</h1>
                        <p>Check your pujas from here.</p>
                    </div>

                    <div class="table-responsive" id="walletTransactionTable">
                        <div class="row pt-1 pb-3" id="historydate">
                            <div class="col-md-12 d-flex justify-content-between align-items-center">
                                <h3 class="font16 font-weight-bold mb-0">Puja List</h3>
                                <a href="{{ route('front.astrologers.create-puja') }}" class="btn btn-sm btn-report" title="Create Puja">
                                    Add Puja
                                </a>
                            </div>
                        </div>
                        
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Puja Title</th>
                                        <th>Puja Image</th>
                                        <th>Puja Price</th>
                                        <th>Puja Place</th>
                                        <th>Puja Start Date</th>
                                        <th>Puja Duration</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($pujas as $puja)
                                        @if (!empty($puja))
                                            <tr>
                                                <td>
                                                    {{ $puja->puja_title }}
                                                </td>
                                                <td>
                                                    @php
                                                         $firstImage = $puja->puja_images[0] ?? null;
                                                    @endphp
                                                   <img src="{{ asset($firstImage) }}" alt="" height="40" width="35">
                                                </td>

                                                <td>
                                                   {{ $currency->value }}{{ $puja->puja_price }}
                                                </td>

                                                <td>
                                                    {{ $puja->puja_place }}
                                                </td>

                                                <td>
                                                    {{ date('d-m-Y h:i a', strtotime($puja->puja_start_datetime)) }}
                                                </td>
                                                <td>
                                                    {{ $puja->puja_duration }} mins
                                                </td>
                                                <td>
                                                    {{ $puja->isAdminApproved}}
                                                </td>

                                               
                                                <td class="text-center">
                                                    <!-- Edit Icon -->
                                                    @if(\Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($puja->puja_start_datetime)) && $puja->isAdminApproved=='Pending')
                                                    <a href="{{ route("front.astrologers.edit-puja",['id' => $puja->id]) }}" class="btn btn-sm btn-primary mx-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                  
                                                    @endif
                                                    
                                                    <!-- Delete Icon (with confirmation) -->
                                                    <form action="{{ route('front.astrologers.delete-puja',['id' => $puja->id]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger mx-1" title="Delete" 
                                                                onclick="return confirm('Are you sure you want to delete this puja?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                  
                                                </td>
                                              
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection