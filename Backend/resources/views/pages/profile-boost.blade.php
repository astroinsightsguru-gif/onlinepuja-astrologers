@extends('../layout/' . $layout)
@section('subhead')
<title>Profile Boost</title>
@endsection
@section('subcontent')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 mt-2">
        <div class="intro-y box">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">Profile Boost</h2>
            </div>
            <div class="p-5">

                <form action="{{ isset($profileBoost) ? route('profile-boost.update', $profileBoost->id) : route('profile.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Title and Subtitle (Col-6 Col-6) -->
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 sm:col-span-6">
                            <label for="title" class="form-label">Chat Commission</label>
                            <input type="text" name="chat_commission" id="chat_commission" class="form-control w-full"
                                value="{{$profileBoost->chat_commission ?? ''}}" placeholder="Enter Chat Commission">
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="subtitle" class="form-label">Call Commission</label>
                            <input type="text" name="call_commission" id="call_commission" class="form-control w-full"
                                value="{{$profileBoost->call_commission ?? ''}}" placeholder="Enter Call Commission">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6">

                        <div class="col-span-12 sm:col-span-6 mt-3">
                            <label for="subtitle" class="form-label">Profile Monthly Boost </label>
                            <input type="text" name="profile_boost" id="profile_boost" class="form-control w-full"
                                value="{{$profileBoost->profile_boost ?? ''}}" placeholder="Enter Profile Monthly Boost">
                        </div>
                    </div>

                    @php
                    $profileBoostBenefits = $profileBoost->profile_boost_benefits;
                    @endphp
                    <!-- Benefits Section -->
                    <div class="border border-gray-300 p-4 rounded mt-5">
                        <h3 class="text-lg font-medium">Profile Boost Benefits</h3>
                        <button type="button" id="add-benefit" class="btn btn-outline-primary mt-3">+ Add
                            Benefit</button>

                        <div id="profile_boost-benefits" class="grid grid-cols-12 gap-6 mt-3">
                        @if (!empty($profileBoostBenefits))
                             @foreach ($profileBoostBenefits as $benefit)
                                    <div class="col-span-12 sm:col-span-6 relative border border-gray-300 p-4 rounded mt-3">
                                        <h3 class="font-bold mb-2"> Benefit </h3>

                                        <textarea name="profile_boost_benefits[]"
                                            class="form-control w-full border border-gray-300 p-2 rounded"
                                            placeholder="Enter benefit description">{{ $benefit }}</textarea>

                                        <button type="button"
                                            class="absolute top-0 right-0 bg-red-500 text-danger border border-gray-800 rounded-full w-5 h-5 flex items-center justify-center cursor-pointer text-sm badge-button shadow-md">×</button>
                                    </div>
                                    @endforeach
                                 @endif
                        </div>
                    </div>
                    <!-- end sections -->
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let benefitCount = 0; // Initialize counter

        document.getElementById('add-benefit').addEventListener('click', function () {
            const benefitContainer = document.getElementById('profile_boost-benefits');

            // Increment the counter
            benefitCount++;

            // Create benefit section
            const newBenefit = document.createElement('div');
            newBenefit.classList.add('col-span-12', 'sm:col-span-6', 'relative', 'border', 'border-gray-300', 'p-4', 'rounded', 'mt-3');

            // Create heading for benefit
            const heading = document.createElement('h3');
            heading.textContent = ` Benefit `;
            heading.classList.add('font-bold', 'mb-2');

            // Create textarea for benefit description
            const descriptionTextarea = document.createElement('textarea');
            descriptionTextarea.name = 'profile_boost_benefits[]';
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
