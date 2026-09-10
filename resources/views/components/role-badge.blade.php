@props(['rol'])
@php
    $colores = [
        'Super Administrador' => 'bg-rose-50 text-rose-700 border-rose-100',
        'Administrador de Empresa' => 'bg-blue-50 text-blue-700 border-blue-100',
        'Gestor de flotilla' => 'bg-orange-50 text-orange-700 border-orange-100',
        'Cliente Individual' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
        'Conductor' => 'bg-violet-50 text-violet-700 border-violet-100',
    ];
    $clase = $colores[$rol] ?? 'bg-gray-50 text-gray-500 border-gray-200';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border $clase"]) }}>
    {{ $rol ?? 'Sin rol' }}
</span>