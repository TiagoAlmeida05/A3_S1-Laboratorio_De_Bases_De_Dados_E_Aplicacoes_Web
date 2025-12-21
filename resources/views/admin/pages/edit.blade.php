@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <div class="bg-white shadow rounded-lg p-6 max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">
                Editing: <span class="text-blue-600">{{ $page->name }}</span>
            </h1>
            <a href="{{ route('admin.pages') }}" class="text-gray-500 hover:text-gray-700">Cancel</a>
        </div>

        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Container for the dynamic inputs --}}
            <div id="blocks-container" class="space-y-6">
                @php
                    $blocks = json_decode($page->content, true) ?? [];
                    if(empty($blocks)) $blocks = [['heading' => '', 'text' => '']];
                    
                    if ($page->name == 'FAQ') {
                        $headingLabel = 'Question';
                        $textLabel = 'Answer';
                    } 
                    elseif ($page->name == 'Contact us') {
                        $headingLabel = 'Name (leave empty for introductory text)';
                        $textLabel = 'Email (or introductory message)';
                    } 
                    else {
                        $headingLabel = 'Section Heading';
                        $textLabel = 'Content Text';
                    }
                @endphp

                @foreach($blocks as $index => $block)
                <div class="content-block border border-gray-200 p-4 rounded bg-gray-50 relative shadow-sm">
                    {{-- Remove Button --}}
                    <button type="button" onclick="this.closest('.content-block').remove()" 
                            class="absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold text-sm px-2 py-1">
                        &times; Remove
                    </button>

                    {{-- HEADING INPUT --}}
                    <div class="mb-3">
                        <label class="block font-bold text-gray-700 mb-1 text-sm">{{ $headingLabel }}</label>
                        <input type="text" name="headings[]" value="{{ $block['heading'] }}" 
                            class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2" 
                            placeholder="{{ $page->name == 'FAQ' ? 'e.g., How do I sign up?' : ($page->name == 'Contact us' ? 'Contact name/none' : 'Our Mission') }}">
                    </div>

                    {{-- TEXT INPUT --}}
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-sm">{{ $textLabel }}</label>
                        <textarea name="texts[]" rows="4" 
                                class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2"
                                placeholder="{{ $page->name == 'Contact us' ? 'Contact e-mail' : 'Type the content here...' }}">{{ $block['text'] }}</textarea>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Add Button --}}
            <button type="button" id="add-block-btn" 
                    class="mt-4 w-full border-2 border-dashed border-gray-300 text-gray-600 font-semibold py-3 px-4 rounded hover:bg-gray-50 hover:border-blue-400 transition">
                + Add New Section
            </button>

            {{-- Save Button --}}
            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT TO ADD NEW ROWS --}}
<script>
    document.getElementById('add-block-btn').addEventListener('click', function() {
        const headingLabel = "{{ $headingLabel }}";
        const textLabel = "{{ $textLabel }}";
        
        const newBlock = `
            <div class="content-block border border-gray-200 p-4 rounded bg-gray-50 relative shadow-sm mt-4">
                <button type="button" onclick="this.closest('.content-block').remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold text-sm px-2 py-1">&times; Remove</button>
                
                <div class="mb-3">
                    <label class="block font-bold text-gray-700 mb-1 text-sm">${headingLabel}</label>
                    <input type="text" name="headings[]" class="w-full border-gray-300 rounded shadow-sm p-2" placeholder="Enter name...">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm">${textLabel}</label>
                    <textarea name="texts[]" rows="4" class="w-full border-gray-300 rounded shadow-sm p-2"></textarea>
                </div>
            </div>
        `;
        document.getElementById('blocks-container').insertAdjacentHTML('beforeend', newBlock);
    });
</script>
@endsection