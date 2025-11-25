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
                            <span style="color:green; font-weight:bold;">Solved</span>
                        @else
                            <span style="color:red; font-weight:bold;">Pending</span>
                        @endif
                    </td>
                    
                    <td>
                        @if(!$report->solved)
                            <form action="{{ route('admin.reports.solve', $report->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="button button-outline" style="color:green; border-color:green;">
                                    Mark as solved
                                </button>
                            </form>
                        @else
                            <span style="color:gray; font-size:0.8em;">Closed by admin #{{ $report->handled_by_id }}</span>
                            <form action="{{ route('admin.reports.reopen', $report->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="button button-outline" style="border-color:red; color:red; font-size:0.9em; padding: 0 10px; height: 30px; line-height: 28px;">
                                    Reopen
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 20px;">
        {{ $reports->links() }}
    </div>
@endsection