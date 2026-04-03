<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->orderBy('nombre')->paginate(20);
        $roles    = Role::all();
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rol'    => ['required', 'exists:roles,id'],
            'nombre'    => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'username'  => ['required', 'string', 'max:40', 'unique:usuarios,username'],
            'email'     => ['required', 'email', 'max:100', 'unique:usuarios,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['password'] = Hash::make($data['password']);
        Usuario::create($data);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'id_rol'    => ['required', 'exists:roles,id'],
            'nombre'    => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'email'     => ['required', 'email', 'max:100', "unique:usuarios,email,{$usuario->id}"],
            'activo'    => ['boolean'],
        ]);

        $usuario->update($data);
        return back()->with('success', 'Usuario actualizado.');
    }

    public function resetPassword(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $usuario->update(['password' => Hash::make($data['password'])]);
        return back()->with('success', 'Contraseña restablecida.');
    }

    public function destroy(Usuario $usuario)
    {
        // Comparar como enteros explícitamente para que Intelephense no marque error
        $usuarioActualId = (int) auth()->id();
        $usuarioDestId   = (int) $usuario->getKey();

        if ($usuarioActualId === $usuarioDestId) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->update(['activo' => false]);
        return back()->with('success', 'Usuario desactivado.');
    }
}
