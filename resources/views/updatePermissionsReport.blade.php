

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
                                <th scope="col">التغيرات</th>
                                <th scope="col">اسم المستوى</th>
                                <th scope="col">اسم الموظف</th>
                                <th scope="col">الوقت</th>
                            </tr>
                        </thead>
                        <tbody id="myTable">
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($updatePermissionsReport as $item)
                                <tr>
                                    <th scope="row">{{ $i++ }}</th>
                                    <td>{!! nl2br(e($item->changes_data)) !!}</td>
                                    <td>{{ $item->level_name }}</td>
                                    <td>{{ $item->user->nameUser }}</td>
                                    <td>{{ $item->edit_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </body>
</html>
