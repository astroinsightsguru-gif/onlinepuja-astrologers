@extends('../layout/' . $layout)

@section('subhead')
<title>Add Course Chapters</title>
@endsection

@section('subcontent')
<style>
    .upload__inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .upload__btn {
        display: inline-block;
        font-weight: 600;
        color: #fff;
        text-align: center;
        min-width: 116px;
        padding: 5px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid;
        background-color: #4045ba;
        border-color: #4045ba;
        border-radius: 10px;
        line-height: 26px;
        font-size: 14px;
    }

    .upload__btn:hover {
        background-color: unset;
        color: #4045ba;
        transition: all 0.3s ease;
    }

    .upload__btn-box {
        margin-bottom: 10px;
    }

    .upload__img-wrap {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .upload__img-box {
        width: 200px;
        padding: 0 10px;
        margin-bottom: 12px;
    }

    .upload__img-close {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: rgba(0, 0, 0, 0.5);
        position: absolute;
        top: 10px;
        right: 10px;
        text-align: center;
        line-height: 24px;
        z-index: 1;
        cursor: pointer;
    }

    .upload__img-close:after {
        content: "✖";
        font-size: 14px;
        color: white;
    }

    .img-bg {
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        position: relative;
        padding-bottom: 100%;
    }
</style>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 mt-2">
        <div class="intro-y box">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">Add Chapters</h2>
            </div>
            <div class="p-5">
                <form action="{{ isset($chapters) ? route('updateCourseChapter', $chapters->id) : route('addCourseChapter') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Title and Subtitle (Col-6 Col-6) -->
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 sm:col-span-6">
                            <label for="chapter_name" class="form-label">Chapter Name</label>
                            <input type="text" name="chapter_name" id="chapter_name" class="form-control w-full"
                                value="{{$chapters->chapter_name ?? ''}}" placeholder="Enter title">
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="course_id" class="form-label">Course</label>
                            <select name="course_id" id="course_id" class="form-select w-full">
                                <option value="">Select Category</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ (isset($chapters) && $chapters->course_id == $course->id) ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!-- Description (Full Width) -->
                    <div class="mt-5">
                        <label for="chapter_description" class="form-label">Chapter Description</label>
                        <textarea name="chapter_description" id="chapter_description" class="form-control w-full"
                            placeholder="Enter description">{{$chapters->chapter_description ?? ''}}</textarea>
                    </div>



                    <div class="grid grid-cols-12 gap-6 mt-5" >
                        <div class="col-span-12 sm:col-span-6">
                            <label for="youtube_link" class="form-label">Video (Youtube Link)</label>
                            <input type="text" name="youtube_link" id="youtube_link" class="form-control w-full"
                                value="{{$chapters->youtube_link ?? ''}}" placeholder="Enter Link">
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="chapter_document" class="form-label">Document (Add pdf as lessons)</label>

                            <!-- File Input -->
                            <input type="file" name="chapter_document" id="chapter_document" class="form-file-control w-full" accept="application/pdf">

                            <!-- Span to show the selected or previously uploaded file name -->
                            <span id="fileNameDisplay" class="text-sm text-gray-600 mt-2">
                                @if(!empty($chapters->chapter_document))
                                    {{ basename($chapters->chapter_document) }}
                                @else
                                    No file selected
                                @endif
                            </span>
                        </div>


                    </div>

                    <!-- end sections -->
                    <div class="upload__box mt-5">
                        <div class="upload__btn-box">
                            <label class="upload__btn">
                                <p>Upload images</p>
                                <input type="file" multiple="" name="chapter_images[]" data-max_length="20"
                                    class="upload__inputfile">
                            </label>
                        </div>
                        <div class="upload__img-wrap">
                            @php
                                $chapterImages = $chapters->chapter_images ?? [];
                                if (is_string($chapterImages)) {
                                    $chapterImages = json_decode($chapterImages, true) ?? [];
                                }
                            @endphp
                            @if(isset($chapters) && !empty($chapterImages) && is_array($chapterImages))
                                @foreach ($chapterImages as $imgkey => $img)
                                    <div class="upload__img-box">
                                        <div style="background-image: url('{{ asset($img) }}');" class="img-bg"
                                            data-file="{{ $img }}">
                                            <input type="file" name="old_images[]" multiple style="display:none;">
                                            <input type="hidden" name="existing_images[]" value="{{ $img }}">
                                            <div class="upload__img-close" name="removed_images">×</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    document.getElementById('chapter_document').addEventListener('change', function(event) {
            var fileName = event.target.files.length ? event.target.files[0].name : "No file chosen";
            document.getElementById('fileNameDisplay').textContent = fileName;
        });


    $(document).ready(function () {
        jQuery('.select2').select2({
            allowClear: true,
            tokenSeparators: [',', ' ']
        });
    });

    jQuery(document).ready(function () {
        ImgUpload();
    });

    function ImgUpload() {
        var imgWrap = "";
        var imgArray = [];

        $('.upload__inputfile').each(function () {
            $(this).on('change', function (e) {
                imgWrap = $(this).closest('.upload__box').find('.upload__img-wrap');
                var maxLength = $(this).attr('data-max_length');

                var files = e.target.files;
                var filesArr = Array.prototype.slice.call(files);
                var iterator = 0;
                filesArr.forEach(function (f) {

                    if (!f.type.match('image.*')) {
                        return;
                    }

                    if (imgArray.length >= maxLength) {
                        return false;
                    } else {
                        imgArray.push(f);

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var html = "<div class='upload__img-box'><div style='background-image: url(" + e.target.result + ")' data-file='" + f.name + "' class='img-bg'><div class='upload__img-close'></div></div></div>";
                            imgWrap.append(html);
                            iterator++;
                        }
                        reader.readAsDataURL(f);
                    }
                });
            });
        });

        $('body').on('click', ".upload__img-close", function () {
            var file = $(this).parent().data("file");
            imgArray = imgArray.filter(f => f.name !== file);
            $(this).closest('.upload__img-box').remove();
        });

        // Optionally reset imgArray on form submit
        $('form').on('submit', function () {
            imgArray = [];
        });
    }



    document.addEventListener('DOMContentLoaded', function () {
        let benefitCount = 0; // Initialize counter

        document.getElementById('add-benefit').addEventListener('click', function () {
            const benefitContainer = document.getElementById('puja-benefits');

            // Increment the counter
            benefitCount++;

            // Create benefit section
            const newBenefit = document.createElement('div');
            newBenefit.classList.add('col-span-12', 'sm:col-span-6', 'relative', 'border', 'border-gray-300', 'p-4', 'rounded', 'mt-3');

            // Create heading for benefit
            const heading = document.createElement('h3');
            heading.textContent = ` Benefit `;
            heading.classList.add('font-bold', 'mb-2');

            // Create input for benefit title
            const titleInput = document.createElement('input');
            titleInput.type = 'text';
            titleInput.name = 'benefit_title[]';
            titleInput.classList.add('form-control', 'w-full', 'mb-2', 'border', 'border-gray-300', 'p-2', 'rounded');
            titleInput.placeholder = 'Enter benefit title';

            // Create textarea for benefit description
            const descriptionTextarea = document.createElement('textarea');
            descriptionTextarea.name = 'benefit_description[]';
            descriptionTextarea.classList.add('form-control', 'w-full', 'border', 'border-gray-300', 'p-2', 'rounded');
            descriptionTextarea.placeholder = 'Enter benefit description';

            // Create remove button (badge style)
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.innerHTML = '&times;';
            removeButton.classList.add('absolute', 'top-0', 'right-0', 'bg-red-500', 'text-danger', 'border', 'border-gray-800', 'rounded-full', 'w-5', 'h-5', 'flex', 'items-center', 'justify-center', 'cursor-pointer', 'text-sm', 'badge-button', 'shadow-md');

            // Remove benefit section on button click
            removeButton.addEventListener('click', function () {
                benefitContainer.removeChild(newBenefit);
            });

            // Append heading, title input, description textarea, and remove button to the new benefit
            newBenefit.appendChild(heading);
            newBenefit.appendChild(titleInput);
            newBenefit.appendChild(descriptionTextarea);
            newBenefit.appendChild(removeButton);

            // Append the new benefit to the benefit container
            benefitContainer.appendChild(newBenefit);
        });

        // Function to get ordinal number
        function ordinalNumber(num) {
            const suffix = ['th', 'st', 'nd', 'rd'];
            const value = num % 100;
            return num + (suffix[(value - 20) % 10] || suffix[value] || suffix[0]);
        }
    });

</script>
@endsection
