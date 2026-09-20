@extends('frontend.astrologers.layout.master')
@section('content')

<style>
     input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield; /* Firefox */
        }

        .card{
            border:1px solid #00000042;
        }
</style>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="text-center text-white py-2">
                    <h2 class="mb-2">{{ isset($puja) ? 'Update' : 'Add' }} Puja</h2>
                </div>
                <div class="card-body">
                    <form id="pujaForm" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Hidden field for puja_id when updating -->
                        @if(isset($puja) && $puja->id)
                            <input type="hidden" name="puja_id" value="{{ $puja->id }}">
                        @endif
                        
                        <!-- Puja Title -->
                        <div class="form-group mb-3">
                            <label for="puja_title" class="form-label">Puja Title</label>
                            <input type="text" class="form-control" id="puja_title" name="puja_title" 
                                   value="{{ isset($puja) ? $puja->puja_title : old('puja_title') }}">
                            <div id="puja_title_error" class="text-danger"></div>
                        </div>
                        
                        <!-- Puja Description -->
                        <div class="form-group mb-3">
                            <label for="long_description" class="form-label">Description</label>
                            <textarea class="form-control" id="long_description" name="long_description" rows="4">{{ isset($puja) ? $puja->long_description : old('long_description') }}</textarea>
                            <div id="long_description_error" class="text-danger"></div>
                        </div>
                        
                        <!-- Puja Image Upload -->
                        <div class="form-group mb-3">
                            <label for="puja_images" class="form-label">Puja Images</label>
                            <input type="file" class="form-control" id="puja_images" name="puja_images[]" multiple accept="image/*">
                            <div id="puja_images_error" class="text-danger"></div>
                            
                            <!-- Image Preview Container -->
                            <div class="image-preview-container mt-3 row" id="imagePreviewContainer">
                                @if(isset($puja) && $puja->puja_images)
                                    @foreach($puja->puja_images as $image)
                                        <div class="col-md-3 mb-3 image-preview-item">
                                            <img src="/{{ $image }}" class="img-thumbnail" style="height: 100px;">
                                            <button type="button" class="btn btn-danger btn-sm mt-1 remove-image" data-image="{{ $image }}">Remove</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <!-- Puja Start Date/Time -->
                            @php
                                $currentDateTime = date('Y-m-d\TH:i');
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="puja_start_datetime" class="form-label">Start Date & Time</label>
                                    <input type="datetime-local" min="{{ $currentDateTime }}" class="form-control" id="puja_start_datetime" 
                                           name="puja_start_datetime" value="{{ isset($puja) ? date('Y-m-d\TH:i', strtotime($puja->puja_start_datetime)) : old('puja_start_datetime') }}">
                                    <div id="puja_start_datetime_error" class="text-danger"></div>
                                </div>
                            </div>
                            
                            <!-- Puja End Date/Time -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="puja_duration" class="form-label">Puja Duration (in minutes)</label>
                                    <input type="number" class="form-control" id="puja_duration" placeholder="120" name="puja_duration" 
                                    value="{{ isset($puja) ? $puja->puja_duration : old('puja_title') }}">
                                    <div id="puja_duration_error" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Puja Place -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    <label for="puja_place" class="form-label">Place</label>
                                    <input type="text" class="form-control" id="puja_place" name="puja_place" 
                                           value="{{ isset($puja) ? $puja->puja_place : old('puja_place') }}">
                                    <div id="puja_place_error" class="text-danger"></div>
                                </div>
                            </div>

                            <!-- Puja Price -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    <label for="puja_price" class="form-label">Puja Price</label>
                                    <input type="number" placeholder="Enter Price" class="form-control" id="puja_price" name="puja_price" 
                                           value="{{ isset($puja) ? $puja->puja_price : old('puja_price') }}">
                                    <div id="puja_price_error" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 text-center">
                            <button type="submit" class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2">
                                {{ isset($puja) ? 'Update Puja' : 'Add Puja' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#pujaForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.text-danger').text('');
        // Get form data
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('front.astrologers.store-puja') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                toastr.success('Form Submitted Successfully')
                window.location.href = "{{ route('front.astrologers.puja-list') }}";
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.error;
                    // console.log(xhr.responseJSON);
                    $.each(errors, function(key, value) {
                        // Find the closest form group and add error message
                        var $field = $('[name="' + key + '"]');
                        var $formGroup = $field.closest('.form-group');
                        
                        // Create or use existing error element
                        var $errorElement = $formGroup.find('.text-danger');
                        if ($errorElement.length === 0) {
                            $errorElement = $('<span class="text-danger"></span>');
                            $formGroup.append($errorElement);
                        }
                        
                        $errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
});
</script>

@php
$apikey = DB::table('systemflag')->where('name', 'googleMapApiKey')->first();
@endphp
<script src="https://maps.googleapis.com/maps/api/js?key={{ $apikey->value }}&libraries=places">
</script>
<script>
    function initializeAutocomplete(inputId) {
        var input = document.getElementById(inputId);
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function(event) {
            var place = autocomplete.getPlace();
            if (place.hasOwnProperty('place_id')) {
                if (!place.geometry) {
                    return;
                }
                latitude.value = place.geometry.location.lat();
                longitude.value = place.geometry.location.lng();
            } else {
                var service = new google.maps.places.PlacesService(document.createElement('div'));
                service.textSearch({
                    query: place.name
                }, function(results, status) {
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        latitude.value = results[0].geometry.location.lat();
                        longitude.value = results[0].geometry.location.lng();
                    }
                });
            }
        });
    }
    initializeAutocomplete('puja_place');

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pujaImagesInput = document.getElementById('puja_images');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        
        pujaImagesInput.addEventListener('change', function(event) {
            // Clear existing previews (except for existing images if updating)
            const existingPreviews = imagePreviewContainer.querySelectorAll('.image-preview-item:not([data-existing])');
            existingPreviews.forEach(preview => preview.remove());
            
            // Add new previews for selected files
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'col-md-3 mb-3 image-preview-item';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';
                        img.style.height = '100px';
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-danger btn-sm mt-1 remove-image';
                        removeBtn.textContent = 'Remove';
                        removeBtn.onclick = function() {
                            previewItem.remove();
                        };
                        
                        previewItem.appendChild(img);
                        previewItem.appendChild(removeBtn);
                        imagePreviewContainer.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        
        // Handle removal of existing images (for update scenario)
        document.querySelectorAll('.remove-image[data-image]').forEach(btn => {
            btn.addEventListener('click', function() {
                const imagePath = this.getAttribute('data-image');
                // You might want to add a hidden input to track images to be deleted
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'images_to_delete[]';
                deleteInput.value = imagePath;
                document.getElementById('pujaForm').appendChild(deleteInput);
                
                // Remove the preview
                this.parentElement.remove();
            });
        });
    });
    </script>


@endsection