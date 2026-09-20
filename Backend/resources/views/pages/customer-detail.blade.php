@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Customer Details</h2>
    </div>
    <!-- BEGIN: Profile Info -->

    @foreach ($result as $userDetail)
        <div class="intro-y box  pt-5 mt-5">

            <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5 px-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                        <img class="rounded-full" src="/{{ $userDetail->profile }}"
                            onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ucfirst($professionTitle)}} image" />
                    </div>
                    <div class="ml-5">
                        <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                            {{ $userDetail->name ? $userDetail->name : '--' }}</div>
                        <div class="text-slate-500">{{ $userDetail->contactNo ? $userDetail->contactNo : '--' }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Contact Details</div>
                    <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            {{ $userDetail->email ? $userDetail->email : '--' }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <i data-lucide="map-pin" class="w-5 h-6 mr-2"></i>
                            {{ $userDetail->addressLine1 ? $userDetail->addressLine1 : '--' }}
                        </div>

                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Details</div>
                    <div class="flex items-center justify-center lg:justify-start mt-2">
                        <div class="flex">
                            Birth Date: <span
                                class="ml-3 font-medium text-success">{{ $userDetail->birthDate ? date('d-m-Y', strtotime($userDetail->birthDate)) : '--' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Birth Time<span
                                class="ml-3 font-medium text-danger">{{ $userDetail->birthTime ? $userDetail->birthTime : '--' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Birth Place<span
                                class="ml-3 font-medium text-warning">{{ $userDetail->birthPlace ? $userDetail->birthPlace : '--' }}</span>
                        </div>
                    </div>
                </div>
            </div>


            <div id="link-tab" class="p-3">

                <ul class="nav nav-link-tabs" role="tablist">
                    <li id="example-1-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#example-tab-1"
                            type="button" role="tab" aria-controls="example-tab-1" aria-selected="true">
                            Call History
                        </button>
                    </li>
                    <li id="example-2-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-2"
                            type="button" role="tab" aria-controls="example-tab-2" aria-selected="false">
                            Wallet
                        </button>
                    </li>
                    <li id="example-3-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-3"
                            type="button" role="tab" aria-controls="example-tab-3" aria-selected="false">
                            Chat History
                        </button>
                    </li>
                    <li id="example-4-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-4"
                            type="button" role="tab" aria-controls="example-tab-4" aria-selected="false">
                            Order Detail
                        </button>
                    </li>

                    <li id="example-9-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-9"
                            type="button" role="tab" aria-controls="example-tab-9" aria-selected="false">
                            Puja Order
                        </button>
                    </li>

                    <li id="example-5-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-5"
                            type="button" role="tab" aria-controls="example-tab-5" aria-selected="false">
                            Report
                        </button>
                    </li>
                    <li id="example-6-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-6"
                            type="button" role="tab" aria-controls="example-tab-6" aria-selected="false">
                            Following List
                        </button>
                    </li>
                    <li id="example-7-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-7"
                            type="button" role="tab" aria-controls="example-tab-7" aria-selected="false">
                            Notification List
                        </button>
                    </li>
                    <li id="example-8-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-8"
                            type="button" role="tab" aria-controls="example-tab-8" aria-selected="false">
                            Gift List
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-5 mastertab">
                    <div id="example-tab-1" class="tab-pane leading-relaxed active" role="tabpanel"
                        aria-labelledby="example-1-tab">
                        @if (count($userDetail->callRequest->callHistory) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">

                                @foreach ($userDetail->callRequest->callHistory as $callReq)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="image-fit" style="height:150px;width:150px">

                                                    <img class="rounded-full" src="/{{ $callReq->profileImage }}"
                                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                        alt="{{ucfirst($professionTitle)}} image" />
                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3">
                                                    {{ $callReq->astrologerName }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-2">
                                                    <div class="flex items-center">
                                                        {{ date('d-m-Y h:i a', strtotime($callReq->created_at)) }}
                                                    </div>
                                                    <div class="flex items-center mt-2">

                                                        {{ $callReq->contactNo }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        Call Rate:
                                                        {{ $currency->value }}{{ $callReq->callRate }} /Min
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        Deduction:
                                                        (-)  {{ $currency->value }}{{ $callReq->deduction }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        Duration:
                                                        {{ $callReq->totalMin }} Min
                                                    </div>

                                                    <div
                                                        class="flex items-center mt-2 {{ $callReq->callStatus == 'Accepted' ? 'text-success' : 'text-danger' }}">
                                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                                        {{ $callReq->callStatus }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Call Request Found</h5>
                            </div>
                        @endif
                    </div>
                    <div id="example-tab-2" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-2-tab">
                        <div class="intro-y box">

                            <div id="basic-tab" class="p-5">
                                <div class="preview">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li id="example-1" class="nav-item flex-1" role="presentation">
                                            <button class="nav-link w-full py-2 active" data-tw-toggle="pill"
                                                data-tw-target="#wallet" type="button" role="tab"
                                                aria-controls="wallet" aria-selected="true">
                                                Wallet Transaction
                                            </button>
                                        </li>
                                        <li id="example-2" class="nav-item flex-1" role="presentation">
                                            <button class="nav-link w-full py-2" data-tw-toggle="pill"
                                                data-tw-target="#payments" type="button" role="tab"
                                                aria-controls="payments" aria-selected="false">
                                                Payments Logs
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content border-l border-r border-b">
                                        <div id="wallet" class="tab-pane leading-relaxed p-5 active" role="tabpanel"
                                            aria-labelledby="example-1">
                                            <div class="col-span-12 xl:col-span-4 mt-6">
                                                <div class="mt-5">
                                                    @if (count($userDetail->walletTransaction->wallet) > 0)
                                                        @foreach ($userDetail->walletTransaction->wallet as $wallet)
                                                            <div class="intro-y">
                                                                <div class="box px-4 py-4 mb-3 flex items-center">
                                                                    <div class="ml-4 mr-auto">
                                                                        @if ($wallet->transactionType == 'Gift')
                                                                        <div class="font-medium">
                                                                            Send
                                                                            {{ $wallet->transactionType }} to
                                                                            {{ $wallet->name }}
                                                                        </div>
                                                                        @elseif($wallet->transactionType == 'Cashback' || $wallet->transactionType == 'Referral')
                                                                        <div class="font-medium">
                                                                            {{ $wallet->transactionType }} Received
                                                                        </div>
                                                                       @elseif($wallet->transactionType == 'pujaOrder' )
                                                                        <div class="font-medium">
                                                                            {{ $wallet->transactionType }}

                                                                        </div>
                                                                        @elseif($wallet->transactionType == 'Report' )
                                                                        <div class="font-medium">
                                                                            {{ $wallet->transactionType }} Request with
                                                                            {{ $wallet->name }}

                                                                        </div>
                                                                        @else
                                                                        <div class="font-medium">
                                                                            {{ $wallet->transactionType }} with
                                                                            {{ $wallet->name }} for
                                                                            {{ $wallet->totalMin }}
                                                                            minutes
                                                                        </div>

                                                                        @endif
                                                                        <div class="text-slate-500 text-xs mt-0.5">
                                                                            {{ date('d-m-Y h:i a', strtotime($wallet->created_at)) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex items-center">

                                                                        <div class="ml-4 mr-auto {{ ($wallet->transactionType == 'Cashback' || $wallet->transactionType == 'Referral') ? 'text-success' : 'text-danger' }}">
                                                                            @if($wallet->transactionType == 'Cashback' || $wallet->transactionType == 'Referral')
                                                                                <div class="font-medium">
                                                                                   (+) {{ $currency->value }} {{ $wallet->amount }}
                                                                                </div>
                                                                            @else
                                                                                <div class="font-medium">
                                                                                    (-) {{ $currency->value }} {{ $wallet->amount }}
                                                                                 </div>
                                                                            @endif
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center w-30">
                                                            <h5>No Wallet Transaction Found</h5>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div id="payments" class="tab-pane leading-relaxed p-5" role="tabpanel"
                                            aria-labelledby="example-2">
                                            <div class="col-span-12 xl:col-span-4 mt-6">
                                                <div class="mt-5">
                                                    @if (count($userDetail->paymentLogs->payment) > 0)
                                                        @foreach ($userDetail->paymentLogs->payment as $payments)
                                                            <div class="intro-y">
                                                                <div class="box px-4 py-4 mb-3 flex items-center">
                                                                    <div class="ml-4 mr-auto">
                                                                        <div class="font-medium">
                                                                            Recharge</div>
                                                                        <div class="text-slate-500 text-xs mt-0.5">
                                                                            {{ date('d-m-Y h:i a', strtotime($payments->created_at)) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex items-center">

                                                                        <div class="ml-4 mr-auto">
                                                                            <div class="font-medium">
                                                                                (+) {{ $currency->value }}{{ $payments->amount }}
                                                                            </div>
                                                                            <div
                                                                                class="text-slate-500 text-x mt-0.5 {{ $payments->paymentStatus == 'Success' || $payments->paymentStatus == 'success' ? 'text-success' : 'text-danger' }}">
                                                                                {{ $payments->paymentStatus }}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center w-30">
                                                            <h5>No Payments Logs Found</h5>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="example-tab-3" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-3-tab">
                        @if (count($userDetail->chatRequest->chatHistory) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">
                                @foreach ($userDetail->chatRequest->chatHistory as $chatReq)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="image-fit" style="height:150px;width:150px">
                                                    <img class="rounded-full" src="/{{ $chatReq->profileImage }}"
                                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                        alt="{{ucfirst($professionTitle)}} image" />
                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3">
                                                    {{ $chatReq->astrologerName }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-2">
                                                    <div class="flex items-center">
                                                        {{ date('d-m-Y h:i a', strtotime($chatReq->created_at)) }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                                        {{ $chatReq->contactNo }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        Rate:
                                                        @if ($chatReq->chatRate)
                                                            {{ $currency->value }}{{ $chatReq->chatRate }}/min
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        Deduction:
                                                        (-) {{ $chatReq->deduction }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        Duration:
                                                        @if ($chatReq->totalMin)
                                                            {{ $chatReq->totalMin }} Min
                                                        @endif
                                                    </div>
                                                    <div
                                                        class="flex items-center mt-2 {{ $chatReq->chatStatus == 'Pending' ? 'text-success' : 'text-danger' }}">
                                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                                        {{ $chatReq->chatStatus }}
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Chat Request Found</h5>
                            </div>
                        @endif
                    </div>
                    <div id="example-tab-4" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-4-tab">
                        @if (count($userDetail->orders->order) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">
                                @foreach ($userDetail->orders->order as $order)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="image-fit" style="height:150px;width:150px">
                                                    <img alt="Product image" class="rounded-full"
                                                        style="width: 100%; height: 100%;"
                                                        src="/{{ $order->productImage }}"onerror="this.onerror=null;this.src='/build/assets/images/default.jpg';" />

                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3 text-success"
                                                    style="font-size: 18px">
                                                    {{ $order->productCategory }} - {{ $order->productName }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-0.9">
                                                    <div class="flex items-center mt-0.8" style="word-break:break-all">
                                                        {{ $order->orderAddressName }}{{ $order->flatNo }},{{ $order->locality }},{{ $order->city }},{{ $order->state }},{{ $order->country }},{{ $order->pincode }}
                                                    </div>

                                                    <div class="flex items-center mt-2">
                                                        <div class="items-center text-danger mr-2">Payable Amount:</div>
                                                        {{ $currency->value }}{{ $order->payableAmount }}
                                                    </div>
                                                    @if(!empty($order->gstPercent))
                                                        <div class="flex items-center mt0.2">
                                                            <div class="items-center text-danger mr-2">GST:</div>
                                                            {{ $order->gstPercent }}%
                                                        </div>
                                                    @endif
                                                    <div class="flex items-center mt-0.6">
                                                        <div class="items-center text-danger mr-2"> Total Amount:</div>
                                                        {{ $currency->value }}{{ $order->totalPayable }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        {{ date('d-m-Y h:i a', strtotime($order->created_at)) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Order Detail Found</h5>
                            </div>
                        @endif
                    </div>
                    <div id="example-tab-5" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-5-tab">
                        <div class="col-span-12 xl:col-span-4 mt-6">
                            <div class="mt-5">
                                @if (count($userDetail->reportRequest->reportHistory) > 0)
                                    @foreach ($userDetail->reportRequest->reportHistory as $report)
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center">
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium text-success">
                                                        {{ $report->firstName }} {{ $report->lastName }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.5">
                                                        {{ date('d-m-Y h:i a', strtotime($report->created_at)) }}
                                                    </div>
                                                    <div class="text-slate-900 text-x mt-0.9">
                                                        {{ $report->title }}
                                                    </div>
                                                    <div class="text text mt-0.9">
                                                        {{ $currency->value }}{{ $report->reportRate }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Report Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="example-tab-9" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-9-tab">
                        <div class="col-span-12 xl:col-span-4 mt-6">
                            <div class="mt-5">
                                @if (count($userDetail->pujas->pujaorder) > 0)
                                    @foreach ($userDetail->pujas->pujaorder as $orders)
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center">
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium text-success">
                                                        {{ $orders->puja_name }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.5">
                                                        {{ date('d-m-Y h:i a', strtotime($orders->created_at)) }}
                                                    </div>
                                                    <div class="text-slate-900 text-x mt-0.9">
                                                        {{ $orders->package_name }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.9">
                                                        Puja Start : {{ date('d-m-Y h:i a', strtotime($orders->puja_start_datetime)) }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.9">
                                                        Puja End : {{ date('d-m-Y h:i a', strtotime($orders->puja_end_datetime)) }}
                                                    </div>
                                                    <div class="text text mt-0.9">
                                                        <span class="text-danger mr-2">Payable Amount:</span>
                                                        {{ $currency->value }}{{ $orders->order_total_price }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Puja Order Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="example-tab-6" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-6-tab">
                        <div class="grid grid-cols-12 gap-6 mt-5">

                            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                                @if (count($userDetail->follower) > 0)
                                    <table class="table table-report -mt-2" aria-label="follower">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap">#</th>
                                                <th class="whitespace-nowrap">PROFILE</th>
                                                <th class="whitespace-nowrap">NAME</th>
                                                <th class="text-center whitespace-nowrap">CONTACT NO</th>
                                                <th class="text-center whitespace-nowrap">DATE</th>
                                                <th class="text-center whitespace-nowrap">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp

                                            @foreach ($userDetail->follower as $follower)
                                                <tr class="intro-x">
                                                    <td>{{ ++$no }} </td>
                                                    <td>
                                                        <div class="flex">
                                                            <div class="w-10 h-10 image-fit zoom-in">
                                                                <img class="rounded-full"
                                                                    src="/{{ $follower->profileImage }}"
                                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                                    alt="{{ucfirst($professionTitle)}} image" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="font-medium whitespace-nowrap">{{ $follower->name }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $follower->contactNo }}</td>
                                                    <td class="text-center whitespace-nowrap">
                                                        {{ date('d-m-Y', strtotime($follower->followingDate)) }}
                                                    </td>
                                                    <td class="table-report__action w-56">
                                                        <div class="flex justify-center items-center">
                                                            <a class="flex items-center mr-3 text-success"
                                                                href="/admin/astrologers/{{ $follower->id }}">
                                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Followers Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="example-tab-7" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-7-tab">
                        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                            @if (count($userDetail->notification) > 0)
                                <table class="table table-report mt-2" aria-label="">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap" style="text-align: center">TITLE</th>
                                            <th class="whitespace-nowrap" style="text-align: center">DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                        @endphp

                                        @foreach ($userDetail->notification as $notification)
                                            <tr class="intro-x">
                                                <td>{{ ++$no }} </td>
                                                <td>
                                                    <div class="font-medium" style="text-align: center">
                                                        {{ $notification->title }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $notification->description }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            @else
                                <div class="text-center w-30">
                                    <h5>No Notification List Found</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="example-tab-8" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-8-tab">
                        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                            @if (count($userDetail->sendGifts->gifts) > 0)
                                <table class="table table-report mt-2" aria-label="gifts">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap" style="text-align: center">Name</th>
                                            <th class="whitespace-nowrap" style="text-align: center">GIFT NAME</th>
                                            <th class="whitespace-nowrap" style="text-align: center">AMOUNT</th>
                                            <th class="whitespace-nowrap" style="text-align: center">DATE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                        @endphp

                                        @foreach ($userDetail->sendGifts->gifts as $gift)
                                            <tr class="intro-x">
                                                <td>{{ ++$no }} </td>
                                                <td>
                                                    <div class="font-medium" style="text-align: center">
                                                        {{ $gift->astrolgoerName }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $gift->giftName }}</td>
                                                <td class="text-center">
                                                    (-)  {{ $currency->value }}{{ $gift->giftAmount*$gift->inr_usd_conversion_rate ? $gift->giftAmount*$gift->inr_usd_conversion_rate : 0 }}
                                                </td>
                                                <td class="text-center">
                                                    {{ date('d-m-Y', strtotime($gift->created_at)) }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            @else
                                <div class="text-center w-30">
                                    <h5>No Gift List Found</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @endforeach
@endsection
@section('script')
    <script type="text/javascript"></script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
