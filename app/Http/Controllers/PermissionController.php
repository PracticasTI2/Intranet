<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class PermissionController extends Controller
{
          /**
          * Display a listing of the resource.
          *
          * @return \Illuminate\Http\Response
          */
    // function __construct()
    // {
    //     $this->middleware('permission:evento-list|evento-create|evento-edit|evento-delete', ['only' => ['index','store']]);
    //     $this->middleware('permission:role-create', ['only' => ['create','store']]);
    //     $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
    //     $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    // }

          /**
          * Display a listing of the resource.
          *
          * @return \Illuminate\Http\Response
          */
          public function index(Request $request)
          {
              $permisos = Permission::orderBy('id','DESC')->paginate(8);
              return view('permisos.index',compact('permisos'))
                  ->with('i', ($request->input('page', 1) - 1) * 8);
          }

          /**
          * Show the form for creating a new resource.
          *
          * @return \Illuminate\Http\Response
          */
        public function create()
          {
              $roles = Role::get();
              return view('permisos.create',compact('roles'));
          }

          /**
          * Store a newly created resource in storage.
          *
          * @param  \Illuminate\Http\Request  $request
          * @return \Illuminate\Http\Response
          */
         public function store(Request $request)
            {
            // Validación del formulario
            $this->validate($request, [
                'name' => 'required|unique:permissions,name',
                // 'roles' => 'required',
            ]);

            // Obtener los roles del formulario
            $roles = $request->input('roles');
            if (!is_array($roles)) {
                $roles = [$roles]; // Convertir en array si no lo es
            }

            // Crear el nuevo permiso
            $permission = Permission::create([
                'name' => $request->input('name'),
                'guard_name' => $request->input('guard')
            ]);

            // Obtener los roles usando los IDs enviados
            $roles = Role::whereIn('id', $roles)->get();

            // Sincronizar los roles con el permiso
            $permission->syncRoles($roles);

            // Redirigir con mensaje de éxito
            return redirect()->route('permisos-index')
                            ->with('success', 'Permiso creado con éxito');
        }
          /**
          * Display the specified resource.
          *
          * @param  int  $id
          * @return \Illuminate\Http\Response
          */
    public function show($id)
    {
        $permiso = Permission::find($id);

        $rolePermissions = Role::join("role_has_permissions","role_has_permissions.role_id","=","roles.id")
                  ->where("role_has_permissions.permission_id",$id)
                  ->get();

        return view('permisos.show',compact('permiso','rolePermissions'));
    }

          /**
          * Show the form for editing the specified resource.
          *
          * @param  int  $id
          * @return \Illuminate\Http\Response
          */
        public function edit($id)
          {
              $permiso = Permission::find($id);

              $roles = Role::get();
              $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.permission_id",$id)
                  ->pluck('role_has_permissions.role_id','role_has_permissions.role_id')
                  ->all();

              return view('permisos.edit',compact('roles','permiso','rolePermissions'));
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
            // Validar el formulario
            $this->validate($request, [
                'name' => 'required',
                'roles' => 'required|array', // Verificar que sea un array
            ]);

            // Buscar el rol, si no se encuentra redirigir con error
            $permiso = Permission::find($id);
            if (!$permiso) {
                return redirect()->route('permisos-index')
                                ->with('error', 'Permiso no encontrado');
            }

            // Actualizar el nombre y el guard_name del rol
            $permiso->name = $request->input('name');
            $permiso->guard_name = 'web'; // Asignar guard_name como 'web'
            $permiso->save();

            // Convertir los IDs de los permisos en nombres de los permisos
            $roles = Role::whereIn('id', $request->input('roles'))->pluck('name');

            // Sincronizar los permisos con el rol
            $permiso->syncRoles($roles);

            // Redirigir con mensaje de éxito
            return redirect()->route('permisos-index')
                            ->with('success', 'Permisos actualizado con éxito');
        }
          /**
          * Remove the specified resource from storage.
          *
          * @param  int  $id
          * @return \Illuminate\Http\Response
          */
          public function destroy($id)
          {
              DB::table("roles")->where('id',$id)->delete();
              return redirect()->route('roles.index')
                              ->with('success','Role deleted successfully');
          }
      }
