@extends('../layout/' . $layout)

@section('subhead')
    <title>Product</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Products</h2>
    <a href="products/add" data-tw-toggle="modal" data-tw-target="#add-blog"
        class="mt-10 d-inline btn btn-primary shadow-md mr-2 addbtn">Add
        Product
    </a>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('products') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input onkeypress="return validateJavascript(event);" value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('products') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
        </div>
    </div>
    <!-- BEGIN: Seller Details -->
    @if ($totalRecords > 0)
        <div class="grid grid-cols-12 gap-6 mt-5 grid-table">
            @foreach ($astromallProduct as $product)
                <div class="intro-y col-span-12 sm:col-span-6 2xl:col-span-4 xl:col-span-4">
                    <div class="box">
                        <div class="p-5">
                            <div
                                class="h-40 2xl:h-56 rounded-md overflow-hidden  before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black/90 before:to-black/10 image-fit">
                                <img alt="Product image" class="rounded-t-md" src="{{ Str::startsWith($product['productImage'], ['http://','https://']) ? $product['productImage'] : '/' . $product['productImage'] }}"
                                    onerror="this.onerror=null;this.src='/build/assets/images/product.png';" />
                                <div class="absolute w-full flex items-center px-3 pt-2 z-10">
                                    <div class="ml-3 text-white mr-auto">
                                    </div>
                                    <div class="dropdown ml-3">
                                        <a href="javascript:;"
                                            class="bg-white/20 dropdown-toggle w-8 h-8 flex items-center justify-center rounded-full"
                                            aria-expanded="false" data-tw-toggle="dropdown">
                                            <i data-lucide="more-vertical" class="w-4 h-4 text-white"></i>
                                        </a>
                                        <div class="dropdown-menu w-40">
                                            <ul class="dropdown-content">
                                                <li>
                                                    <a href="{{ route('getCategoryById', $product['id']) }}"
                                                        class="dropdown-item">
                                                        <i data-lucide="eye" class="w-4 h-4 mr-2"></i>View Product
                                                    </a>
                                                </li>
                                                <li>
                                                    <a id="editbtn" href="products/edit/{{ $product['id'] }}"
                                                        onclick="" class="dropdown-item" data-tw-target="#edit-modal"
                                                        data-tw-toggle="modal"><i data-lucide="check-square"
                                                            class="editbtn w-4 h-4 mr-2"></i>Edit Product</a>
                                                </li>
                                                <li>
                                                </li>
                                                <li>
                                                    <a id="" href="javascript:;"
                                                        onclick="addProductDetail({{ $product['id'] }})"
                                                        class="dropdown-item" data-tw-target="#add-modal"
                                                        data-tw-toggle="modal" data-tw-dismiss="dropdown"><i data-lucide="plus"
                                                            class="editbtn w-4 h-4 mr-2"></i>Add Detail</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 text-white px-5 pb-6 z-10">
                                    <a href="" class="block font-medium text-base">{{ $product['name'] }}</a>
                                    <span
                                        class="text-white/90 text-xs mt-3">{{ date('d-m-Y', strtotime($product['created_at'])) }}</span>
                                </div>
                            </div>
                            <div class="text-slate-600 dark:text-slate-500 mt-5">
                                 <div class="flex items-center">
                                    <i data-lucide="link" class="w-4 h-4 mr-2"></i> Amount (INR) : {{ $product['amount'] }}
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="link" class="w-4 h-4 mr-2"></i> Amount (USD): {{ $product['usd_amount']?:'- - -' }}
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="link" class="w-4 h-4 mr-2"></i> Product Category:
                                    {{ $product['productCategory'] }}
                                </div>
                                <div class="flex items-center mt-2">
                                    <i data-lucide="layers" class="w-4 h-4 mr-2"></i> Features:
                                    {{ $product['features'] }}
                                </div>

                            </div>
                        </div>
                        <div
                            class="flex justify-center lg:justify-end items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                            <div
                                class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0 p-4 mr-2">
                                <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                    href="javascript:;" data-tw-toggle="modal" data-onstyle="success" data-offstyle="danger"
                                    data-toggle="toggle" data-on="Active" data-off="InActive"
                                    {{ $product['isActive'] ? 'checked' : '' }}
                                    onclick="editProductStatus({{ $product['id'] }},{{ $product['isActive'] }})"
                                    href="$product['id']" data-tw-target="#verified">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('products', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('products', ['page' => $i + 1, 'searchString' => $searchString]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('products', ['page' => $page + 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y mt-5" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <div id="add-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Product Detail</h2>
                </div>
                <form action="{{ route('addProductDetailApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="astromallProductId" name="astromallProductId">
                                            <label for="question" class="form-label">Question</label>
                                            <input onkeypress="return validateJavascript(event);" type="text" name="question" id="question" class="form-control"
                                                placeholder="Question" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>

                                            <label for="answer" class="form-label">Answer</label>
                                            <textarea onkeypress="return validateJavascript(event);" type="text" name="answer" id="answer" class="form-control" placeholder="Answer" required></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add Product
                                        Detail</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You wan't Active!</div>
                    </div>
                    <form action="{{ route('productStatusApi') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="status_id" name="status_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnActive">Yes,
                                Active it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- BEGIN: Pagination -->
    <!-- END: Pagination -->
    </div>
    <!-- END: Seller Details -->
@endsection

@section('script')
    <script type="text/javascript">
        function editProduct($id, $name) {
            var id = $id;
            $fid = id;

            $('#status_id').val($fid);
            $('#id').val($name);
        }

        function addProductDetail($id) {
            var id = $id;
            $fid = id;
            $('#astromallProductId').val($fid);
        }

        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function editProductStatus($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
            $('#id').val($name);
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
