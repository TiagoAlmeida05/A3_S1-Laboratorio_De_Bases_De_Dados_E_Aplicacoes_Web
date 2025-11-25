@extends('layouts.admin')

@section('content')
    <h1>Gerir Páginas Estáticas (US58)</h1>

    <table>
        <thead>
            <tr>
                <th>Nome da Página</th>
                <th>Última Edição Por</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
                <tr>
                    <td style="font-weight: bold; color: #9b4dca;">{{ $page->name }}</td>
                    
                    <td>
                        {{ $page->editor->name ?? 'Sistema' }}
                    </td>
                    
                    <td>
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="button button-outline">
                            Editar Conteúdo
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection