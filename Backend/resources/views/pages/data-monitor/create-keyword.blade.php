@extends('../layout/' . $layout)

@section('subhead')
    <title>Add Keyword</title>
@endsection

@section('subcontent')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">
            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Add Keyword</h2>
                </div>
                <form data-single="true" action="{{ route('store.keyword') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <label for="type" class="form-label">Type</label>
                                        <select id="type" class="form-select" name="type" onchange="handleTypeChange()">
                                            <option value="">--Select--</option>
                                            <option value="offensive-word">Offensive Word</option>
                                            <option value="phone">Phone Number</option>
                                            <option value="email">Email</option>
                                        </select>
                                    </div>
                          
                                    <div id="pattern-container" class="input">
                                        <!-- Pattern input for offensive-word -->
                                        <label for="pattern" class="form-label">Keyword</label>
                                        <input type="text" name="pattern" id="pattern" class="form-control" placeholder="Pattern">
                                    </div>
                                
                                    <div id="boolean-container" class="input" style="display: none;">
                                        <!-- True/False select for other types -->
                                        <label for="boolean-select" class="form-label">Value</label>
                                        <select id="boolean-select" name="pattern" class="form-select">
                                            <option value="">--Select--</option>
                                            <option value="true">True</option>
                                            <option value="false">False</option>
                                        </select>
                                    </div>
                                    <div class="mt-5">
                                        <button class="btn btn-primary shadow-md mr-2">Add Keyword</button>
                           
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
        const booleanSelect = document.getElementById('boolean-select');
        
        // Reset values and disable unused input
        patternInput.value = '';
        
        patternInput.disabled = true;
        booleanSelect.value = '';
        booleanSelect.disabled = true;

        if (typeSelect.value === 'offensive-word') {
            // Enable pattern input for offensive-word
            patternInput.disabled = false;
            booleanContainer.style.display = 'none';
        } else if (typeSelect.value === 'phone' || typeSelect.value === 'email') {
            // Enable boolean select for other types
            booleanSelect.disabled = false;
            booleanContainer.style.display = 'block';
        } else {
            // Hide all inputs if no valid type is selected
            booleanContainer.style.display = 'none';
        }
    }

    // Attach change event listener
    document.getElementById('type').addEventListener('change', handleTypeChange);
</script>

