@extends('../layout/' . $layout)

@section('subhead')
    <title>Chat</title>
    <style>
        .clear-both {
            clear: both
        }

        .bg-primary {
            background-color: #074e62 !important
        }
        .chat__box__text-box {
    max-width: 60%; /* Adjust based on your needs */
}

.chat__box__text-box p {
    margin-bottom: 4px; /* Space between message and timestamp */
}

.chat__box__text-box span {
    display: block;
    font-size: 0.59rem; /* Smaller font for timestamp */
    margin-top: 4px;
}

    </style>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Chat</h2>
    </div>
    <div class="intro-y chat grid grid-cols-12 gap-5 mt-5">
        <!-- END: Chat Side Menu -->
        <!-- BEGIN: Chat Content -->
        <div class="intro-y col-span-12 lg:col-span-12 2xl:col-span-12">
            <div class="chat__box box">
                <!-- BEGIN: Chat Active -->
                <div class=" h-full flex flex-col">
                  <div class="overflow-y-scroll px-5 pt-5 flex-1" style="overflow-y:auto;">
                    @foreach ($messages as $msg)
                    @if (isset($msg['fields']['userId2']['stringValue']) && $msg['fields']['userId2']['stringValue'] == $user->astrologerId)
                        <!-- Message from the user -->
                        <div class="chat__box__text-box flex items-start float-left mb-4">
                            <img src="{{ $user->userProfile ? asset($user->userProfile) : asset('build/assets/images/person.png') }}" alt="Avatar" class="mr-3 rounded-full w-10 h-10 object-cover">
                            <div class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md relative">
                                <p class="mb-0">{{ $msg['fields']['message']['stringValue'] ?? '' }}</p>
                                @if (!empty($msg['fields']['attachementPath']['stringValue']))
                                        @php
                                        $attachmentPath = trim($msg['fields']['attachementPath']['stringValue']);
                                        $filePathWithoutParams = explode('?', $attachmentPath)[0];
                                        $fileExtension = strtolower(pathinfo($filePathWithoutParams, PATHINFO_EXTENSION));
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                    @endphp

                                    @if (in_array($fileExtension, $imageExtensions))
                                        <!-- Render image -->
                                        <img src="{{ $attachmentPath }}" alt="Attachment" class="mt-2 rounded-lg w-40 h-auto object-cover" onclick="previewImage(this)">
                                    @else
                                        <!-- Render file attachment -->
                                        <div class="file-attachment mt-2" onclick="downloadFile('{{ $attachmentPath }}')">
                                            <i data-lucide="file"></i>
                                            <p class="mt-2">Attachment</p>
                                        </div>
                                    @endif
                                @endif
                                <span class="text-xs text-gray-500 float-right">
                                    {{ \Carbon\Carbon::parse($msg['fields']['createdAt']['timestampValue'], 'UTC')->setTimezone('Asia/Kolkata')->format('d-m-Y h:i a') }}
                                </span>
                            </div>
                        </div>
                        <div class="clear-both"></div>
                    @else
                        <!-- Message from the astrologer -->
                        <div class="chat__box__text-box flex items-start mb-4" style="float: right;">
                            <div class="bg-primary px-4 py-3 text-white rounded-l-md rounded-t-md relative">
                                <p class="mb-0">{{ $msg['fields']['message']['stringValue'] ?? '' }}</p>
                                @if (!empty($msg['fields']['attachementPath']['stringValue']))
                                @php
                                    $attachmentPath = trim($msg['fields']['attachementPath']['stringValue']);
                                    $filePathWithoutParams = explode('?', $attachmentPath)[0];
                                    $fileExtension = strtolower(pathinfo($filePathWithoutParams, PATHINFO_EXTENSION));
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                 @endphp
                                    @if (in_array($fileExtension, $imageExtensions))
                                        <!-- Render image -->
                                        <img src="{{ $attachmentPath }}" alt="Attachment" class="mt-2 rounded-lg w-40 h-auto object-cover" onclick="previewImage(this)">
                                    @else
                                        <!-- Render file attachment -->
                                        <div class="file-attachment mt-2" onclick="downloadFile('{{ $attachmentPath }}')">
                                            <i data-lucide="file"></i>
                                            <p class="mt-2">Attachment</p>
                                        </div>
                                    @endif
                                @endif
                                <span class="text-xs text-gray-200 float-right">
                                    {{ \Carbon\Carbon::parse($msg['fields']['createdAt']['timestampValue'], 'UTC')->setTimezone('Asia/Kolkata')->format('d-m-Y h:i a') }}
                                </span>
                            </div>
                            <img src="{{ $user->astroImg ? asset($user->astroImg) : asset('/build/assets/images/person.png') }}" alt="Avatar" class="ml-3 rounded-full w-10 h-10 object-cover">
                        </div>
                        <div class="clear-both"></div>
                    @endif
                @endforeach
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
<script>
function previewImage(img) {
    const preview = document.createElement("div");
    preview.style.position = "fixed";
    preview.style.top = "0";
    preview.style.left = "0";
    preview.style.width = "100vw";
    preview.style.height = "100vh";
    preview.style.background = "rgba(0, 0, 0, 0.8)";
    preview.style.display = "flex";
    preview.style.alignItems = "center";
    preview.style.justifyContent = "center";
    preview.style.zIndex = "1000";
    preview.onclick = () => document.body.removeChild(preview);

    const imgElement = document.createElement("img");
    imgElement.src = img.src;
    imgElement.style.maxWidth = "90%";
    imgElement.style.maxHeight = "90%";
    imgElement.style.borderRadius = "8px";

    preview.appendChild(imgElement);
    document.body.appendChild(preview);
}
</script>
    <script type="text/javascript">
    var spinner = $('.loader');
        jQuery('#create-chat').submit(function(e) {
            e.preventDefault();
            spinner.show();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('createChat') }}",
                data: new FormData(this),
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        spinner.hide();
                        location.reload();
                    } else {
                        spinner.hide();
                    }
                }
            });
        });
        $(window).on('load', function() {
            $('.loader').hide();
        })


        function downloadFile(url) {
            window.open(url, '_blank');

        }
    </script>
@endsection
