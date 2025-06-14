<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Estadístico - {{ $mes }}/{{ $año }}</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Reporte de Estadísticas</h2>
    <p><strong>Mes:</strong> {{ $mes }} / <strong>Año:</strong> {{ $año }}</p>

    <table>
        <thead>
            <tr>
                <th>Indicador</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Usuarios</td><td>{{ $stats['totalUsers'] }}</td></tr>
            <tr><td>Cursos</td><td>{{ $stats['totalCourses'] }}</td></tr>
            <tr><td>Inscripciones</td><td>{{ $stats['totalInscriptions'] }}</td></tr>
            <tr><td>Certificados</td><td>{{ $stats['totalCertificates'] }}</td></tr>
            <tr><td>Tipos de Actividad</td><td>{{ $stats['totalActivityTypes'] }}</td></tr>
        </tbody>
    </table>
</body>
</html>
