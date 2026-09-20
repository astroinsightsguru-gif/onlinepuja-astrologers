@extends('../layout/' . $layout)

@section('subhead')
    <title>ContactUs List</title>
@endsection

@section('subcontent')
<link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />


    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Calls Monitoring</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form method="GET" action="{{ route('calls.monitoring') }}" id="searchForm">
                    
                    <select name="astrologerId" id="astrologerId">
                        <option value="">-- Select {{ucfirst($professionTitle)}} --</option>
                        @foreach($astrologers as $astrologer)
                            <option value="{{ $astrologer->id }}" {{ request('astrologerId') == $astrologer->id ? 'selected' : '' }}>
                                {{ $astrologer->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="userId" id="userId">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('userId') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->contactNo }})
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="date" id="date" value="{{ request('date') }}">

                   <button type="submit" class="btn btn-primary">
                        <i data-lucide="search"  class="w-4 h-4 mr-1"></i> Search
                    </button>
                
                    <button type="button" id="clearButton" class="btn btn-secondary">
                        <i data-lucide="x"  class="w-4 h-4 mr-1"></i> Clear
                    </button>
                </form>
            </div>
        </div>
    </div>
    @if ($totalRecords > 0)
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2" aria-label="customer-list">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">Counsellor Name</th>
                        <th class="whitespace-nowrap">User Name</th>
                        <th class="whitespace-nowrap">Date</th>
                        <th class="whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($completedCalls as $clist)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>

                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->astrologerName ? $clist->astrologerName : $clist->contactNo }}
                                    
                                </div>
                            </td>
                           
                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->Username ? $clist->Username : $clist->contactNo }}
                                     
                                </div>
                            </td>
                             <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->created_at ? date("d-m-Y h:i a" , strtotime($clist->created_at)) : '--' }}</div>
                            </td>

                           <td>
                                <?php
                                    $bucketname = DB::table('systemflag')->where('name', 'GoogleBucketName')->select('value')->first();
                                    $file = "https://storage.googleapis.com/{$bucketname->value}/{$clist->sId}_{$clist->channelName}.m3u8";
                                ?>
                                @if(!empty($clist->sId))
                                <a class="flex items-center mr-3 text-success" href="javascript:void(0);" onclick="toggleAudio('{{ $clist->callId }}', '{{$file}}', this)">
                                    ▶️ Play
                                </a>
                                @else
                                <p>---</p>
                                @endif
                            </td>
                        
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
        <!-- BEGIN: Pagination -->
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline intro-y col-span-12 addbtn ">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('calls.monitoring', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('calls.monitoring', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('calls.monitoring', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <link href="https://cdn.jsdelivr.net/npm/lucide-icons@latest/dist/lucide.min.css" rel="stylesheet">

@endsection
@section('script')
<script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>

    <!-- Optional: hls.js for additional HLS support -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    
   <script>
   let currentPlayer = null; // Track the currently playing player

    function toggleAudio(callId, file, element) {
        console.log(file);

        // If a different player is already playing, stop it
        if (currentPlayer && currentPlayer.id !== `hls-player-${callId}`) {
            videojs(currentPlayer.id).pause();
            currentPlayer.style.display = 'none';
            currentPlayer = null;
        }

        // Check if player already exists for this callId
        let existingPlayer = document.getElementById(`hls-player-${callId}`);
        if (!existingPlayer) {
            // Create new player if not exists
            const playerContainer = document.createElement('div');
            playerContainer.innerHTML = `
                <video
                    id="hls-player-${callId}"
                    class="video-js vjs-default-skin"
                    controls
                    width="200"
                    height="150"
                    style="display: block;"
                ></video>
            `;
            element.parentNode.appendChild(playerContainer);

            const video = videojs(`hls-player-${callId}`);
            video.src({
                src: file,
                type: 'application/vnd.apple.mpegurl'
            });
            video.play();

            currentPlayer = document.getElementById(`hls-player-${callId}`);
        } else {
            // If player exists, toggle visibility and play/pause
            if (existingPlayer.style.display === 'none') {
                existingPlayer.style.display = 'block';
                videojs(`hls-player-${callId}`).play();
                currentPlayer = existingPlayer;
            } else {
                videojs(`hls-player-${callId}`).pause();
                existingPlayer.style.display = 'none';
                currentPlayer = null;
            }
        }
    }
</script>


    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
     <script>
    document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('searchForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('calls.monitoring') }}"; // Redirect to remove query parameters
    });
</script>
@endsection
