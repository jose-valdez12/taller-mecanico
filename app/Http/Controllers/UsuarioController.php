<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // Mostrar usuarios
    public function index()
    {
        $usuarios = User::orderBy('id', 'desc')->get();

        return view('usuarios.index', compact('usuarios'));
    }

    // Formulario de registro
    public function create()
    {
        return view('usuarios.create');
    }

    // Guardar usuario
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => [
                'required',
                Rule::in([
                    'administrador',
                    'recepcion',
                    'mecanico',
                    'bodeguero'
                ])
            ],
        ]);

        $datos['password'] = Hash::make($datos['password']);

        User::create($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    // Formulario de edición
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    // Actualizar usuario
    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id)
            ],
            'rol' => [
                'required',
                Rule::in([
                    'administrador',
                    'recepcion',
                    'mecanico',
                    'bodeguero'
                ])
            ],
        ]);

        // Evitar cambiar el rol del último administrador
        if (
            $usuario->rol === 'administrador' &&
            $datos['rol'] !== 'administrador' &&
            User::where('rol', 'administrador')->count() <= 1
        ) {
            return back()->withErrors([
                'rol' => 'No se puede cambiar el rol del último administrador.'
            ])->withInput();
        }

        // Evitar que un administrador se quite su propio rol
        if (
            $usuario->id === auth()->id() &&
            $datos['rol'] !== 'administrador'
        ) {
            return back()->withErrors([
                'rol' => 'No puedes quitarte tu propio rol de administrador.'
            ])->withInput();
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function destroy(User $usuario)
    {
        // Evitar que un usuario se elimine a sí mismo
        if ($usuario->id === auth()->id()) {
            return back()->with(
                'error',
                'No puedes eliminar tu propio usuario.'
            );
        }

        // Evitar eliminar usuarios con operaciones relacionadas
        if (
            $usuario->ingresosAsignados()->exists() ||
            $usuario->solicitudesRepuestos()->exists()
        ) {
            return back()->with(
                'error',
                'No se puede eliminar este usuario porque tiene operaciones registradas.'
            );
        }

        // Evitar eliminar al último administrador
        if (
            $usuario->rol === 'administrador' &&
            User::where('rol', 'administrador')->count() <= 1
        ) {
            return back()->with(
                'error',
                'No se puede eliminar al último administrador.'
            );
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
