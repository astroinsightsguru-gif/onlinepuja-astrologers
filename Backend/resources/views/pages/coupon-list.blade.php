@extends('../layout/' . $layout)

@section('subhead')
    <title>Coupon</title>
@endsection

@section('subcontent')
    <div id="loader" class="center">
    </div>
    <h2 class="intro-y text-lg font-medium mt-10">Coupon List</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-coupon"
                class="btn btn-primary shadow-md mr-2">Add
                Coupon</a>
            <div class="hidden md:block mx-auto text-slate-500">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto">
            <table class="table table-report -mt-2" aria-label="coupon">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">NAME</th>
                        <th class="whitespace-nowrap">COUPON CODE</th>
                        <th class="whitespace-nowrap">FROM DATE</th>
                        <th class="whitespace-nowrap">TO DATE</th>
                        <th class="whitespace-nowrap">MIN AMOUNT</th>
                        <th class="whitespace-nowrap">MAX AMOUNT</th>
                        <th class="text-center whitespace-nowrap">STATUS</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($coupons as $coupon)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $coupon['name'] }}</div>
                            </td>
                            <td class="text-center">{{ $coupon['couponCode'] }}</td>
                            <td class="text-center whitespace-nowrap">{{ date('d-m-Y', strtotime($coupon['validFrom'])) }}
                            </td>
                            <td class="text-center whitespace-nowrap">{{ date('d-m-Y', strtotime($coupon['validTo'])) }}
                            </td>
                            <td class="text-center">{{ $coupon['minAmount'] }}</td>
                            <td class="text-center">{{ $coupon['maxAmount'] }}</td>
                            <td class="w-40">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                             mt-3 sm:mt-0">
                                    <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $coupon['isActive'] ? 'checked' : '' }}
                                        onclick="editCoupon({{ $coupon['id'] }},'{{ $coupon['name'] }}',{{ $coupon['isActive'] }})"
                                        href="$coupon['id']" data-tw-target="#verified">
                                </div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn" href="javascript:;"
                                        onclick="editbtn({{ $coupon['id'] }} , '{{ $coupon['name'] }}', '{{ $coupon['couponCode'] }}', '{{ $coupon['validFrom'] }}', '{{ $coupon['validTo'] }}', '{{ $coupon['minAmount'] }}', '{{ $coupon['maxAmount'] }}', '{{ $coupon['description'] }}')"
                                        value="{{ $coupon['name'] }}" class="flex items-center mr-3 "
                                        data-tw-target="#edit-modal" data-tw-toggle="modal"><i data-lucide="check-square"
                                            class="editbtn w-4 h-4 mr-1"></i>Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
            <nav class="w-full sm:w-auto sm:mr-auto" aria-label="coupon-list">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('setCouponPage', $page - 1) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link" href="{{ route('setCouponPage', $i + 1) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('setCouponPage', $page + 1) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- END: Data List -->
        <div id="add-coupon" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Add Coupon</h2>
                    </div>
                    <form action="{{ route('addCouponApi') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="input" class="p-5">
                            <div class="preview">
                                <div class="mt-3">
                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    placeholder="Name" required onkeypress="return Validate(event);">
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="couponCode" class="form-label">Coupon Code</label>
                                                <input type="text" name="couponCode" id="couponCode"
                                                    class="form-control" placeholder="Coupon Code" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="validFrom" class="form-label">From Date</label>
                                                <input type="date" name="validFrom" id="validFrom"
                                                    class="form-control" placeholder="From Date" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="validTo" class="form-label">To Date</label>
                                                <input type="date" name="validTo" id="validTo" class="form-control"
                                                    placeholder="To Date" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="minAmount" class="form-label">Min Amount</label>
                                                <input type="number" name="minAmount" id="minAmount"
                                                    class="form-control" placeholder="Min Amount" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="maxAmount" class="form-label">Max Amount</label>
                                                <input type="number" name="maxAmount" id="maxAmount"
                                                    class="form-control" placeholder="Max Amount" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" id="description" class="form-control" placeholder="Description" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add Coupon</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Edit Coupon</h2>
                    </div>
                    <form action="{{ route('editCouponApi') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="input" class="p-5">
                            <div class="preview">
                                <div class="mt-3">
                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <input type="hidden" id="filed_id" name="filed_id">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" name="name" id="id" class="form-control"
                                                    placeholder="Name" required onkeypress="return Validate(event);">
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="couponCode" class="form-label">Coupon Code</label>
                                                <input type="text" name="couponCode" id="coid"
                                                    class="form-control" placeholder="Coupon Code" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="validFrom" class="form-label">From Date</label>
                                                <input type="date" name="validFrom" id="fid"
                                                    class="form-control" placeholder="From Date" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="validTo" class="form-label">To Date</label>
                                                <input type="date" name="validTo" id="tid" class="form-control"
                                                    placeholder="To Date" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="minAmount" class="form-label">Min Amount</label>
                                                <input type="text" name="minAmount" id="maid"
                                                    class="form-control" placeholder="Min Amount" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="maxAmount" class="form-label">Max Amount</label>
                                                <input type="text" name="maxAmount" id="mxid"
                                                    class="form-control" placeholder="Max Amount" required>
                                            </div>
                                        </div>
                                        <div class="input">
                                            <div>
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" id="decid" class="form-control" placeholder="Description" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Edit
                                        Coupon</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- BEGIN: Delete Confirmation Modal -->
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process
                            cannot be undone.</div>
                    </div>
                    <form action="#" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button class="btn btn-danger w-24">@method('DELETE')Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('couponStatusApi') }}" method="POST" enctype="multipart/form-data">
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
    <!-- END: Delete Confirmation Modal -->
@endsection

@section('script')
    <script type="text/javascript">
        function editbtn($id, $name, $couponcode, $validFrom, $validTo, $minAmount, $maxAmount, $description) {

            var id = $id;
            var coid = $id;
            var fid = $id;
            var tid = $id;
            var maid = $id;
            var mxid = $id;
            var decid = $id;
            $cid = id;

            $('#filed_id').val($cid);
            $('#id').val($name);
            $('#coid').val($couponcode);
            $('#maid').val($minAmount);
            $('#mxid').val($maxAmount);
            $('#decid').val($description);
            if ($validFrom) {
                var newdate = $validFrom.split("-");
                var date = newdate[2].split(" ");
                date = newdate[0] + '-' + newdate[1] + '-' + date[0]
                $('#fid').val(date);
            }
            if ($validTo) {
                var newdate = $validTo.split("-");
                var date = newdate[2].split(" ");
                date = newdate[0] + '-' + newdate[1] + '-' + date[0]
                $('#tid').val(date);
            }
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }s

        function editCoupon($id, $name, $isActive) {
            var id = $id;
            $fid = id;
            $('#status_id').val($fid);
            $('#id').val($name);
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

        }
    </script>
@endsection
