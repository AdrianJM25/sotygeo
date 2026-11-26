<?php
namespace App\Http\Controllers;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    public function index()
    {
        $userActual = auth()->user();
        $query = User::with([
            'roles',
            'domicilio',
            'empresa',
        ])->latest();
        if (!$userActual->hasRole('Super Administrador')) {
            $query->where(
                'empresa_id',
                $userActual->empresa_id
            );
            $roles = Role::query()
                ->where(
                    'name',
                    '!=',
                    'Super Administrador'
                )
                ->orderBy('name')
                ->get();
            $empresas = collect([
                $userActual->empresa,
            ])->filter();
        } else {
            $roles = Role::query()
                ->orderBy('name')
                ->get();
            $empresas = Empresa::query()
                ->orderBy('nombre')
                ->get();
        }
        $users = $query->paginate(10);
        return view(
            'usuarios.index',
            compact(
                'users',
                'roles',
                'empresas'
            )
        );
    }
    public function store(Request $request)
    {
        $userActual = auth()->user();
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'apellido_paterno' => [
                'required',
                'string',
                'max:255',
            ],
            'apellido_materno' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'role' => [
                'required',
                Rule::in(
                    $this->rolesPermitidos()
                ),
            ],
            'empresa_id' => $userActual->hasRole('Super Administrador')
                ? [
                    'nullable',
                    'exists:empresas,id',
                ]
                : [
                    'nullable',
                ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'avatar_recortado' => [
                'nullable',
                'string',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
            'direccion.calle' => [
                'nullable',
                'required_with:direccion.codigo_postal',
                'string',
                'max:255',
            ],
            'direccion.numero_exterior' => [
                'nullable',
                'string',
                'max:20',
            ],
            'direccion.numero_interior' => [
                'nullable',
                'string',
                'max:20',
            ],
            'direccion.colonia' => [
                'nullable',
                'string',
                'max:255',
            ],
            'direccion.ciudad' => [
                'nullable',
                'required_with:direccion.calle',
                'string',
                'max:255',
            ],
            'direccion.estado' => [
                'nullable',
                'required_with:direccion.calle',
                'string',
                'max:255',
            ],
            'direccion.codigo_postal' => [
                'nullable',
                'required_with:direccion.calle',
                'string',
                'max:10',
            ],
            'direccion.referencias' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no puede tener más de 255 caracteres.',
            'apellido_materno.max' => 'El apellido materno no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado en el sistema.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'role.required' => 'Selecciona un rol para el usuario.',
            'role.in' => 'El rol seleccionado no es válido.',
            'empresa_id.exists' => 'La empresa seleccionada no es válida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'avatar.image' => 'El archivo seleccionado debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'avatar.max' => 'La imagen no puede pesar más de 2 MB.',
            'direccion.calle.required_with' => 'Ingresa la calle del domicilio.',
            'direccion.ciudad.required_with' => 'Ingresa la ciudad del domicilio.',
            'direccion.estado.required_with' => 'Ingresa el estado del domicilio.',
            'direccion.codigo_postal.required_with' => 'Ingresa el código postal del domicilio.',
        ]);
        $user = DB::transaction(function () use (
            $request,
            $validated,
            $userActual
        ) {
            $empresaId =
                $userActual->hasRole(
                    'Super Administrador'
                )
                    ? ($validated['empresa_id'] ?? null)
                    : $userActual->empresa_id;
            if (
                $userActual->hasRole('Super Administrador')
                && $validated['role'] === 'Super Administrador'
            ) {
                $empresaId = null;
            }
            $avatarPath = null;
            if ($request->filled('avatar_recortado')) {
                $avatarPath =
                    $this->guardarAvatarRecortado(
                        $request->input(
                            'avatar_recortado'
                        )
                    );
            } elseif ($request->hasFile('avatar')) {
                $avatarPath = $request
                    ->file('avatar')
                    ->store(
                        'avatars',
                        'public'
                    );
            }
            $user = User::create([
                'empresa_id' => $empresaId,
                'nombre' => $validated['nombre'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? null,
                'avatar' => $avatarPath,
                // Contraseña interna temporal. El usuario creará la suya
                // mediante el enlace enviado a su correo electrónico.
                'password' => Hash::make(
                    Str::random(40)
                ),
                'activo' => $request->boolean(
                    'activo'
                ),
            ]);
            $user->assignRole(
                $validated['role']
            );
            $this->guardarDomicilio(
                $user,
                $validated
            );
            return $user;
        });
        $correoEnviado =
            $this->enviarAccesoInicial(
                $user
            );
        return redirect()
            ->route('usuarios.index')
            ->with(
                $correoEnviado
                    ? 'success'
                    : 'error',
                $correoEnviado
                    ? 'Usuario creado correctamente. Se envió un correo para que establezca su contraseña.'
                    : 'Usuario creado correctamente, pero no fue posible enviar el correo de acceso. Puedes reenviarlo desde el listado de usuarios.'
            );
    }
    public function update(
        Request $request,
        User $usuario
    ) {
        $this->asegurarAccesoUsuario(
            $usuario
        );
        $userActual = auth()->user();
        $validated = $request->validate([
            'is_edit' => [
                'nullable',
                'integer',
            ],
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'apellido_paterno' => [
                'required',
                'string',
                'max:255',
            ],
            'apellido_materno' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )->ignore($usuario->id),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'role' => [
                'required',
                Rule::in(
                    $this->rolesPermitidos()
                ),
            ],
            'empresa_id' => $userActual->hasRole('Super Administrador')
                ? [
                    'nullable',
                    'exists:empresas,id',
                ]
                : [
                    'nullable',
                ],
            'password' => [
                'nullable',
                'string',
                'min:8',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'avatar_recortado' => [
                'nullable',
                'string',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
            'direccion.calle' => [
                'nullable',
                'required_with:direccion.codigo_postal',
                'string',
                'max:255',
            ],
            'direccion.numero_exterior' => [
                'nullable',
                'string',
                'max:20',
            ],
            'direccion.numero_interior' => [
                'nullable',
                'string',
                'max:20',
            ],
            'direccion.colonia' => [
                'nullable',
                'string',
                'max:255',
            ],
            'direccion.ciudad' => [
                'nullable',
                'required_with:direccion.calle',
                'string',
                'max:255',
            ],
            'direccion.estado' => [
                'nullable',
                'required_with:direccion.calle',
                'string',
                'max:255',
            ],
            'direccion.codigo_postal' => [
                'nullable',
                'required_with:direccion.calle',
                'string',
                'max:10',
            ],
            'direccion.referencias' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no puede tener más de 255 caracteres.',
            'apellido_materno.max' => 'El apellido materno no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado en el sistema.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'role.required' => 'Selecciona un rol para el usuario.',
            'role.in' => 'El rol seleccionado no es válido.',
            'empresa_id.exists' => 'La empresa seleccionada no es válida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'avatar.image' => 'El archivo seleccionado debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'avatar.max' => 'La imagen no puede pesar más de 2 MB.',
            'direccion.calle.required_with' => 'Ingresa la calle del domicilio.',
            'direccion.ciudad.required_with' => 'Ingresa la ciudad del domicilio.',
            'direccion.estado.required_with' => 'Ingresa el estado del domicilio.',
            'direccion.codigo_postal.required_with' => 'Ingresa el código postal del domicilio.',
        ]);
        DB::transaction(function () use (
            $request,
            $usuario,
            $validated,
            $userActual
        ) {
            $empresaId =
                $userActual->hasRole(
                    'Super Administrador'
                )
                    ? ($validated['empresa_id'] ?? null)
                    : $usuario->empresa_id;
            if (
                $userActual->hasRole('Super Administrador')
                && $validated['role'] === 'Super Administrador'
            ) {
                $empresaId = null;
            }
            $dataToUpdate = [
                'empresa_id' => $empresaId,
                'nombre' => $validated['nombre'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? null,
                'activo' => $request->boolean(
                    'activo'
                ),
            ];
            if ($request->filled('avatar_recortado')) {
                if ($usuario->avatar) {
                    Storage::disk('public')
                        ->delete(
                            $usuario->avatar
                        );
                }
                $dataToUpdate['avatar'] =
                    $this->guardarAvatarRecortado(
                        $request->input(
                            'avatar_recortado'
                        )
                    );
            } elseif ($request->hasFile('avatar')) {
                if ($usuario->avatar) {
                    Storage::disk('public')
                        ->delete(
                            $usuario->avatar
                        );
                }
                $dataToUpdate['avatar'] =
                    $request
                        ->file('avatar')
                        ->store(
                            'avatars',
                            'public'
                        );
            }
            if ($request->filled('password')) {
                $dataToUpdate['password'] =
                    Hash::make(
                        $validated['password']
                    );
            }
            $usuario->update(
                $dataToUpdate
            );
            $usuario->syncRoles([
                $validated['role'],
            ]);
            $this->guardarDomicilio(
                $usuario,
                $validated
            );
        });
        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Usuario actualizado correctamente.'
            );
    }
    /**
     * Activa o desactiva la cuenta.
     */
    public function destroy(User $usuario)
    {
        $this->asegurarAccesoUsuario(
            $usuario
        );
        if ($usuario->id === auth()->id()) {
            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No puedes desactivar tu propia cuenta.'
                );
        }
        $usuario->update([
            'activo' => !$usuario->activo,
        ]);
        $mensaje =
            $usuario->activo
                ? 'Usuario reactivado correctamente.'
                : 'Usuario desactivado correctamente.';
        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                $mensaje
            );
    }
    /**
     * Elimina permanentemente un usuario.
     */
    public function eliminar(User $usuario)
    {
        $this->asegurarAccesoUsuario(
            $usuario
        );
        if ($usuario->id === auth()->id()) {
            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No puedes eliminar tu propia cuenta.'
                );
        }
        if ($usuario->flotillas()->exists()) {
            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No se puede eliminar: el usuario tiene flotillas asignadas. Reasigna o elimina esas flotillas primero.'
                );
        }
        DB::transaction(function () use (
            $usuario
        ) {
            if ($usuario->avatar) {
                Storage::disk('public')
                    ->delete(
                        $usuario->avatar
                    );
            }
            $usuario->domicilio()
                ->delete();
            $usuario->syncRoles([]);
            $usuario->delete();
        });
        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Usuario eliminado permanentemente.'
            );
    }
    /**
     * Reenvía al usuario el enlace para establecer su contraseña.
     */
    public function reenviarAcceso(
        User $usuario
    ) {
        $this->asegurarAccesoUsuario(
            $usuario
        );
        $correoEnviado =
            $this->enviarAccesoInicial(
                $usuario
            );
        return redirect()
            ->route('usuarios.index')
            ->with(
                $correoEnviado
                    ? 'success'
                    : 'error',
                $correoEnviado
                    ? 'Se envió un nuevo enlace de acceso a ' . $usuario->email . '.'
                    : 'No fue posible enviar el correo de acceso. Revisa la configuración de correo e inténtalo nuevamente.'
            );
    }
    /**
     * Mostrar el avatar de un usuario administrable.
     */
    public function avatar(
        User $usuario
    ) {
        $this->asegurarAccesoUsuario(
            $usuario
        );
        if (
            !$usuario->avatar
            ||
            !Storage::disk(
                'public'
            )->exists(
                $usuario->avatar
            )
        ) {
            abort(404);
        }
        return Storage::disk(
            'public'
        )->response(
            $usuario->avatar,
            null,
            [
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' =>
                    'no-cache',
            ]
        );
    }
    /**
     * Genera un token y envía al usuario un enlace para
     * establecer su contraseña de acceso a SotyGeo.
     */
    private function enviarAccesoInicial(
        User $user
    ): bool {
        try {
            $token = Password::broker()
                ->createToken($user);
            $url = route(
                'password.reset',
                [
                    'token' => $token,
                    'email' => $user->email,
                ]
            );
            $nombre = trim(
                ($user->nombre ?? '')
                . ' '
                . ($user->apellido_paterno ?? '')
            );
            Mail::send(
                'emails.bienvenida-sotygeo',
                [
                    'nombre' => $nombre ?: 'usuario',
                    'email' => $user->email,
                    'url' => $url,
                ],
                function ($mensaje) use ($user) {
                    $mensaje
                        ->to($user->email)
                        ->subject(
                            'Bienvenido a SotyGeo - Crea tu contraseña'
                        );
                }
            );
            Log::info(
                'SotyGeo: acceso inicial enviado.',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]
            );
            return true;
        } catch (\Throwable $e) {
            Log::error(
                'SotyGeo: no fue posible enviar el acceso inicial.',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]
            );
            report($e);
            return false;
        }
    }
    /**
     * Roles que el usuario autenticado puede asignar.
     */
    private function rolesPermitidos(): array
    {
        $query = Role::query();
        if (
            !auth()
                ->user()
                ->hasRole(
                    'Super Administrador'
                )
        ) {
            $query->where(
                'name',
                '!=',
                'Super Administrador'
            );
        }
        return $query
            ->pluck('name')
            ->all();
    }
    /**
     * Impide administrar usuarios de otra empresa.
     */
    private function asegurarAccesoUsuario(
        User $usuario
    ): void {
        $userActual = auth()->user();
        if (
            $userActual->hasRole(
                'Super Administrador'
            )
        ) {
            return;
        }
        if (
            (int) $usuario->empresa_id
            !==
            (int) $userActual->empresa_id
        ) {
            abort(
                403,
                'No tienes permiso para administrar este usuario.'
            );
        }
    }
    /**
     * Crea o actualiza el domicilio cuando se capturó una dirección.
     */
    private function guardarDomicilio(
        User $usuario,
        array $validated
    ): void {
        $direccion =
            $validated['direccion']
            ?? [];
        if (
            empty(
                $direccion['calle']
                ?? null
            )
        ) {
            return;
        }
        $usuario
            ->domicilio()
            ->updateOrCreate(
                [
                    'tipo' => 'domicilio',
                ],
                [
                    'calle' => $direccion['calle'],
                    'numero_exterior' => $direccion['numero_exterior'] ?? null,
                    'numero_interior' => $direccion['numero_interior'] ?? null,
                    'colonia' => $direccion['colonia'] ?? null,
                    'ciudad' => $direccion['ciudad'],
                    'estado' => $direccion['estado'],
                    'codigo_postal' => $direccion['codigo_postal'],
                    'referencias' => $direccion['referencias'] ?? null,
                    'es_principal' => true,
                ]
            );
    }
    /**
     * Guarda el JPEG generado por el recortador del navegador.
     */
    private function guardarAvatarRecortado(
        string $dataUrl
    ): string {
        if (
            !preg_match(
                '/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/',
                $dataUrl,
                $coincidencias
            )
        ) {
            abort(
                422,
                'La imagen recortada no tiene un formato válido.'
            );
        }
        $contenido = base64_decode(
            $coincidencias[2],
            true
        );
        if ($contenido === false) {
            abort(
                422,
                'No fue posible procesar la imagen recortada.'
            );
        }
        $ruta =
            'avatars/'
            .
            Str::uuid()
            .
            '.jpg';
        Storage::disk('public')
            ->put(
                $ruta,
                $contenido
            );
        return $ruta;
    }
}
