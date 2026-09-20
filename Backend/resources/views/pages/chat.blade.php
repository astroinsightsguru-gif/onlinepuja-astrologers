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
                    <div class="flex flex-col sm:flex-row border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 flex-none image-fit relative">
                                <img class="rounded-full" style="width: 100%; height: 100%;" src="/{{ $data['userProfile'] }}"
                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                    alt="{{ucfirst($professionTitle)}} image" />
                            </div>
                            <div class="ml-3 mr-auto">
                                <div class="font-medium text-base">{{ $data['userName'] }}</div>
                            </div>
                        </div>
                        <div
                            class="flex items-center sm:ml-auto mt-5 sm:mt-0 border-t sm:border-0 border-slate-200/60 pt-3 sm:pt-0 -mx-5 sm:mx-0 px-5 sm:px-0">
                        </div>
                    </div>
                    <div class="overflow-y-scroll  px-5 pt-5 flex-1" style="overflow-y:auto ">
                        @foreach ($messages as $msg)
                        @if (isset($msg['fields']['userId2']['stringValue']) && $msg['fields']['userId2']['stringValue'] == $data['ticketId'])
                            <div class="chat__box__text-box flex items-end float-left mb-4">
                                <div class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md">
                                    {{ $msg['fields']['message']['stringValue'] ?? '' }}
                                </div>
                            </div>
                            <div class="clear-both"></div>
                        @else
                            <div class="chat__box__text-box flex items-end mb-4" style="float: right">
                                <div class="bg-primary px-4 py-3 text-white rounded-l-md rounded-t-md ">
                                    {{ $msg['fields']['message']['stringValue'] ?? '' }}
                                </div>
                            </div>
                            <div class="clear-both"></div>
                        @endif
                    @endforeach
                    
                    </div>
                    <form method="POST" enctype="multipart/form-data" style="width:100%" id="create-chat">
                        <div
                            class="ml-3 pt-4 pb-10 sm:py-4 flex items-center border-t border-slate-200/60 dark:border-darkmode-400">
                            @if ($data['ticketStatus'] != 'CLOSED')
                                @csrf
                                <input type="hidden" name="messageCount" value="{{ count($messages) }}">
                                <input type="hidden" name="chatId" value="{{ $data['chatId'] }}">
                                <input type="hidden" name="senderId" value="{{ $data['userId'] }}">
                                <input type="hidden" name="ticketId" value="{{ $data['ticketId'] }}">
                                <input type="hidden" name="ticketStatus" value="{{ $data['ticketStatus'] }}">
                                <textarea
                                    class="chat__box__input form-control dark:bg-darkmode-600 h-16 resize-none border-transparent px-5 py-3 shadow-none focus:border-transparent focus:ring-0"
                                    rows="1" id="message" name="message" placeholder="Type your message..."></textarea>
                                <div style="position:static"
                                    class="flex absolute sm:static left-0 bottom-0 ml-5 sm:ml-0 mb-5 sm:mb-0">
                                </div>
                                <button type="submit"
                                    class="w-8 h-8 sm:w-10 sm:h-10 block bg-primary text-white rounded-full flex-none flex items-center justify-center mr-5">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
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
    </script>
@endsection
