<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $userActual = auth()->user();

        // Filtrar usuarios: Si no es Super Admin, solo ve los de su propia empresa
        $query = User::with('roles', 'domicilio', 'empresa')->latest();
        
        if (!$userActual->hasRole('Super Administrador')) {
            $query->where('empresa_id', $userActual->empresa_id);
            // Evitar que un admin de empresa asigne el rol de Super Administrador
            $roles = Role::where('name', '!=', 'Super Administrador')->get();
            $empresas = collect([$userActual->empresa]); // Solo se envía su propia empresa
        } else {
            $roles = Role::all();
            $empresas = Empresa::orderBy('nombre')->get();
        }

        $users = $query->paginate(10);

        return view('usuarios.index', compact('users', 'roles', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'empresa_id' => 'nullable|exists:empresas,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validación de imagen

            'direccion.calle' => 'nullable|required_with:direccion.codigo_postal|string|max:255',
            'direccion.numero_exterior' => 'nullable|string|max:20',
            'direccion.numero_interior' => 'nullable|string|max:20',
            'direccion.colonia' => 'nullable|string|max:255',
            'direccion.ciudad' => 'nullable|required_with:direccion.calle|string|max:255',
            'direccion.estado' => 'nullable|required_with:direccion.calle|string|max:255',
            'direccion.codigo_postal' => 'nullable|required_with:direccion.calle|string|max:10',
            'direccion.referencias' => 'nullable|string',
        ]);

        $user = DB::transaction(function () use ($request, $validated) {
            
            // Lógica Multi-tenant de inyección
            $empresa_id = auth()->user()->hasRole('Super Administrador') 
                          ? ($validated['empresa_id'] ?? null) 
                          : auth()->user()->empresa_id;

            // Manejo de la subida del avatar
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'empresa_id' => $empresa_id,
                'nombre' => $validated['nombre'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? null,
                'avatar' => $avatarPath, // Guardar la ruta en la base de datos
                'password' => Hash::make($validated['password']),
                'activo' => $request->has('activo'),
            ]);

            $user->assignRole($validated['role']);

            if (!empty($validated['direccion']['calle'] ?? null)) {
                $user->domicilio()->create([
                    'tipo' => 'domicilio',
                    'calle' => $validated['direccion']['calle'],
                    'numero_exterior' => $validated['direccion']['numero_exterior'] ?? null,
                    'numero_interior' => $validated['direccion']['numero_interior'] ?? null,
                    'colonia' => $validated['direccion']['colonia'] ?? null,
                    'ciudad' => $validated['direccion']['ciudad'],
                    'estado' => $validated['direccion']['estado'],
                    'codigo_postal' => $validated['direccion']['codigo_postal'],
                    'referencias' => $validated['direccion']['referencias'] ?? null,
                    'es_principal' => true,
                ]);
            }

            return $user;
        });

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'empresa_id' => 'nullable|exists:empresas,id',
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validación de imagen

            'direccion.calle' => 'nullable|required_with:direccion.codigo_postal|string|max:255',
            'direccion.numero_exterior' => 'nullable|string|max:20',
            'direccion.numero_interior' => 'nullable|string|max:20',
            'direccion.colonia' => 'nullable|string|max:255',
            'direccion.ciudad' => 'nullable|required_with:direccion.calle|string|max:255',
            'direccion.estado' => 'nullable|required_with:direccion.calle|string|max:255',
            'direccion.codigo_postal' => 'nullable|required_with:direccion.calle|string|max:10',
            'direccion.referencias' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $usuario, $validated) {
            
            // Protección de edición multi-tenant
            $empresa_id = auth()->user()->hasRole('Super Administrador') 
                          ? ($validated['empresa_id'] ?? null) 
                          : $usuario->empresa_id;

            $dataToUpdate = [
                'empresa_id' => $empresa_id,
                'nombre' => $validated['nombre'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? null,
                'activo' => $request->has('activo'),
            ];

            // Manejo de la actualización del avatar
            if ($request->hasFile('avatar')) {
                // Si el usuario ya tenía un avatar, lo eliminamos del storage
                if ($usuario->avatar) {
                    Storage::disk('public')->delete($usuario->avatar);
                }
                // Guardamos el nuevo avatar y actualizamos el array de datos
                $dataToUpdate['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $usuario->update($dataToUpdate);

            if ($request->filled('password')) {
                $usuario->update(['password' => Hash::make($validated['password'])]);
            }

            $usuario->syncRoles([$validated['role']]);

            if (!empty($validated['direccion']['calle'] ?? null)) {
                $usuario->domicilio()->updateOrCreate(
                    ['direccionable_id' => $usuario->id, 'direccionable_type' => User::class, 'tipo' => 'domicilio'],
                    [
                        'calle' => $validated['direccion']['calle'],
                        'numero_exterior' => $validated['direccion']['numero_exterior'] ?? null,
                        'numero_interior' => $validated['direccion']['numero_interior'] ?? null,
                        'colonia' => $validated['direccion']['colonia'] ?? null,
                        'ciudad' => $validated['direccion']['ciudad'],
                        'estado' => $validated['direccion']['estado'],
                        'codigo_postal' => $validated['direccion']['codigo_postal'],
                        'referencias' => $validated['direccion']['referencias'] ?? null,
                        'es_principal' => true,
                    ]
                );
            }
        });

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->update(['activo' => !$usuario->activo]);
        $mensaje = $usuario->activo ? 'Usuario reactivado correctamente.' : 'Usuario desactivado correctamente.';
        return redirect()->route('usuarios.index')->with('success', $mensaje);
    }

    public function eliminar(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($usuario->flotillas()->exists()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede eliminar: el usuario tiene flotillas asignadas. Reasigna o elimina esas flotillas primero.');
        }

        DB::transaction(function () use ($usuario) {
            // Eliminar el avatar físico del storage si existe
            if ($usuario->avatar) {
                Storage::disk('public')->delete($usuario->avatar);
            }

            $usuario->domicilio()->delete();
            $usuario->delete();
        });

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado permanentemente.');
    }
}