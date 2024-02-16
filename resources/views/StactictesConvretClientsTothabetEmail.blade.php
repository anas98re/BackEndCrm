

<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    {{-- <input class="form-control" id="myInput" type="text" placeholder="Search.."> --}}
                    <br>
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">عدد العملاء الذين تم تحويلهم</th>
                                <th scope="col"> التاريخ والوقت</th>
                                <th scope="col">اسم الموظف الذي تم التحويل منه</th>
                                <th scope="col">اسم الموظف الذي تم التحويل اليه</th>
                            </tr>
                        </thead>
                        <tbody id="myTable">
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($ClintsStaticts as $item)
                                <tr>
                                    <th scope="row">{{ $i++ }}</th>
                                    <td>{{$item->numberOfClients}}</td>
                                    <td>{{ $item->convert_date }}</td>
                                    <td>{{ $item->oldUser != null ?  $item->oldUser->nameUser : ''}}</td>
                                    <td>{{ $item->newUser != null ?  $item->newUser->nameUser : ''}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </body>
</html>
