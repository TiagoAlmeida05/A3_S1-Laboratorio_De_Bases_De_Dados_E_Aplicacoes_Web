@extends('layouts.app')
@section('title', $page->name)
@section('content')
<div class="container mx-auto py-10 px-4">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 border-b pb-4">
            {{ $page->name }}
        </h1>
        <div class="space-y-6">
            @php
                $blocks = json_decode($page->content, true);
            @endphp
            @if($blocks && is_array($blocks))
                @foreach($blocks as $block)
                    
                    @if($page->name === 'FAQ')
                        <details class="border rounded-lg p-4 bg-gray-50 group">
                            <summary class="font-bold cursor-pointer text-blue-600 flex justify-between items-center list-none">
                                {{ $block['heading'] }}
                            </summary>
                            <div class="mt-4 text-gray-600 leading-relaxed">
                                {!! nl2br(e($block['text'])) !!} 
                            </div>
                        </details>

                    @else
                        <div class="mb-6">
                            @if(!empty($block['heading']))
                                <h2 class="text-2xl font-bold text-gray-800 mb-3">
                                    {{ $block['heading'] }}
                                </h2>
                            @endif
                            
                            <div class="text-gray-700 leading-relaxed space-y-2">
                                {!! nl2br(e($block['text'])) !!}
                            </div>
                        </div>
                    @endif

                @endforeach
            @else
                <p class="text-gray-500 italic">No content available yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection