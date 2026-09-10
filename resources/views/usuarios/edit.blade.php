<!-- ================= MODAL DE EDICIÓN ================= -->
<div x-show="openEdit" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">

    <div x-show="openEdit" x-transition.opacity @click="openEdit = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openEdit"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden z-50 max-h-[90vh] flex flex-col">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
            <h2 class="text-lg font-bold text-gray-800">Editar Usuario</h2>
            <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('usuarios.update', $usuario) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6 overflow-y-auto"
              x-data="{
                  nombre: '{{ old('nombre', $usuario->nombre) }}',
                  apellidoPaterno: '{{ old('apellido_paterno', $usuario->apellido_paterno) }}',
                  role: '{{ old('role') ?? ($usuario->roles->first()->name ?? '') }}',
                  avatarPreview: '{{ $usuario->avatar_url ? $usuario->avatar_url : '' }}',
                  colorRol: {
                      'Super Administrador': 'bg-rose-50 text-rose-700 border-rose-100',
                      'Administrador de Empresa': 'bg-blue-50 text-blue-700 border-blue-100',
                      'Gestor de flotilla': 'bg-orange-50 text-orange-700 border-orange-100',
                      'Cliente Individual': 'bg-cyan-50 text-cyan-700 border-cyan-100',
                      'Conductor': 'bg-violet-50 text-violet-700 border-violet-100',
                  },
                  get iniciales() {
                      let n = this.nombre.trim().charAt(0);
                      let a = this.apellidoPaterno.trim().charAt(0);
                      return (n + a).toUpperCase() || '?';
                  },
                  previewAvatar(event) {
                      const file = event.target.files[0];
                      if (file) { this.avatarPreview = URL.createObjectURL(file); }
                  }
              }">
            @csrf
            @method('PUT')

            <!-- Identificador de error para este modal específico -->
            <input type="hidden" name="is_edit" value="{{ $usuario->id }}">

            <!-- ================= VISTA PREVIA + FOTO DE PERFIL ================= -->
            <div class="flex items-center gap-4 bg-gray-50 border border-gray-100 rounded-xl p-3">
                <div class="relative shrink-0">
                    <div class="w-14 h-14 rounded-full overflow-hidden flex items-center justify-center text-white text-base font-bold transition-colors"
                         :class="!avatarPreview && (role && colorRol[role] ? colorRol[role].split(' ')[0].replace('50','500') : 'bg-gray-400')">
                        <img x-show="avatarPreview" :src="avatarPreview" style="display:none;" class="w-full h-full object-cover" alt="Vista previa">
                        <span x-show="!avatarPreview" x-text="iniciales"></span>
                    </div>
                    <label for="avatar-input-edit-{{ $usuario->id }}" class="absolute -bottom-1 -right-1 w-6 h-6 bg-gray-900 text-white rounded-full flex items-center justify-center cursor-pointer border-2 border-white hover:bg-gray-700 transition-colors" title="Cambiar foto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                        </svg>
                    </label>
                    <input id="avatar-input-edit-{{ $usuario->id }}" type="file" name="avatar" accept="image/png, image/jpeg, image/webp" class="hidden" @change="previewAvatar($event)">
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 truncate" x-text="(nombre || 'Nombre') + ' ' + (apellidoPaterno || 'Apellido')"></p>
                    <span x-show="role" x-transition.opacity class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium border mt-0.5"
                          :class="colorRol[role] ?? 'bg-gray-50 text-gray-500 border-gray-200'" x-text="role"></span>
                    <span x-show="!role" class="text-[11px] text-gray-400 block">JPG, PNG o WEBP · máx. 2MB</span>
                    @if($errors->has('avatar') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('avatar') }}</span> @endif
                </div>
            </div>

            <!-- ================= DATOS DEL USUARIO ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- SECCIÓN MULTI-TENANT (EMPRESA) -->
                @role('Super Administrador')
                    <div class="md:col-span-2 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Empresa / Cliente Asociado</label>
                        <select name="empresa_id" class="bg-white border {{ $errors->has('empresa_id') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            <option value="">-- Interno (Personal de SOTyTECH) --</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ old('empresa_id', $usuario->empresa_id) == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nombre }} (RFC: {{ $emp->rfc ?? 'N/D' }})
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('empresa_id') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('empresa_id') }}</span> @endif
                    </div>
                @else
                    <div class="md:col-span-2 bg-gray-50/50 p-3 rounded-xl border border-gray-200">
                        <label class="block mb-2 text-sm font-medium text-gray-500">Empresa / Cliente Asociado</label>
                        <input type="text" value="{{ $usuario->empresa->nombre ?? 'SOTyTECH (Interno)' }}" disabled class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                        <span class="text-xs text-gray-400 mt-1 block">La empresa asociada no puede ser modificada por este rol.</span>
                    </div>
                @endrole

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nombre(s)</label>
                    <input type="text" name="nombre" x-model="nombre" required class="bg-gray-50 border {{ $errors->has('nombre') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    @if($errors->has('nombre') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('nombre') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" x-model="apellidoPaterno" required class="bg-gray-50 border {{ $errors->has('apellido_paterno') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    @if($errors->has('apellido_paterno') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('apellido_paterno') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Apellido Materno (Opcional)</label>
                    <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $usuario->apellido_materno) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="bg-gray-50 border {{ $errors->has('email') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    @if($errors->has('email') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('email') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Teléfono Móvil</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Rol del Sistema</label>
                    <select name="role" x-model="role" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        <option value="">-- Seleccionar Rol --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role') ?? ($usuario->roles->first()->name ?? '')) == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" class="bg-gray-50 border {{ $errors->has('password') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5" placeholder="Dejar en blanco para no cambiar">
                    @if($errors->has('password') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('password') }}</span> @endif
                </div>

                <div class="flex items-center pt-8">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 bg-gray-100 border-gray-300 rounded focus:ring-gray-900">
                    <label class="ml-2 text-sm font-medium text-gray-900">Usuario Activo (Permitir login)</label>
                </div>
            </div>

            <!-- ================= DOMICILIO (OPCIONAL Y CONTRAÍBLE) ================= -->
            <div x-data="{ openDireccion: {{ old('direccion.calle', $usuario->domicilio->calle ?? '') || ($errors->has('direccion.*') && old('is_edit') == $usuario->id) ? 'true' : 'false' }} }" class="pt-2 border-t border-gray-100">
                
                <button type="button" @click="openDireccion = !openDireccion" class="w-full flex items-center justify-between text-sm font-bold text-gray-700 py-3 hover:text-gray-900 transition-colors focus:outline-none">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Domicilio (Opcional)
                    </span>
                    <svg :class="{'rotate-180': openDireccion}" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="openDireccion" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-3 pb-2">
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Calle</label>
                        <input type="text" name="direccion[calle]" value="{{ old('direccion.calle', $usuario->domicilio->calle ?? '') }}"
                               class="bg-gray-50 border {{ $errors->has('direccion.calle') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        @if($errors->has('direccion.calle') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('direccion.calle') }}</span> @endif
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Número Exterior</label>
                        <input type="text" name="direccion[numero_exterior]" value="{{ old('direccion.numero_exterior', $usuario->domicilio->numero_exterior ?? '') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Número Interior</label>
                        <input type="text" name="direccion[numero_interior]" value="{{ old('direccion.numero_interior', $usuario->domicilio->numero_interior ?? '') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Colonia</label>
                        <input type="text" name="direccion[colonia]" value="{{ old('direccion.colonia', $usuario->domicilio->colonia ?? '') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Código Postal</label>
                        <input type="text" name="direccion[codigo_postal]" value="{{ old('direccion.codigo_postal', $usuario->domicilio->codigo_postal ?? '') }}"
                               class="bg-gray-50 border {{ $errors->has('direccion.codigo_postal') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        @if($errors->has('direccion.codigo_postal') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('direccion.codigo_postal') }}</span> @endif
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Ciudad</label>
                        <input type="text" name="direccion[ciudad]" value="{{ old('direccion.ciudad', $usuario->domicilio->ciudad ?? '') }}"
                               class="bg-gray-50 border {{ $errors->has('direccion.ciudad') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        @if($errors->has('direccion.ciudad') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('direccion.ciudad') }}</span> @endif
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Estado</label>
                        <input type="text" name="direccion[estado]" value="{{ old('direccion.estado', $usuario->domicilio->estado ?? '') }}"
                               class="bg-gray-50 border {{ $errors->has('direccion.estado') && old('is_edit') == $usuario->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        @if($errors->has('direccion.estado') && old('is_edit') == $usuario->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('direccion.estado') }}</span> @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Referencias</label>
                        <textarea name="direccion[referencias]" rows="2"
                                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">{{ old('direccion.referencias', $usuario->domicilio->referencias ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="openEdit = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>