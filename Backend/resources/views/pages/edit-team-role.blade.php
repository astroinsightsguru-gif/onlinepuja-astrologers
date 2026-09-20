@extends('../layout/' . $layout)

@section('subhead')
    <title>Add TeamRole</title>
@endsection

@section('subcontent')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">

            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Edit Team Role</h2>
                </div>
                <form method="POST" action="{{ route('editTeamRoleApi') }}" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="regular-form-1" class="form-label">Name</label>
                                            <input id="name" name="id" type="hidden" class="form-control inputs"
                                                placeholder="Role Name" value="{{ $role->id }}"required>
                                            <input id="name" name="name" type="text" class="form-control inputs"
                                                placeholder="Role Name" value="{{ $role->name }}"required>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:grid grid-cols-3 gap-2 mt-3">
                                        @foreach ($pages as $pageIndex => $page)
                                            <div class="mt-2">
                                                <input type="hidden" name="page[{{ $pageIndex }}][page][id]"
                                                    value="{{ $page->id }}">
                                                <input id="{{ $page->id }}"
                                                    class="show-code d-inline form-check-input mr-2 ml-3"
                                                    name="page[{{ $pageIndex }}][page][value]" type="checkbox"
                                                    {{ $page->isPermitted ? 'checked' : '' }}>
                                                <p class="d-inline">{{ $page->pageName }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">
                                    Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ session('error') }}");
        @endif
    </script>
@endsection
