<?php

namespace App\Services\Paket;

use App\Models\PaketUmrah;
use App\Models\PaketMaster;
use App\Models\Keberangkatan;
use Illuminate\Support\Facades\DB;

class PaketUmrahUpdateService
{
    public function update(PaketUmrah $paket, array $data): PaketUmrah
    {
        return DB::transaction(function () use ($paket, $data) {

            /**
             * 1️⃣ UPDATE PAKET UMRAH (CONTENT)
             */
            $paket->update($data);

            /**
             * 2️⃣ UPDATE PAKET MASTER
             */
            $paketMaster = PaketMaster::where('nama_paket', $paket->title)->first();

            if ($paketMaster) {
                $paketMaster->update([
                    'nama_paket'    => $data['title'],
                    'pesawat'       => $data['pesawat'],
                    'hotel_mekkah'  => $data['hotmekkah'],
                    'hotel_madinah' => $data['hotmadinah'],
                    'harga_quad'    => $data['quad'],
                    'harga_triple'  => $data['triple'],
                    'harga_double'  => $data['double'],
                ]);
            }

            /**
             * 3️⃣ UPDATE KEBERANGKATAN
             */
            Keberangkatan::where('id_paket_master', $paketMaster->id ?? null)
                ->where('status', 'Aktif')
                ->update([
                    'tanggal_berangkat' => $data['tglberangkat'],
                    'tanggal_pulang'    => now()
                        ->parse($data['tglberangkat'])
                        ->addDays($data['durasi']),
                    'kuota'             => $data['seat'],
                ]);

            return $paket;
        });
    }
}
