@extends('../layout/' . $layout)

@section('subhead')
    <title>Edit Product</title>
@endsection

@section('subcontent')
    <div class="grid grid-cols-11 gap-x-6 mt-5 pb-20">
        <div class="intro-y col-span-12 2xl:col-span-12">
            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Edit Product</h2>
                </div>
                <form action="{{ route('editProductApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <!-- Name and Amount Fields (each occupying col-span-6) -->
                            <div class="sm:grid grid-cols-12 gap-2">
                                <div class="col-span-6">
                                    <input type="hidden" name="field_id" id="field_id" class="form-control"
                                        value="{{ $product['id'] }}">
                                    <label for="name" class="form-label">Name</label>
                                    <input id="name" name="name" type="text" class="form-control"
                                        placeholder="Name" value="{{ $product['name'] }}" required
                                        onkeypress="return Validate(event);">
                                </div>
                                <div class="col-span-6">
                                    <label for="productCategoryId" class="form-label">Product Category</label>
                                    <select class="form-control" id="productCategoryId" name="productCategoryId">
                                        <option disabled selected value="">--Select Category--</option>
                                        @foreach ($result as $category)
                                            <option value="{{ $category['id'] }}"
                                                {{ $product->productCategoryId == $category['id'] ? 'selected' : '' }}>
                                                {{ $category['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Product Category and USD Amount Fields (each occupying col-span-6) -->
                            <div class="sm:grid grid-cols-12 gap-2 mt-3">

                                <div class="col-span-6">
                                    <label for="amount" class="form-label">Amount (INR)</label>
                                    <input type="text" id="amount" name="amount" class="form-control"
                                        placeholder="Amount" value="{{ $product['amount'] }}" required
                                        onkeydown="numbersOnly(event)">
                                </div>

                                <div class="col-span-6">
                                    <label for="usd_amount" class="form-label">Amount (USD)</label>
                                    <input type="text" id="usd_amount" name="usd_amount" class="form-control"
                                        placeholder="USD Amount" value="{{ $product['usd_amount'] ?? '' }}" required
                                        onkeydown="numbersOnly(event)">
                                </div>
                            </div>

                            <!-- Features Field -->
                            <div class="mt-3">
                                <label for="features" class="form-label">Features</label>
                                <textarea id="features" class="form-control" name="features" placeholder="Features"
                                    minlength="10" required onkeypress="return validateJavascript(event);">{{ $product['features'] }}</textarea>
                            </div>

                            <!-- Product Image Upload -->
                            <div class="sm:grid grid-cols-2 gap-2 mt-3">
                                <div>
                                    <label for="productImage" class="form-label">Product Image</label>
                                    <img id="thumb" width="150px" src="/{{ $product['productImage'] }}" alt="Product image"
                                        onerror="this.style.display='none';" />
                                    <input type="file" class="mt-2" name="productImage" id="productImage"
                                        onchange="preview()" accept="image/*">
                                </div>
                            </div>



                             <!-- Product FAQs Section -->
                        <div class="mt-5">
                            <label class="form-label">Product FAQs</label>
                            <div id="faqs-container">
                                @foreach($product_faqs as $index => $faq)
                                <div class="faq-item mb-3 p-3 border rounded">
                                    <input type="hidden" name="faqs[{{$index}}][id]" value="{{ $faq->id }}">
                                    <div class="grid grid-cols-12 gap-2">
                                        <div class="col-span-6">
                                            <label class="form-label">Question</label>
                                            <input type="text" name="faqs[{{$index}}][question]" 
                                                class="form-control" placeholder="Question" 
                                                value="{{ $faq->question }}" required>
                                        </div>
                                        <div class="col-span-6">
                                            <label class="form-label">Answer</label>
                                            <input type="text" name="faqs[{{$index}}][answer]" 
                                                class="form-control" placeholder="Answer" 
                                                value="{{ $faq->answer }}" required>
                                        </div>
                                        <div class="col-span-2 flex items-end">
                                            <button type="button" class="btn btn-danger remove-faq-btn" 
                                                    data-faq-id="{{ $faq->id }}">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                            <!-- Save Button -->
                            <div class="mt-5">
                                <button class="btn btn-primary shadow-md mr-2">Save</button>
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
        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function numbersOnly(e) {
            var keycode = e.keyCode;
            if ((keycode < 48 || keycode > 57) && (keycode < 96 || keycode > 105) && keycode !=
                9 && keycode != 8 && keycode != 37 && keycode != 38 && keycode != 39 && keycode != 40 && keycode != 46) {
                e.preventDefault();
            }
        }
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
    // Remove FAQ functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-faq-btn')) {
            e.preventDefault();
            const faqId = e.target.dataset.faqId;
            if (faqId) {
                // Add a hidden input for deleted FAQ
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'deleted_faqs[]';
                deleteInput.value = faqId;
                document.querySelector('form').appendChild(deleteInput);
            }
            e.target.closest('.faq-item').remove();
        }
    });
});
    </script>
    @vite('resources/js/ckeditor-classic.js')
@endsection
