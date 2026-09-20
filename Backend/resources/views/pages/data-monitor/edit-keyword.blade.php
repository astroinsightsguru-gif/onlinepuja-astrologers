@extends('../layout/' . $layout)

@section('subhead')
    <title>Edit Keyword</title>
@endsection

@section('subcontent')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">
            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Edit Keyword</h2>
                </div>
                <form data-single="true" action="{{ route('update.keyword', ['id' => $keyword->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                            <input type="hidden" name="id" id="id" value="{{$keyword->id }}">

                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <label for="type" class="form-label">Type</label>
                                        <input type="text" name="type" id="type" class="form-control" placeholder="Type" value="{{ $keyword->type }}" readonly>
                                    </div>
                                    @if($keyword->type == 'offensive-word')
                                    <div id="pattern-container" class="input">
                                        <label for="pattern" class="form-label">Keyword</label>
                                        <?php
                                        $keywordsArray = json_decode($keyword->pattern, true); // Decode JSON to an array
                                        $keywordsString = implode(',', $keywordsArray);
                                        ?>
                                        <input type="text" name="pattern" id="pattern" class="form-control" placeholder="Pattern" value="{{ $keywordsString }}">
                                    </div>
                                    @else
                                    <div id="boolean-container" class="input">
                                        <label for="boolean-select" class="form-label">Value</label>
                                        <select id="boolean-select" name="pattern" class="form-select">
                                            <option value="">--Select--</option>
                                            <option value="true" {{ $keyword->pattern == 'true' ? 'selected' : '' }}>True</option>
                                            <option value="false" {{ $keyword->pattern == 'false' ? 'selected' : '' }}>False</option>
                                        </select>
                                    </div>
                                    @endif
                                    <div class="mt-5">
                                        <button type="submit" class="btn btn-primary shadow-md mr-2">Update Keyword</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    function handleTypeChange() {
        const typeSelect = document.getElementById('type');
        const patternInput = document.getElementById('pattern');
        const booleanContainer = document.getElementById('boolean-container');
         const patternContainer = document.getElementById('pattern-container');

        const booleanSelect = document.getElementById('boolean-select');
     
        patternInput.disabled = true;
        booleanSelect.disabled = true;

        if (typeSelect.value === 'offensive-word') {
            patternInput.disabled = false;
            patternContainer.style.display = 'block';
            booleanContainer.style.display = 'none';
        } else (typeSelect.value === 'phone' || typeSelect.value === 'email') {
            booleanSelect.disabled = false;
            booleanContainer.style.display = 'block';
            patternContainer.style.display = 'none';
        }
    }

    // Since the type is now disabled, we don't need to call this function on change.
    // We just need to call it initially to set the correct form state based on the keyword data.
    document.addEventListener('DOMContentLoaded', function () {
        handleTypeChange(); // Call it when the page loads to initialize the form correctly
    });
</script>
