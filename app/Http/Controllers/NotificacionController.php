<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.23 - NOTIFICACIONES
    |--------------------------------------------------------------------------
    */


    /**
     * Obtener todas las notificaciones del usuario.
     */
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $notificaciones = $usuario
            ->notifications()
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones,
            'no_leidas' => $usuario
                ->unreadNotifications()
                ->count(),
        ]);
    }


    /**
     * Obtener notificaciones recientes para la campana.
     */
    public function recientes(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $notificaciones = $usuario
            ->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notificacion) {

                return [
                    'id' => $notificacion->id,

                    'tipo' =>
                        $notificacion->data['tipo']
                        ?? 'sistema',

                    'titulo' =>
                        $notificacion->data['titulo']
                        ?? 'Notificación',

                    'mensaje' =>
                        $notificacion->data['mensaje']
                        ?? '',

                    'url' =>
                        $notificacion->data['url']
                        ?? null,

                    'datos' =>
                        $notificacion->data['datos']
                        ?? [],

                    'leida' =>
                        !is_null(
                            $notificacion->read_at
                        ),

                    'fecha' =>
                        $notificacion
                            ->created_at
                            ?->format(
                                'd/m/Y H:i:s'
                            ),

                    'hace' =>
                        $notificacion
                            ->created_at
                            ?->diffForHumans(),
                ];
            });


        return response()->json([
            'success' => true,

            'no_leidas' =>
                $usuario
                    ->unreadNotifications()
                    ->count(),

            'notificaciones' =>
                $notificaciones,
        ]);
    }


    /**
     * Marcar una notificación como leída.
     */
    public function marcarLeida(
        Request $request,
        string $id
    ): JsonResponse {

        $notificacion =
            $request
                ->user()
                ->notifications()
                ->where(
                    'id',
                    $id
                )
                ->firstOrFail();


        if (
            is_null(
                $notificacion->read_at
            )
        ) {

            $notificacion
                ->markAsRead();
        }


        return response()->json([
            'success' => true,
            'mensaje' =>
                'Notificación marcada como leída.',
        ]);
    }


    /**
     * Marcar todas como leídas.
     */
    public function marcarTodasLeidas(
        Request $request
    ): JsonResponse {

        $request
            ->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);


        return response()->json([
            'success' => true,
            'mensaje' =>
                'Todas las notificaciones fueron marcadas como leídas.',
        ]);
    }


    /**
     * Eliminar una notificación.
     */
    public function destroy(
        Request $request,
        string $id
    ): JsonResponse {

        $notificacion =
            $request
                ->user()
                ->notifications()
                ->where(
                    'id',
                    $id
                )
                ->firstOrFail();


        $notificacion
            ->delete();


        return response()->json([
            'success' => true,
            'mensaje' =>
                'Notificación eliminada.',
        ]);
    }
}