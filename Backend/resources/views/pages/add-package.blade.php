@extends('../layout/' . $layout)

@section('subhead')
    <title>Add Package</title>
@endsection

@section('subcontent')
<style>
#package-points-container {
    display: flex;
    flex-direction: column;
}

.package-point-pair {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.add-point-btn, .remove-point-btn {
    padding: 0.5rem 1rem;
    border: none;
    color: white;
    cursor: pointer;
}

.add-point-btn {
    background-color: #4CAF50; /* Green */
}

.remove-point-btn {
    background-color: #f44336; /* Red */
}

.package-point-pair .input {
    flex: 1;
}

.package-point-pair .btn {
    margin-left: 1rem;
}
</style>

    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 mt-2">
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">Add Package</h2>
            </div>

            <form method="POST" enctype="multipart/form-data" id="add-form" action="{{ isset($package) ? route('puja-package.update', $package->id) : route('puja-package.store') }}">
            @csrf
            <div id="input" class="p-5">
                <div class="preview">
                    <div class="mt-3">
                        <div class="sm:grid grid-cols-4 gap-2">
                            <!-- Title Field -->
                            <div class="input">
                                <div>
                                    <label for="title" class="form-label">Title</label>
                                    <input id="title" name="title" type="text" class="form-control inputs"
                                        placeholder="Add Package title" value="{{ $package->title ?? '' }}" required>
                                    <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                        <ul></ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Package Price Field -->
                            <div class="input mt-2 sm:mt-0">
                                <div>
                                    <label for="package_price" class="form-label">Package Price</label>
                                    <input id="package_price" name="package_price" type="text" class="form-control inputs"
                                        placeholder="Price" value="{{ $package->package_price ?? '' }}" required maxlength="10">
                                    <div class="text-danger print-number-error-msg mb-2" style="display:none">
                                        <ul></ul>
                                    </div>
                                </div>
                            </div>

                            <!-- USD Package Price Field -->
                            <div class="input mt-2 sm:mt-0">
                                <div>
                                    <label for="package_price_usd" class="form-label">USD Package Price</label>
                                    <input id="package_price_usd" name="package_price_usd" type="text" class="form-control inputs"
                                        placeholder="USD Price" value="{{ $package->package_price_usd ?? '' }}" required maxlength="10">
                                </div>
                            </div>

                            <!-- Person Field -->
                            <div class="input mt-2 sm:mt-0">
                                <div>
                                    <label for="person" class="form-label">Person</label>
                                    <input id="person" type="number" name="person" class="form-control inputs"
                                        placeholder="Enter person" value="{{ $package->person ?? '' }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Package Points Section -->
                    <div class="mt-3" id="package-points-container">
                        @if(isset($package) && !empty($package->description))
                            @foreach ($package->description as $deskey => $des)
                                <div class="package-point-pair flex items-center mb-3">
                                    <div class="input flex-1">
                                        <div>
                                            <label for="validation-form-6" class="form-label">Package Point</label>
                                            <textarea class="form-control inputs" name="package_points[]"
                                                placeholder="Enter Package Point" minlength="10" required>{{ $des }}</textarea>
                                        </div>
                                    </div>
                                    @if($deskey == 0)
                                        <div>
                                            <button type="button" id="add-point-btn" class="btn btn-secondary">
                                                <i data-lucide="plus"></i>
                                            </button>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-danger remove-point-btn">X</button>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="package-point-pair flex items-center mb-3">
                                <div class="input flex-1">
                                    <div>
                                        <label for="validation-form-6" class="form-label">Package Point</label>
                                        <textarea class="form-control inputs" name="package_points[]"
                                            placeholder="Enter Package Point" maxlength="200" required></textarea>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" id="add-point-btn" class="btn btn-secondary">
                                        <i data-lucide="plus"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary shadow-md">
                            {{ isset($package) ? 'Update Package' : 'Add Package' }}
                        </button>
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
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('package-points-container');
    const addButton = document.getElementById('add-point-btn');

    // Function to add a new package point pair
    function addNewPair() {
        const newPair = document.createElement('div');
        newPair.className = 'package-point-pair flex items-center mb-3';

        newPair.innerHTML = `
            <div class="input flex-1">
                <div>
                    <label for="validation-form-6" class="form-label w-full flex flex-col sm:flex-row">
                        Package Point
                    </label>
                    <textarea class="form-control inputs" name="package_points[]" placeholder="Enter Package Point" minlength="10" required></textarea>
                </div>
            </div>
            <button type="button" class="btn btn-danger remove-point-btn">X</button>
        `;

        // Append the new pair to the container
        container.appendChild(newPair);
    }

    // Handle click events in the container
    container.addEventListener('click', function(event) {
        if (event.target.classList.contains('add-point-btn')) {
            addNewPair();
        } else if (event.target.classList.contains('remove-point-btn')) {
            const pair = event.target.closest('.package-point-pair');
            if (container.children.length > 1) {
                container.removeChild(pair);
            } else {
                alert('You must have at least one pair of package points.');
            }
        }
    });

    // Handle click events on the "Add More Points" button
    addButton.addEventListener('click', function() {
        addNewPair();
    });
});
</script>


@endsection
