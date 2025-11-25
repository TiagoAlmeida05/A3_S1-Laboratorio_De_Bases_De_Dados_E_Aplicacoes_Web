@extends('layouts.admin')

@section('content')
    <h1>Gerir Conteúdo / Denúncias (US57)</h1>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Denunciante</th>
                <th>Descrição do Problema</th>
                <th>Estado</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->date->format('d/m/Y H:i') }}</td>
                    
                    <td>{{ $report->reporter->name ?? 'Desconhecido' }}</td>
                    
                    <td>{{ $report->description }}</td>
                    
                    <td>
                        @if($report->solved)
                            <span style="color:green; font-weight:bold;">Resolvido</span>
                        @else
                            <span style="color:red; font-weight:bold;">Pendente</span>
                        @endif
                    </td>
                    
                    <td>
                        @if(!$report->solved)
                            <form action="{{ route('admin.reports.solve', $report->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="button button-outline" style="color:green; border-color:green;">
                                    Marcar Resolvido
                                </button>
                            </form>
                        @else
                            <small>Resolvido por Admin #{{ $report->handled_by_id }}</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection