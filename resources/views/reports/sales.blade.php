<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        p {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h1>Reporte de Ventas</h1>
    <p>Desde: {{ request('start_date') }} Hasta: {{ request('end_date') }}</p>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->customer->name }}</td>
                    <td>{{ $sale->inventory->detailedProduct->product->name }}</td>
                    <td>{{ $sale->inventory->detailedProduct->product->price }}</td>
                    <td>{{ $sale->total_quantity }}</td>
                    <td>{{ $sale->total_sale }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
