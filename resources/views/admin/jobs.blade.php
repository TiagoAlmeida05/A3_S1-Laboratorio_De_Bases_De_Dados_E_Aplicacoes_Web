@extends('layouts.admin')

@section('content')
    <h1>Gestão de Ofertas (US56)</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->title }}</td>
                    <td style="color: {{ $job->status == 'Active' ? 'green' : 'orange' }}">
                        {{ $job->status }}
                    </td>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            @if($job->status !== 'Active')
                                <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="button button-outline" style="color:green; border-color:green;">Aprovar</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.jobs.delete', $job->id) }}" method="POST" onsubmit="return confirm('Apagar?');">
                                @csrf @method('DELETE')
                                <button class="button button-outline" style="color:red; border-color:red;">Remover</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection