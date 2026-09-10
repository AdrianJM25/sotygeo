@props(['activo'])

@if($activo)
    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">
        <span class="relative flex w-1.5 h-1.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
        </span>
        Activo
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-gray-100 text-gray-500 text-xs font-medium border border-gray-200">
        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
        Inactivo
    </span>
@endif