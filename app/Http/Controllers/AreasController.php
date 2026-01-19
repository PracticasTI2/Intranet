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

class AreasController extends Controller
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
    public function index()
    {
        $areas = Area::query()->search(request(['nombre']))->paginate(8);

    //     $breadcrumbs = [
    //     ['title' => 'INICIO', 'url' => route('dashboard')],
    //     ['title' => 'USUARIOS', 'url' => route('users.index')],
    //     // ['title' =>  $getnombrecliente[0]['razon_social'], 'url' =>route('clientes.show', ['cliente' => $id])],
    // ];

        return view('usuarios/areas/index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usuarios = User::get();

        return view('usuarios/areas/create', compact('usuarios'));
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
            'nombre' => 'required|unique:area,nombre',
            'encargado' => 'required',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'encargado.required' => 'El encargado es obligatorio.',
            'nombre.unique' => 'El área ya existe. Escribe un nombre diferente.',
        ]);

        $area = new Area();
        $area->nombre = $request->input('nombre');
        $area->id_encargado = $request->input('encargado');
        $area->save();

        return redirect()->route('areas-index')->with('success', 'Área creada correctamente');
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

       $areas = Area::leftJoin('user', 'user.id' , 'area.id_encargado')->where('idarea', $id)->first();
        return view('usuarios.areas.show',compact('areas'));
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
        $areas = Area::leftJoin('user', 'user.id' , 'area.id_encargado')->where('area.idarea', $id)->first();
        $userEnc = User::find($areas->id_encargado);

        return view('usuarios.areas.edit', compact('areas','usuarios','userEnc'));
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

            $area = Area::find($id);

            // Validar los datos del formulario
            $this->validate($request, [
                'nombre' => 'required',
                'encargado' => 'required',

            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'encargado.required' => 'El encargado es obligatorio.',

            ]);
                // Actualizar datos del formulario
                $area->nombre = $request->nombre;
                $area->id_encargado = $request->encargado;
                $area->save();

                return redirect()->route('areas-index')->with('success', 'Area actualizada');
            }

        catch (ValidationException $e) {
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
        $area = Area::findOrFail($id);
        $area->delete();

        return redirect()->route('areas-index')->with('success', 'Area eliminada correctamente.');
    }
}
