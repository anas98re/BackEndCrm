<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    public function headings(): array
    {
        return [
            'title',
            'latitude',
            'longitude',
            'rating',
            'reviews',
            'type',
            'address',
            'open_state',
            'hours',
            'phone',
        ];
    }
    /**
    * @var $property
    */
    public function map($client): array
    {
        return [
            $client->title,
            $client->gps_coordinates->latitude,
            $client->gps_coordinates->longitude,
            $client->rating ?? '',
            $client->reviews ?? '',
            $client->type,
            $client->address,
            $client->open_state ?? '',
            $client->hours ?? '',
            $client->phone ?? '',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://serpapi.com/search.json?engine=google_maps&q=%D9%85%D8%AA%D8%AC%D8%B1%20%D8%A8%D9%82%D8%A7%D9%84%D8%A9&ll=%4026.17%2C50.1971%2C15.1z&type=search&api_key=f8aa9a3da257ee1abf34331161bafd17a0828ab85e6e6b7ead26afddb4247a50&hl=ar',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'GET',
        // ));
        // $response = curl_exec($curl);
        // curl_close($curl);

        // dd(collect(json_decode($response))->local_results);
        $retrievedClients = collect();
        for ($i = 1; $i <= 6; $i++)
        {
            $clients = collect(json_decode(file_get_contents(public_path('storage/clients/response'.$i.'.json'))));
            foreach($clients['local_results'] as $client)
            {
                $retrievedClients[] = $client;
            }
        }
        return $retrievedClients;
    }
}
