<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\DatoUsuario;
use App\Models\Puesto;
use App\Models\Area;


use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Adldap\Laravel\Facades\Adldap;


// use OwenIt\Auditing\Models\Audit;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use LdapRecord\Query\Builder;


use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsuariosController extends Controller
{
        //  function __construct()
        //   {
        //       $this->middleware('permission:administrador-view', ['only' => ['index','show','create','store','edit','update','destroy','historico']]);

        //       // $this->middleware('permission:video-create', ['only' => ['create','store']]);
        //       // $this->middleware('permission:video-edit', ['only' => ['edit','update']]);
        //       // $this->middleware('permission:video-delete', ['only' => ['destroy']]);
        //       // $this->middleware('permission:video-listadmin', ['only' => ['listarvideosadmin']]);

        //   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $users = User::query()->search(request(['name', 'email']))->paginate(8);


         $query = User::query()->with('datoUsuario');

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('fechan')) {
        // Asegúrate de comparar con la tabla correcta
        $fecha = $request->fechan;
        $query->whereHas('datoUsuario', function ($q) use ($fecha) {
            $q->whereDate('nacimiento', $fecha);
        });
    }

    $users = $query->paginate(10);

    return view('usuarios.index', compact('users'));
    //     $breadcrumbs = [
    //     ['title' => 'INICIO', 'url' => route('dashboard')],
    //     ['title' => 'USUARIOS', 'url' => route('users.index')],
    //     // ['title' =>  $getnombrecliente[0]['razon_social'], 'url' =>route('clientes.show', ['cliente' => $id])],
    // ];

        // return view('usuarios/index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usuarios = User::get();
        $roles = Role::get();
        $puesto = Puesto::get();
        $areas = Area::get();
        return view('usuarios/create', compact('usuarios', 'roles', 'puesto', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
{

    try {
        // Validar los datos del formulario
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'nombre' => 'required',
            'apaterno' => 'required',
            'amaterno' => 'required',
            'ingreso' => 'required|date',
            'nacimiento' => 'nullable|date',
            'correo' => 'required|email|unique:user,correo',
            'puesto' => 'required|integer',
            'area' => 'required|integer',
            'jefe' => 'nullable|integer',
            'roles' => 'array',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:6048',
        ]);


        // Buscar si el usuario ya existe por su correo electrónico
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            // Si el usuario no existe, crear uno nuevo
            $user = new User();
            $user->email = $request->input('email');

        }

        // Actualizar o establecer el nombre del usuario
        $user->name = $request->input('name');
        $user->correo = $request->input('correo');


        // Sincronizar datos con LDAP si es necesario
        $ldapUser = LdapUser::where('samaccountname', '=', $request->input('email'))->first();
        if ($ldapUser) {
            $guid = $ldapUser->getConvertedGuid();
            $user->guid = $guid;
            $user->domain = 'default';

        }

        $user->save();

        // Crear o actualizar datos del usuario relacionados
        $datoUser = $user->datoUsuario ?: new DatoUsuario();
        $datoUser->user_id = $user->id; // Asignar el mismo id que el usuario creado o actualizado


        $datoUser->nombre = $request->input('nombre');
        $datoUser->apaterno = $request->input('apaterno');
        $datoUser->amaterno = $request->input('amaterno');
        $datoUser->ingreso = $request->input('ingreso');
        $datoUser->correo = $request->input('correo');

        $datoUser->nacimiento = $request->input('nacimiento');
        $datoUser->egreso = $request->input('egreso');
        $datoUser->puesto_idpuesto = $request->input('puesto');
        $datoUser->area_idarea = $request->input('area');
        $datoUser->idjefe = $request->input('jefe');
        $datoUser->visible = $request->input('visible') === 'on' ? 1 : 0;
        $datoUser->empresa_idempresa = 2;


        // Manejar la carga de la foto si está presente

          if ($request->hasFile('foto')) {
            $archivo = $request->file('foto');
            $extension = $archivo->getClientOriginalExtension();
            $nombreArchivo = Str::slug($request->input('nombre')) . '-' . time() . '.' . $extension;
            $archivoPath = $archivo->storeAs('fotos', $nombreArchivo, 'public');
            $datoUser->foto = $archivoPath;
        }


        $user->datoUsuario()->save($datoUser);

        // Asignar roles al usuario si se proporcionan
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('usuarios-index')->with('success', 'Usuario creado o actualizado correctamente');
    } catch (ValidationException $e) {
        // Captura la excepción de validación y redirige de nuevo con los errores
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //
          $user = User::join('dato_usuario', 'dato_usuario.user_id', 'user.id')->join('puesto', 'puesto.idpuesto','dato_usuario.puesto_idpuesto')->join('empresa', 'empresa.idempresa','dato_usuario.empresa_idempresa')->find($id);
        // $vistas= VecesVista::join('usuarios', 'usuarios.id', 'veces_vistas.id_usuario')->join('videos', 'videos.id', 'veces_vistas.id_video')->where('veces_vistas.id_usuario', $id)->get();

        //  $roles = Role::get();
       $userRole = $user->roles->pluck('name')->all();


        return view('usuarios.show',compact('user','userRole'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function edit($id)
{
    $usuarios = User::get();

    $user = User::join('dato_usuario', 'dato_usuario.user_id', 'user.id')
        ->join('puesto', 'puesto.idpuesto', 'dato_usuario.puesto_idpuesto')
        ->join('area', 'area.idarea', 'dato_usuario.area_idarea')
        ->join('empresa', 'empresa.idempresa', 'dato_usuario.empresa_idempresa')
        ->where('dato_usuario.user_id', $id)
        ->select('user.*', 'dato_usuario.*', 'puesto.*', 'empresa.*')
        ->first();

    if (!$user) {
        return redirect()->route('usuarios-index')->with('error', 'Usuario no encontrado');
    }

    $roles = Role::get();
    $userRole = $user->roles->pluck('id')->all();
    $puesto = Puesto::get();
    $userPuesto = Puesto::find($user->puesto_idpuesto);
    $userJefe = User::find($user->idjefe);
    $areaUser = Area::find($user->area_idarea);
    $areas = Area::get();

    return view('usuarios.edit', compact('user', 'roles', 'userRole', 'puesto', 'userPuesto', 'usuarios', 'areaUser', 'areas', 'userJefe'));
}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function update(Request $request, $id)
{

    try {
        // Buscar el usuario en la base de datos
        $user = User::find($id);
        $datoUser = DatoUsuario::where('user_id', $id)->first();

        // Verificar si el usuario existe en la base de datos
        if (!$user || !$datoUser) {
            return redirect()->route('usuarios-index')->with('error', 'Usuario no encontrado');
        }
        // Validar los datos del formulario
        $this->validate($request, [
            'nombre' => 'required',
            'apaterno' => 'required',
            'amaterno' => 'required',
            'ingreso' => 'required|date',
            'nacimiento' => 'nullable|date',
            'egreso' => 'nullable|date',
            // 'correo' => 'required|email|unique:user,correo',
            'correo' => 'required|email' ,
            'puesto' => 'required|integer',
            'area' => 'required|integer',
            'jefe' => 'nullable|integer',
            'roles' => 'array',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:6048',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'correo.required' => 'El campo correo es obligatorio.',
            'correo.email' => 'El campo correo debe de tener este formato ejemplo@grupoasmedia.com .',
            'correo.unique' => 'Ya existe ese correo registra otro nuevo.',

            'apaterno.required' => 'El campo apellido paterno es obligatorio.',
            'amaterno.required' => 'El campo apellido materno es obligatorio.',
            'ingreso.required' => 'El campo fecha de ingreso es obligatorio.',
            'ingreso.date' => 'El campo fecha de ingreso debe ser una fecha válida.',
            'nacimiento.date' => 'El campo fecha de nacimiento debe ser una fecha válida.',
            'egreso.date' => 'El campo fecha de egreso debe ser una fecha válida.',
            'puesto.required' => 'El campo puesto es obligatorio.',
            'puesto.integer' => 'El campo puesto debe ser un número entero.',
            'area.required' => 'El campo área es obligatorio.',
            'area.integer' => 'El campo área debe ser un número entero.',
            'jefe.integer' => 'El campo jefe debe ser un número entero.',
            'roles.array' => 'El campo roles debe ser un array.',
            'foto.image' => 'El campo foto debe ser una imagen.',
            'foto.mimes' => 'El campo foto debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'foto.max' => 'El campo foto no debe superar los 6048 KB.',
        ]);

        // Sincronizar datos con LDAP
        // $ldapUser = User::where('email', $user->email)->first(); // Ajusta según tu campo de búsqueda en LDAP
        $ldapUser = LdapUser::where('samaccountname', '=', $request->input('email'))->first();

        if ($ldapUser) {

            // Obtener atributos de LDAP y actualizarlos en el usuario de la base de datos
            $user->name = $ldapUser->getAttributeValue('cn')[0] ?? $user->name;
            $user->email = $ldapUser->getAttributeValue('samaccountname')[0] ?? $user->email;
            $guid = $ldapUser->getConvertedGuid();
            $user->guid = $guid;
            $user->domain = 'default';
            $user->correo = $request->correo;

            // $datoUser->nombre = $ldapUser->getAttributeValue('givenName')[0] ?? $datoUser->nombre;
            // $datoUser->apaterno = $ldapUser->getAttributeValue('sn')[0] ?? $datoUser->apaterno;

        }

        // Actualizar datos del formulario
        $datoUser->nombre = $request->nombre;
        $datoUser->apaterno = $request->apaterno;
        $datoUser->amaterno = $request->amaterno;
        $datoUser->correo = $request->correo;
        $datoUser->ingreso = $request->ingreso;
        $datoUser->nacimiento = $request->nacimiento;
        $datoUser->egreso = $request->egreso;
        $datoUser->puesto_idpuesto = $request->puesto;
        $datoUser->area_idarea = $request->area;
        $datoUser->idjefe = $request->jefe;
       $datoUser->visible = $request->input('visible') === 'on' ? 1 : 0;

        // Manejar la carga de la foto


       if ($request->hasFile('foto')) {
    $archivo = $request->file('foto');

    // Obtener el nombre original del archivo (sin extensión)
    $nombreArchivoOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);

    // Generar el nuevo nombre del archivo con la misma extensión
    $extension = $archivo->getClientOriginalExtension();
    $nombreArchivo = Str::slug($nombreArchivoOriginal) . '.' . $extension;

    // Verificar si el usuario ya tiene una foto y eliminarla si existe
    if ($datoUser->foto && Storage::disk('public')->exists($datoUser->foto)) {
        Storage::disk('public')->delete($datoUser->foto);
    }

    // Guardar el nuevo archivo
    $archivoPath = $archivo->storeAs('fotos', $nombreArchivo, 'public');

    // Actualizar la ruta de la foto en el usuario
    $datoUser->foto = $archivoPath;
    $datoUser->save();
}


        $datoUser->save();
        $user->save();

        // Verificar si se proporcionan roles
        if ($request->has('roles')) {
            // Limpiar roles anteriores
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('usuarios-index')->with('success', 'Usuario actualizado');
    } catch (ValidationException $e) {
        // Captura la excepción de validación y redirige de nuevo con los errores
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
