<?php

namespace App\Console\Commands;

use App\Models\Alerta;
use App\Models\EventoGeocerca;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EvaluarPermanenciaGeocercas extends Command
{
    protected $signature = 'geocercas:evaluar-permanencia';

    protected $description = 'Revisa los eventos de geocerca abiertos y genera alertas de exceso de permanencia, aunque no llegue un ping GPS nuevo.';

    public function handle(): int
    {
        // Une eventos abiertos con la regla pivote del vehículo en esa zona,
        // y calcula en el propio Postgres cuántos minutos lleva dentro.
        $vencidos = DB::table('eventos_geocerca as e')
            ->join('vehiculo_zona as vz', function ($join) {
                $join->on('vz.vehiculo_id', '=', 'e.vehiculo_id')
                     ->on('vz.zona_id', '=', 'e.zona_id');
            })
            ->whereNull('e.fecha_salida')
            ->where('e.alerta_permanencia_generada', false)
            ->whereNotNull('vz.permanencia_maxima_minutos')
            ->whereRaw('EXTRACT(EPOCH FROM (NOW() - e.fecha_entrada)) / 60 > vz.permanencia_maxima_minutos')
            ->select('e.id as evento_id', 'e.vehiculo_id', 'e.zona_id', 'vz.permanencia_maxima_minutos')
            ->get();

        if ($vencidos->isEmpty()) {
            $this->info('Sin eventos que excedan su permanencia máxima.');
            return self::SUCCESS;
        }

        foreach ($vencidos as $registro) {
            $evento = EventoGeocerca::with(['vehiculo', 'zona'])->find($registro->evento_id);
            if (!$evento) {
                continue;
            }

            $evento->update([
                'tipo_evento' => 'exceso_permanencia',
                'alerta_generada' => true,
                'alerta_permanencia_generada' => true,
            ]);

            Alerta::create([
                'vehiculo_id' => $evento->vehiculo_id,
                'tipo' => 'exceso_permanencia',
                'mensaje' => "El vehículo {$evento->vehiculo->nombre} lleva más de {$registro->permanencia_maxima_minutos} minutos en la zona: {$evento->zona->nombre}.",
            ]);

            $this->line("Alerta generada: {$evento->vehiculo->nombre} en {$evento->zona->nombre}.");
        }

        $this->info(count($vencidos) . ' alerta(s) de exceso de permanencia generada(s).');

        return self::SUCCESS;
    }
}