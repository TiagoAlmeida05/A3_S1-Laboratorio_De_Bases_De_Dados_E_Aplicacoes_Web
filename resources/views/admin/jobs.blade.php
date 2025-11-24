<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gerir Jobs</title>
    <style>
        /* Estilo básico só para não ficar feio */
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .status-Active { color: green; font-weight: bold; }
        .status-Pending { color: orange; font-weight: bold; }
        .status-Closed { color: red; }
    </style>
</head>
<body>

    <h1>Gestão de Ofertas (US56)</h1>
    
    <p>
        <a href="/">Voltar à Home</a> | 
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf <button type="submit">Logout</button>
        </form>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Salário</th>
                <th>Estado</th>
                <th>Data Criação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->title }}</td>
                    <td>{{ $job->min_wage }}€ - {{ $job->max_wage }}€</td>
                    <td class="status-{{ $job->status }}">{{ $job->status }}</td>
                    <td>{{ $job->creation_date->format('d/m/Y') }}</td>
                    <td>
                        <button>Editar</button>
                        <button style="color:red;">Apagar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>