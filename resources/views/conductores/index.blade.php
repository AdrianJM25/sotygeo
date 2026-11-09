<x-app-layout>

    <div class="font-['Ubuntu']">

        {{-- ====================================================== --}}
        {{-- TARJETA PRINCIPAL --}}
        {{-- ====================================================== --}}

        <div class="overflow-hidden rounded-2xl border border-[#0056b3]/20 bg-white shadow-sm shadow-black/5">

            {{-- ================================================== --}}
            {{-- CABECERA --}}
            {{-- ================================================== --}}

            <div class="flex flex-col gap-4 bg-[#0056b3] p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">

                <div>
                    <h2 class="font-['Josefin_Sans'] text-xl font-semibold tracking-tight text-white sm:text-2xl">
                        Gestión de conductores
                    </h2>

                    <p class="mt-1 text-sm text-blue-100">
                        Administra los conductores registrados y sus vehículos asignados.
                    </p>
                </div>

                <button
                    type="button"
                    data-modal-open="modalCrearConductor"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5
                           text-sm font-medium text-[#0056b3] shadow-sm shadow-black/20 transition-all
                           hover:bg-blue-50 active:scale-[0.98]
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white
                           focus-visible:ring-offset-2 focus-visible:ring-offset-[#0056b3]"
                >
                    <span class="material-symbols-rounded text-[20px]">
                        person_add
                    </span>

                    Nuevo conductor
                </button>

            </div>


            {{-- ================================================== --}}
            {{-- MENSAJE DE ÉXITO --}}
            {{-- ================================================== --}}

            @if(session('success'))

                <div class="mx-5 mt-5 flex items-center gap-2 rounded-xl border border-[#0056b3]/30
                            bg-blue-50 px-4 py-3 text-sm text-[#003d82] sm:mx-6">

                    <span class="material-symbols-rounded text-[18px] text-[#0056b3]">
                        check_circle
                    </span>

                    {{ session('success') }}

                </div>

            @endif


            {{-- ================================================== --}}
            {{-- ERRORES --}}
            {{-- ================================================== --}}

            @if($errors->any())

                <div class="mx-5 mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3
                            text-sm text-red-700 sm:mx-6">

                    <div class="mb-2 flex items-center gap-2 font-medium">
                        <span class="material-symbols-rounded text-[18px]">
                            error
                        </span>

                        Revisa los siguientes datos:
                    </div>

                    <ul class="ml-6 list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif


            {{-- ================================================== --}}
            {{-- TABLA --}}
            {{-- ================================================== --}}

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm text-gray-700">

                    <thead class="border-b border-[#0056b3]/15 bg-blue-50/60 text-xs text-[#0056b3]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">
                                Conductor
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Contacto
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Licencia
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Vehículo
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Estado
                            </th>

                            <th class="px-6 py-4 text-right font-semibold">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#0056b3]/10">

                        @forelse($conductores as $conductor)

                            <tr class="bg-white transition-colors hover:bg-blue-50/30">

                                {{-- ====================================== --}}
                                {{-- CONDUCTOR --}}
                                {{-- ====================================== --}}

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg
                                                    bg-[#0056b3]/10 text-[#0056b3]">

                                            <span class="material-symbols-rounded text-[21px]">
                                                person
                                            </span>

                                        </div>

                                        <div class="min-w-0">

                                            <div class="truncate font-medium text-[#003d82]">
                                                {{ $conductor->nombre_completo }}
                                            </div>

                                            @if($conductor->empresa)

                                                <div class="mt-0.5 truncate text-xs text-gray-500">
                                                    {{ $conductor->empresa->nombre }}
                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- ====================================== --}}
                                {{-- CONTACTO --}}
                                {{-- ====================================== --}}

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-1.5 text-gray-700">

                                        <span class="material-symbols-rounded text-[16px] text-[#0056b3]">
                                            call
                                        </span>

                                        {{ $conductor->telefono ?? 'Sin teléfono' }}

                                    </div>

                                    <div class="mt-1 flex items-center gap-1.5 text-xs text-gray-500">

                                        <span class="material-symbols-rounded text-[15px] text-[#0056b3]">
                                            mail
                                        </span>

                                        {{ $conductor->email ?? 'Sin correo' }}

                                    </div>

                                </td>


                                {{-- ====================================== --}}
                                {{-- LICENCIA --}}
                                {{-- ====================================== --}}

                                <td class="px-6 py-4">

                                    <div class="font-medium text-[#003d82]">
                                        {{ $conductor->numero_licencia ?? 'Sin licencia' }}
                                    </div>

                                    @if($conductor->vigencia_licencia)

                                        @if($conductor->vigencia_licencia->isPast())

                                            <div class="mt-1 inline-flex items-center gap-1 text-xs text-red-600">

                                                <span class="material-symbols-rounded text-[14px]">
                                                    warning
                                                </span>

                                                Venció:
                                                {{ $conductor->vigencia_licencia->format('d/m/Y') }}

                                            </div>

                                        @else

                                            <div class="mt-1 text-xs text-gray-500">
                                                Vigencia:
                                                {{ $conductor->vigencia_licencia->format('d/m/Y') }}
                                            </div>

                                        @endif

                                    @else

                                        <div class="mt-1 text-xs text-gray-400">
                                            Sin vigencia registrada
                                        </div>

                                    @endif


                                    {{-- ================================== --}}
                                    {{-- BOTONES FRENTE / REVERSO --}}
                                    {{-- ================================== --}}

                                    @if(
                                        $conductor->licencia_frente_path ||
                                        $conductor->licencia_reverso_path
                                    )

                                        <div class="mt-2 flex flex-wrap items-center gap-2">

                                            @if($conductor->licencia_frente_path)

                                                <a
                                                    href="{{ route('conductores.licencia', [$conductor, 'frente']) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    title="Ver frente de la licencia"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-blue-50
                                                           px-2 py-1 text-xs font-medium text-[#0056b3]
                                                           transition-colors hover:bg-blue-100"
                                                >
                                                    <span class="material-symbols-rounded text-[15px]">
                                                        visibility
                                                    </span>

                                                    Frente
                                                </a>

                                            @endif


                                            @if($conductor->licencia_reverso_path)

                                                <a
                                                    href="{{ route('conductores.licencia', [$conductor, 'reverso']) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    title="Ver reverso de la licencia"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-gray-100
                                                           px-2 py-1 text-xs font-medium text-gray-700
                                                           transition-colors hover:bg-gray-200"
                                                >
                                                    <span class="material-symbols-rounded text-[15px]">
                                                        visibility
                                                    </span>

                                                    Reverso
                                                </a>

                                            @endif

                                        </div>

                                    @else

                                        <div class="mt-2 text-xs text-gray-400">
                                            Sin fotografías
                                        </div>

                                    @endif

                                </td>


                                {{-- ====================================== --}}
                                {{-- VEHÍCULO --}}
                                {{-- ====================================== --}}

                                <td class="px-6 py-4">

                                    @if($conductor->vehiculo)

                                        <span class="inline-flex items-center gap-1 rounded-lg bg-[#0056b3]/10
                                                     px-2 py-1 text-xs font-medium text-[#003d82]">

                                            <span class="material-symbols-rounded text-[15px]">
                                                directions_car
                                            </span>

                                            {{ $conductor->vehiculo->nombre }}

                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            Sin vehículo asignado
                                        </span>

                                    @endif

                                </td>


                                {{-- ====================================== --}}
                                {{-- ESTADO --}}
                                {{-- ====================================== --}}

                                <td class="px-6 py-4">

                                    @if($conductor->activo)

                                        <span class="inline-flex items-center gap-1 rounded-full border
                                                     border-emerald-200 bg-emerald-50 px-2.5 py-1
                                                     text-xs font-medium text-emerald-700">

                                            <span class="material-symbols-rounded text-[15px] text-emerald-600">
                                                check_circle
                                            </span>

                                            Activo

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1 rounded-full border
                                                     border-gray-200 bg-gray-100 px-2.5 py-1
                                                     text-xs font-medium text-gray-600">

                                            <span class="material-symbols-rounded text-[15px]">
                                                person_off
                                            </span>

                                            Inactivo

                                        </span>

                                    @endif

                                </td>


                                {{-- ====================================== --}}
                                {{-- ACCIONES --}}
                                {{-- ====================================== --}}

                                <td class="whitespace-nowrap px-6 py-4 text-right">

                                    <div class="inline-flex items-center gap-1">

                                        {{-- EDITAR --}}

                                        <button
                                            type="button"
                                            data-modal-open="modalEditarConductor-{{ $conductor->id }}"
                                            title="Editar conductor"
                                            class="rounded-lg p-2 text-gray-500 transition-colors
                                                   hover:bg-[#0056b3]/10 hover:text-[#0056b3]"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">
                                                edit
                                            </span>
                                        </button>


                                        {{-- ACTIVAR / DESACTIVAR --}}

                                        <form
                                            action="{{ route('conductores.estado', $conductor) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('¿Deseas cambiar el estado de este conductor?');"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                title="{{ $conductor->activo
                                                    ? 'Desactivar conductor'
                                                    : 'Activar conductor'
                                                }}"
                                                class="rounded-lg p-2 transition-colors
                                                       {{
                                                           $conductor->activo
                                                               ? 'text-red-500 hover:bg-red-50 hover:text-red-700'
                                                               : 'text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700'
                                                       }}"
                                            >
                                                <span class="material-symbols-rounded text-[20px]">
                                                    {{
                                                        $conductor->activo
                                                            ? 'person_off'
                                                            : 'person_check'
                                                    }}
                                                </span>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="bg-white px-6 py-10 text-center text-gray-500"
                                >
                                    <span class="material-symbols-rounded mb-2 block text-[32px] text-[#0056b3]/40">
                                        person_off
                                    </span>

                                    No hay conductores registrados.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ================================================== --}}
            {{-- PAGINACIÓN --}}
            {{-- ================================================== --}}

            @if($conductores->hasPages())

                <div class="border-t border-[#0056b3]/15 bg-white p-4">
                    {{ $conductores->links() }}
                </div>

            @endif

        </div>



        {{-- ====================================================== --}}
        {{-- MODALES EDITAR --}}
        {{-- ====================================================== --}}

        @foreach($conductores as $conductor)

            <div
                id="modalEditarConductor-{{ $conductor->id }}"
                data-modal-backdrop
                class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/50 p-4"
            >

                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl">

                    {{-- ========================================== --}}
                    {{-- CABECERA --}}
                    {{-- ========================================== --}}

                    <div class="flex items-center justify-between border-b border-gray-200 p-5">

                        <div>

                            <h3 class="font-['Josefin_Sans'] text-xl font-semibold text-gray-800">
                                Editar conductor
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Modifica la información del conductor.
                            </p>

                        </div>

                        <button
                            type="button"
                            data-modal-close="modalEditarConductor-{{ $conductor->id }}"
                            class="rounded-lg p-2 text-gray-500 transition-colors
                                   hover:bg-blue-50 hover:text-[#0056b3]"
                        >
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </button>

                    </div>


                    {{-- ========================================== --}}
                    {{-- FORMULARIO EDITAR --}}
                    {{-- ========================================== --}}

                    <form
                        action="{{ route('conductores.update', $conductor) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="p-6"
                    >

                        @csrf
                        @method('PUT')

                        <input
                            type="hidden"
                            name="is_edit"
                            value="{{ $conductor->id }}"
                        >


                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            {{-- ====================================== --}}
                            {{-- EMPRESA --}}
                            {{-- ====================================== --}}

                            @if(auth()->user()->hasRole('Super Administrador'))

                                <div class="md:col-span-2">

                                    <label class="mb-1 block text-sm font-medium text-gray-700">
                                        Empresa *
                                    </label>

                                    <select
                                        name="empresa_id"
                                        required
                                        class="w-full rounded-xl border-gray-300
                                               focus:border-[#0056b3]
                                               focus:ring-[#0056b3]"
                                    >

                                        @foreach($empresas as $empresa)

                                            <option
                                                value="{{ $empresa->id }}"
                                                @selected(
                                                    (
                                                        old('is_edit') == $conductor->id
                                                            ? old('empresa_id')
                                                            : $conductor->empresa_id
                                                    ) == $empresa->id
                                                )
                                            >
                                                {{ $empresa->nombre }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                            @endif


                            {{-- ====================================== --}}
                            {{-- NOMBRE --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Nombre *
                                </label>

                                <input
                                    type="text"
                                    name="nombre"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('nombre')
                                            : $conductor->nombre
                                    }}"
                                    required
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- APELLIDO PATERNO --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Apellido paterno
                                </label>

                                <input
                                    type="text"
                                    name="apellido_paterno"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('apellido_paterno')
                                            : $conductor->apellido_paterno
                                    }}"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- APELLIDO MATERNO --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Apellido materno
                                </label>

                                <input
                                    type="text"
                                    name="apellido_materno"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('apellido_materno')
                                            : $conductor->apellido_materno
                                    }}"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- TELÉFONO --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Teléfono
                                </label>

                                <input
                                    type="text"
                                    name="telefono"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('telefono')
                                            : $conductor->telefono
                                    }}"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- CORREO --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Correo electrónico
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('email')
                                            : $conductor->email
                                    }}"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- NÚMERO DE LICENCIA --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Número de licencia
                                </label>

                                <input
                                    type="text"
                                    name="numero_licencia"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('numero_licencia')
                                            : $conductor->numero_licencia
                                    }}"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- VIGENCIA --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Vigencia de licencia
                                </label>

                                <input
                                    type="date"
                                    name="vigencia_licencia"
                                    value="{{
                                        old('is_edit') == $conductor->id
                                            ? old('vigencia_licencia')
                                            : optional($conductor->vigencia_licencia)->format('Y-m-d')
                                    }}"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                            </div>


                            {{-- ====================================== --}}
                            {{-- LICENCIA FRENTE PERSONALIZADA --}}
                            {{-- ====================================== --}}

                            <div
                                x-data="{
                                    fileName: 'Ningún archivo seleccionado'
                                }"
                            >

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Licencia - Frente
                                </label>


                                <div class="flex items-center gap-2">

                                    <label
                                        for="licencia_frente_editar_{{ $conductor->id }}"
                                        class="inline-flex shrink-0 cursor-pointer items-center gap-2
                                               rounded-xl bg-[#0056b3] px-4 py-2.5
                                               text-sm font-medium text-white shadow-sm
                                               transition-colors hover:bg-[#004494]"
                                    >

                                        <span class="material-symbols-rounded text-[19px]">
                                            upload_file
                                        </span>

                                        Seleccionar archivo

                                    </label>


                                    <div
                                        class="min-w-0 flex-1 truncate rounded-xl
                                               border border-gray-300 bg-gray-50
                                               px-3 py-2.5 text-sm text-gray-500"
                                        x-text="fileName"
                                    ></div>

                                </div>


                                <input
                                    id="licencia_frente_editar_{{ $conductor->id }}"
                                    type="file"
                                    name="licencia_frente"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="
                                        fileName =
                                            $event.target.files.length
                                                ? $event.target.files[0].name
                                                : 'Ningún archivo seleccionado'
                                    "
                                >


                                @if($conductor->licencia_frente_path)

                                    <div class="mt-2 flex flex-wrap items-center gap-3">

                                        <a
                                            href="{{ route('conductores.licencia', [$conductor, 'frente']) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 text-xs
                                                   font-medium text-[#0056b3] hover:underline"
                                        >

                                            <span class="material-symbols-rounded text-[15px]">
                                                visibility
                                            </span>

                                            Ver foto actual

                                        </a>

                                        <span class="text-xs text-gray-400">
                                            Selecciona otra imagen solo para reemplazarla.
                                        </span>

                                    </div>

                                @else

                                    <p class="mt-1.5 text-xs text-gray-400">
                                        JPG, PNG o WEBP. Máximo 5 MB.
                                    </p>

                                @endif

                            </div>


                            {{-- ====================================== --}}
                            {{-- LICENCIA REVERSO PERSONALIZADA --}}
                            {{-- ====================================== --}}

                            <div
                                x-data="{
                                    fileName: 'Ningún archivo seleccionado'
                                }"
                            >

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Licencia - Reverso
                                </label>


                                <div class="flex items-center gap-2">

                                    <label
                                        for="licencia_reverso_editar_{{ $conductor->id }}"
                                        class="inline-flex shrink-0 cursor-pointer items-center gap-2
                                               rounded-xl bg-[#0056b3] px-4 py-2.5
                                               text-sm font-medium text-white shadow-sm
                                               transition-colors hover:bg-[#004494]"
                                    >

                                        <span class="material-symbols-rounded text-[19px]">
                                            upload_file
                                        </span>

                                        Seleccionar archivo

                                    </label>


                                    <div
                                        class="min-w-0 flex-1 truncate rounded-xl
                                               border border-gray-300 bg-gray-50
                                               px-3 py-2.5 text-sm text-gray-500"
                                        x-text="fileName"
                                    ></div>

                                </div>


                                <input
                                    id="licencia_reverso_editar_{{ $conductor->id }}"
                                    type="file"
                                    name="licencia_reverso"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="
                                        fileName =
                                            $event.target.files.length
                                                ? $event.target.files[0].name
                                                : 'Ningún archivo seleccionado'
                                    "
                                >


                                @if($conductor->licencia_reverso_path)

                                    <div class="mt-2 flex flex-wrap items-center gap-3">

                                        <a
                                            href="{{ route('conductores.licencia', [$conductor, 'reverso']) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 text-xs
                                                   font-medium text-[#0056b3] hover:underline"
                                        >

                                            <span class="material-symbols-rounded text-[15px]">
                                                visibility
                                            </span>

                                            Ver foto actual

                                        </a>

                                        <span class="text-xs text-gray-400">
                                            Selecciona otra imagen solo para reemplazarla.
                                        </span>

                                    </div>

                                @else

                                    <p class="mt-1.5 text-xs text-gray-400">
                                        JPG, PNG o WEBP. Máximo 5 MB.
                                    </p>

                                @endif

                            </div>


                            {{-- ====================================== --}}
                            {{-- VEHÍCULO --}}
                            {{-- ====================================== --}}

                            <div>

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Vehículo asignado
                                </label>

                                <select
                                    name="vehiculo_id"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                                    <option value="">
                                        Sin vehículo
                                    </option>

                                    @foreach($vehiculos as $vehiculo)

                                        <option
                                            value="{{ $vehiculo->id }}"
                                            @selected(
                                                (
                                                    old('is_edit') == $conductor->id
                                                        ? old('vehiculo_id')
                                                        : $conductor->vehiculo_id
                                                ) == $vehiculo->id
                                            )
                                        >
                                            {{ $vehiculo->nombre }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- ====================================== --}}
                            {{-- ESTADO --}}
                            {{-- ====================================== --}}

                            <div class="flex items-center gap-3 pt-6">

                                <input
                                    type="hidden"
                                    name="activo"
                                    value="0"
                                >

                                <input
                                    type="checkbox"
                                    name="activo"
                                    value="1"
                                    id="activo_{{ $conductor->id }}"
                                    @checked(
                                        old('is_edit') == $conductor->id
                                            ? old('activo')
                                            : $conductor->activo
                                    )
                                    class="rounded border-gray-300 text-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                                <label
                                    for="activo_{{ $conductor->id }}"
                                    class="text-sm text-gray-700"
                                >
                                    Conductor activo
                                </label>

                            </div>

                        </div>


                        {{-- ========================================== --}}
                        {{-- BOTONES --}}
                        {{-- ========================================== --}}

                        <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-5">

                            <button
                                type="button"
                                data-modal-close="modalEditarConductor-{{ $conductor->id }}"
                                class="rounded-xl border border-gray-300 px-4 py-2.5
                                       text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#0056b3]
                                       px-4 py-2.5 text-white shadow-sm transition-colors
                                       hover:bg-[#004494]"
                            >
                                <span class="material-symbols-rounded text-[18px]">
                                    save
                                </span>

                                Guardar cambios
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        @endforeach



        {{-- ====================================================== --}}
        {{-- MODAL CREAR --}}
        {{-- ====================================================== --}}

        <div
            id="modalCrearConductor"
            data-modal-backdrop
            class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/50 p-4"
        >

            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl">

                {{-- ========================================== --}}
                {{-- CABECERA --}}
                {{-- ========================================== --}}

                <div class="flex items-center justify-between border-b border-gray-200 p-5">

                    <div>

                        <h3 class="font-['Josefin_Sans'] text-xl font-semibold text-gray-800">
                            Registrar conductor
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Ingresa la información del nuevo conductor.
                        </p>

                    </div>

                    <button
                        type="button"
                        data-modal-close="modalCrearConductor"
                        class="rounded-lg p-2 text-gray-500 transition-colors
                               hover:bg-blue-50 hover:text-[#0056b3]"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>

                </div>


                {{-- ========================================== --}}
                {{-- FORMULARIO CREAR --}}
                {{-- ========================================== --}}

                <form
                    action="{{ route('conductores.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="p-6"
                >

                    @csrf


                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        {{-- ====================================== --}}
                        {{-- EMPRESA --}}
                        {{-- ====================================== --}}

                        @if(auth()->user()->hasRole('Super Administrador'))

                            <div class="md:col-span-2">

                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Empresa *
                                </label>

                                <select
                                    name="empresa_id"
                                    required
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                                    <option value="">
                                        Selecciona una empresa
                                    </option>

                                    @foreach($empresas as $empresa)

                                        <option
                                            value="{{ $empresa->id }}"
                                            @selected(
                                                old('empresa_id') == $empresa->id
                                            )
                                        >
                                            {{ $empresa->nombre }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        @endif


                        {{-- ====================================== --}}
                        {{-- NOMBRE --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Nombre *
                            </label>

                            <input
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                required
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- APELLIDO PATERNO --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Apellido paterno
                            </label>

                            <input
                                type="text"
                                name="apellido_paterno"
                                value="{{ old('apellido_paterno') }}"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- APELLIDO MATERNO --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Apellido materno
                            </label>

                            <input
                                type="text"
                                name="apellido_materno"
                                value="{{ old('apellido_materno') }}"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- TELÉFONO --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- CORREO --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- NÚMERO DE LICENCIA --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Número de licencia
                            </label>

                            <input
                                type="text"
                                name="numero_licencia"
                                value="{{ old('numero_licencia') }}"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- VIGENCIA --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Vigencia de licencia
                            </label>

                            <input
                                type="date"
                                name="vigencia_licencia"
                                value="{{ old('vigencia_licencia') }}"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ====================================== --}}
                        {{-- LICENCIA FRENTE PERSONALIZADA --}}
                        {{-- ====================================== --}}

                        <div
                            x-data="{
                                fileName: 'Ningún archivo seleccionado'
                            }"
                        >

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Licencia - Frente
                            </label>


                            <div class="flex items-center gap-2">

                                <label
                                    for="licencia_frente_crear"
                                    class="inline-flex shrink-0 cursor-pointer items-center gap-2
                                           rounded-xl bg-[#0056b3] px-4 py-2.5
                                           text-sm font-medium text-white shadow-sm
                                           transition-colors hover:bg-[#004494]"
                                >

                                    <span class="material-symbols-rounded text-[19px]">
                                        upload_file
                                    </span>

                                    Seleccionar archivo

                                </label>


                                <div
                                    class="min-w-0 flex-1 truncate rounded-xl
                                           border border-gray-300 bg-gray-50
                                           px-3 py-2.5 text-sm text-gray-500"
                                    x-text="fileName"
                                ></div>

                            </div>


                            <input
                                id="licencia_frente_crear"
                                type="file"
                                name="licencia_frente"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="
                                    fileName =
                                        $event.target.files.length
                                            ? $event.target.files[0].name
                                            : 'Ningún archivo seleccionado'
                                "
                            >

                            <p class="mt-1.5 text-xs text-gray-400">
                                JPG, PNG o WEBP. Máximo 5 MB.
                            </p>

                        </div>


                        {{-- ====================================== --}}
                        {{-- LICENCIA REVERSO PERSONALIZADA --}}
                        {{-- ====================================== --}}

                        <div
                            x-data="{
                                fileName: 'Ningún archivo seleccionado'
                            }"
                        >

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Licencia - Reverso
                            </label>


                            <div class="flex items-center gap-2">

                                <label
                                    for="licencia_reverso_crear"
                                    class="inline-flex shrink-0 cursor-pointer items-center gap-2
                                           rounded-xl bg-[#0056b3] px-4 py-2.5
                                           text-sm font-medium text-white shadow-sm
                                           transition-colors hover:bg-[#004494]"
                                >

                                    <span class="material-symbols-rounded text-[19px]">
                                        upload_file
                                    </span>

                                    Seleccionar archivo

                                </label>


                                <div
                                    class="min-w-0 flex-1 truncate rounded-xl
                                           border border-gray-300 bg-gray-50
                                           px-3 py-2.5 text-sm text-gray-500"
                                    x-text="fileName"
                                ></div>

                            </div>


                            <input
                                id="licencia_reverso_crear"
                                type="file"
                                name="licencia_reverso"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="
                                    fileName =
                                        $event.target.files.length
                                            ? $event.target.files[0].name
                                            : 'Ningún archivo seleccionado'
                                "
                            >

                            <p class="mt-1.5 text-xs text-gray-400">
                                JPG, PNG o WEBP. Máximo 5 MB.
                            </p>

                        </div>


                        {{-- ====================================== --}}
                        {{-- VEHÍCULO --}}
                        {{-- ====================================== --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Vehículo asignado
                            </label>

                            <select
                                name="vehiculo_id"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                                <option value="">
                                    Sin vehículo
                                </option>

                                @foreach($vehiculos as $vehiculo)

                                    <option
                                        value="{{ $vehiculo->id }}"
                                        @selected(
                                            old('vehiculo_id') == $vehiculo->id
                                        )
                                    >
                                        {{ $vehiculo->nombre }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- ========================================== --}}
                    {{-- BOTONES --}}
                    {{-- ========================================== --}}

                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-5">

                        <button
                            type="button"
                            data-modal-close="modalCrearConductor"
                            class="rounded-xl border border-gray-300 px-4 py-2.5
                                   text-gray-700 transition-colors hover:bg-gray-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#0056b3]
                                   px-4 py-2.5 text-white shadow-sm transition-colors
                                   hover:bg-[#004494]"
                        >
                            <span class="material-symbols-rounded text-[18px]">
                                save
                            </span>

                            Registrar conductor
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    {{-- ====================================================== --}}
    {{-- JAVASCRIPT DE MODALES --}}
    {{-- ====================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                |--------------------------------------------------------------------------
                | ABRIR MODAL
                |--------------------------------------------------------------------------
                */

                function abrirModal(modalId) {

                    const modal =
                        document.getElementById(modalId);

                    if (!modal) {

                        console.error(
                            'No se encontró el modal:',
                            modalId
                        );

                        return;
                    }

                    modal
                        .classList
                        .remove('hidden');

                    modal
                        .classList
                        .add('flex');

                    document
                        .body
                        .classList
                        .add('overflow-hidden');


                    const primerCampo =
                        modal.querySelector(
                            'input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]), select, textarea'
                        );

                    if (primerCampo) {

                        window.setTimeout(
                            function () {
                                primerCampo.focus();
                            },
                            50
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | CERRAR MODAL
                |--------------------------------------------------------------------------
                */

                function cerrarModal(modalId) {

                    const modal =
                        document.getElementById(modalId);

                    if (!modal) {
                        return;
                    }

                    modal
                        .classList
                        .add('hidden');

                    modal
                        .classList
                        .remove('flex');


                    const modalAbierto =
                        document.querySelector(
                            '[data-modal-backdrop]:not(.hidden)'
                        );

                    if (!modalAbierto) {

                        document
                            .body
                            .classList
                            .remove('overflow-hidden');

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | BOTONES ABRIR
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('[data-modal-open]')
                    .forEach(
                        function (boton) {

                            boton.addEventListener(
                                'click',
                                function () {

                                    const modalId =
                                        boton.getAttribute(
                                            'data-modal-open'
                                        );

                                    abrirModal(modalId);

                                }
                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | BOTONES CERRAR
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('[data-modal-close]')
                    .forEach(
                        function (boton) {

                            boton.addEventListener(
                                'click',
                                function () {

                                    const modalId =
                                        boton.getAttribute(
                                            'data-modal-close'
                                        );

                                    cerrarModal(modalId);

                                }
                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | CERRAR AL HACER CLIC FUERA
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('[data-modal-backdrop]')
                    .forEach(
                        function (modal) {

                            modal.addEventListener(
                                'click',
                                function (event) {

                                    if (
                                        event.target ===
                                        modal
                                    ) {

                                        cerrarModal(
                                            modal.id
                                        );

                                    }

                                }
                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | CERRAR CON ESC
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key !==
                            'Escape'
                        ) {
                            return;
                        }

                        const modalAbierto =
                            document.querySelector(
                                '[data-modal-backdrop]:not(.hidden)'
                            );

                        if (modalAbierto) {

                            cerrarModal(
                                modalAbierto.id
                            );

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | REABRIR MODAL SI HAY ERRORES
                |--------------------------------------------------------------------------
                */

                @if(
                    $errors->any()
                    &&
                    !old('is_edit')
                )

                    abrirModal(
                        'modalCrearConductor'
                    );

                @elseif(
                    $errors->any()
                    &&
                    old('is_edit')
                )

                    abrirModal(
                        'modalEditarConductor-{{ old('is_edit') }}'
                    );

                @endif

            }
        );

    </script>

</x-app-layout>