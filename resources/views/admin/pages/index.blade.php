@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <div class="mb-4">
        <h1 class="h3 text-secondary">Info Pages Management</h1>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">Page Name</th>
                        <th class="py-3">Last Edited By</th>
                        <th class="py-3 text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr>
                            {{-- Nome da Página --}}
                            <td class="ps-4 fw-bold text-dark">
                                {{ $page->name }}
                            </td>
                            
                            {{-- Editor --}}
                            <td class="text-secondary">
                                {{ $page->editor->name ?? 'System' }}
                            </td>
                            
                            {{-- Botão de Ação --}}
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-outline-primary fw-medium">
                                    Edit Page
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection