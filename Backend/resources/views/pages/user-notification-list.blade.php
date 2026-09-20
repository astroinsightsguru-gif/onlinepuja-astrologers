@extends('../layout/' . $layout)

@section('subhead')
    <title>Notification</title>
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">User Notification</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">


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
    <!-- END: Delete Confirmation Modal -->
@endsection

@section('script')
    <script type="text/javascript">
        function editbtn($id, $title, $description) {
            var id = $id;
            var did = $id;
            $cid = id;

            $('#filed_id').val($cid);
            $('#id').val($title);
            $('#did').val($description);
        }
    </script>
@endsection
