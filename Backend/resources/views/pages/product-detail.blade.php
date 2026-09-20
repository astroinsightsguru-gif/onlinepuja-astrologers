@extends('../layout/' . $layout)

@section('subhead')
    <title>Product Detail</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Product Details</h2>
    </div>
    <!-- BEGIN: Transaction Details -->
    @foreach ($astroMallDetail as $astroMall)
        <div class="box p-5 rounded-md mt-4">

            <div class="intro-y grid grid-cols-11 gap-5 mt-5">
                <div class="col-span-12 lg:col-span-4 2xl:col-span-3">
                    <img class="tooltip rounded" src="/{{ $astroMall->productImage }}"
                        onerror="this.onerror=null;this.src='/build/assets/images/demo.png';" alt="Product image" />

                </div>
                <div class="col-span-12 lg:col-span-7">
                    <div class=" items-center ">
                        <div class="font-medium text-base truncate" style="font-size: 25px">{{ $astroMall->name }}</div>
                        <div class="font text-base truncate mt-4">{{ $astroMall->features }}</div>
                        <div class="font-medium text-base truncate mt-3">{{ $currency->value }} {{ $astroMall->amount }}
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($astroMall->questionAnswer as $product)
                <div class="card border p-2 mt-5 rounded">
                    <div class="col-span-12 lg:col-span-7">
                        <div class=" items-center ">
                            <div class="font-medium text-base truncate" style="font-size: 16px">{{ $product->question }}
                            </div>
                            <div class="font text-base truncate ">{{ $product->answer }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="mt-5">
                @foreach ($astroMall->productReview as $review)
                    <div class="intro-y">
                        <div class="card border px-4 py-4 mb-3 flex items-center rounded">

                            <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                <img class="rounded-full" src="/{{ $review->profile }}"
                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                    alt="{{ucfirst($professionTitle)}} image" />
                            </div>

                            <div class="ml-4 mr-auto">
                                <div class="font-medium">{{ $review->userName }}</div>
                                <div class="flex items-center">
                                    <div class="flex items-center">
                                        <i data-lucide="star" class="text-pending fill-pending/30 w-4 h-4 mr-1"></i>
                                        <i data-lucide="star" class="text-pending fill-pending/30 w-4 h-4 mr-1"></i>
                                        <i data-lucide="star" class="text-pending fill-pending/30 w-4 h-4 mr-1"></i>
                                        <i data-lucide="star" class="text-pending fill-pending/30 w-4 h-4 mr-1"></i>
                                        <i data-lucide="star" class="text-slate-400 fill-slate/30 w-4 h-4 mr-1"></i>
                                    </div>
                                    <div class="text-slate-500 text-xs mt-0.5">({{ $review->rating }})</div>
                                    <div class="text-slate-500 text-xs mt-0.5 pl-3">
                                        {{ date('j F Y', strtotime($review->created_at)) }}</div>

                                </div>
                                <div class="font text-base mt-2">{{ $review->review }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    @endforeach
    <!-- END: Transaction Details -->
@endsection
