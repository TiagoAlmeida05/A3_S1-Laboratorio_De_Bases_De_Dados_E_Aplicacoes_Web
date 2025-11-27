@extends('layouts.admin')

@section('content')
    <h1>Manage Content</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Whistleblower</th>
                <th>Problem</th>
                <th>State</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->date->format('d/m/Y H:i') }}</td>
                    
                    <td>{{ $report->reporter->name ?? 'User Desconhecido' }}</td>
                    
                    <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $report->description }}">
                        {{ Str::limit($report->description, 80) }}
                    </td>
                    
                    <td>
                        @if($report->solved)
                            <span">Solved</span>
                        @else
                            <span>Pending</span>
                        @endif
                    </td>
                    
                    <td>
                        @if(!$report->solved)
                            <form action="{{ route('admin.reports.solve', $report->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="button">
                                    Mark as solved
                                </button>
                            </form>
                        @else
                            <span>Closed by admin #{{ $report->handled_by_id }}</span>
                            <form action="{{ route('admin.reports.reopen', $report->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="button">
                                    Reopen
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div>
        {{ $reports->links() }}
    </div>
@endsection