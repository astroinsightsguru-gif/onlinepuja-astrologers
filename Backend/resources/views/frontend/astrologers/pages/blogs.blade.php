@extends('frontend.astrologers.layout.master')
<style>
    .loader_full__LR0ml {
    transition: opacity .4s ease;
}
.loader_image__D6P69 {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.object-cover {
    -o-object-fit: cover;
    object-fit: cover;
}

.read-more
        {
            color :blue;
        }

</style>

@section('content')
<div class="py-5 bg-light">
    <div class="container d-flex flex-column gap-5">
        <h2 class="position-relative border-bottom pb-2 text-dark">
            Ours Blogs
            <span class="position-absolute bottom-0 start-50 translate-middle-x bg-warning d-block rounded"
                  style="width: 110px; height: 3px; margin-top: -1px;">
            </span>
        </h2>
        <div class="row justify-content-strat">
            @if (isset($bloglist) && count($bloglist)>0)
            @foreach ($bloglist as $blog)
            <div class="col-md-4 mt-4">
                <a href="{{ route('front.astrologers.getBlogDetails', ['slug' => $blog->slug]) }}" class="text-decoration-none">
                    <div class="product-card parad-shivling shadow-sm overflow-hidden p-0">
                        <div class="position-relative" style="height:250px;">
                            @php
                                $extension = pathinfo($blog->blogImage, PATHINFO_EXTENSION);
                                $videoExtensions = ['mp4', 'webm', 'ogg'];
                            @endphp
        
                            {{-- <!-- View count badge -->
                            <div class="position-absolute top-0 right-0 bg-black bg-opacity-50  p-2 rounded-bl-lg" style="z-index: 1000">
                                <i class="fa-regular fa-eye"></i> {{ $blog->viewer }}
                            </div> --}}
        
                            @if(in_array($extension, $videoExtensions))
                                <video class="product-image position-absolute w-100 h-100 d-flex" controls>
                                    <source src="{{ asset($blog->blogImage) }}" type="video/{{ $extension }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <img src="{{ asset($blog->blogImage) }}" 
                                    class="product-image position-absolute w-100 h-100" 
                                    style="top: 0; left: 0; object-fit: cover;">
                            @endif
                        </div>
                        <div class="p-3 text-left">
                            <h3 class="font-weight-700">{{ $blog->title }}</h3>
                            <p class="text-dark">
                                {!! \Illuminate\Support\Str::words($blog->description, 15) !!}
                            </p>
                            <span class="mt-1 text-blue-500 group-hover:text-black text-sm group-hover:underline read-more">
                                Read More →
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
       
        </div>
        @else
        <h3 class="mt-5 mb-5 text-center">No Blog Available</h3>
        @endif
        <!-- Pagination Controls -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $bloglist->links() }}
        </div>

    </div>
</div>

@endsection

