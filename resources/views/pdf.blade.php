<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Excursão de Temperatura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 18px;
            margin: 0;
        }

        header p {
            font-size: 12px;
            margin: 0;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h2 {
            font-size: 14px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <header>
        <h1>Relatório de Excursão de Temperatura</h1>
        <p>Data do Relatório: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </header>

    <div class="section">
        <h2>Detalhes Gerais</h2>
        <p><strong>Número do Protocolo:</strong> {{ $record->protocol_number }}</p>
        <p><strong>Nome da Instituição:</strong> {{ $record->institution_name }}</p>
        <p><strong>CNPJ:</strong> {{ $record->cnpj }}</p>
        <p><strong>Excursão de Temperatura:</strong>
            {{ optional(\Carbon\Carbon::parse($record->excursion_verification_at))->format('d/m/Y H:i') }}
        </p>
        <p><strong>Última Verificação:</strong>
            {{ optional(\Carbon\Carbon::parse($record->last_verification_at))->format('d/m/Y H:i') }}
        </p>
        <p><strong>Retorno ao Armazenamento:</strong>
            {{ optional(\Carbon\Carbon::parse($record->returned_to_storage_at))->format('d/m/Y H:i') }}
        </p>
        <p><strong>Tempo Estimado de Exposição:</strong> {{ $record->estimated_exposure_time }} horas</p>
    </div>

    <div class="section">
        <h2>Dados de Exposição</h2>
        <p><strong>Temperatura Máxima Exposta:</strong> {{ $record->max_exposed_temperature }}°C</p>
        <p><strong>Temperatura Mínima Exposta:</strong> {{ $record->min_exposed_temperature }}°C</p>
    </div>

    <div class="section">
        <h2>Medicamentos</h2>
        @if ($record->medicament && count($record->medicament) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Fabricante</th>
                        <th>Lote</th>
                        <th>Data de Validade</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->medicament as $medicament)
                        <tr>
                            <td>{{ $medicament['medicament_name'] }}</td>
                            <td>{{ $medicament['medicament_manufacturer'] }}</td>
                            <td>{{ $medicament['medicament_batch'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($medicament['medicament_date'])->format('d/m/Y') }}</td>
                            <td>{{ $medicament['medicament_quantity'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nenhum medicamento registrado.</p>
        @endif
    </div>

    <footer>
        <p style="text-align: center;">Este é um documento gerado automaticamente.</p>
    </footer>
</body>

</html>
