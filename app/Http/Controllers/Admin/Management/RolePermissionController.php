<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RolePermissionController extends Controller
{


    // Roles Management
    public function rolesIndex()
    {
        return view('admin.roles.index');
    }

    public function getRolesData()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();

        return DataTables::of($roles)

            ->addColumn('users_count', function ($role) {
                return $role->users_count;
            })
            ->addColumn('permissions_count', function ($role) {
                return $role->permissions_count;
            })
            ->addColumn('created_at', function ($role) {
                return $role->created_at;
            })
            ->addColumn('actions', function ($role) {
                $actions = '<div class="d-flex gap-2">';
                $actions .= '<button class="btn btn-sm btn-primary edit-role" data-id="' . $role->id . '" data-name="' . $role->name . '">
                    <i class="material-icons-outlined">edit</i>
                </button>';

                if ($role->name !== 'admin') {
                    $actions .= '<button class="btn btn-sm btn-danger delete-role" data-id="' . $role->id . '" data-name="' . $role->name . '">
                        <i class="material-icons-outlined">delete</i>
                    </button>';
                }

                $actions .= '<a href="' . route('admin.roles.permissions', $role->id) . '" class="btn btn-sm btn-info">
                    <i class="material-icons-outlined">security</i>
                </a>';
                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            Role::create(['name' => $request->name, 'guard_name' => 'web']);
            return response()->json(['success' => true, 'message' => 'Role created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating role: ' . $e->getMessage()]);
        }
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot modify admin role']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $role->update(['name' => $request->name]);
            return response()->json(['success' => true, 'message' => 'Role updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating role: ' . $e->getMessage()]);
        }
    }

    public function destroyRole($id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->name === 'admin') {
                return response()->json(['success' => false, 'message' => 'Cannot delete admin role']);
            }

            if ($role->users()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Cannot delete role that is assigned to users']);
            }

            $role->delete();
            return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting role: ' . $e->getMessage()]);
        }
    }

    // Permissions Management
    public function permissionsIndex()
    {
        return view('admin.permissions.index');
    }

    public function getPermissionsData(Request $request)
    {
        $query = Permission::withCount('roles');

        // Calculate summary data for all permissions
        $allPermissions = Permission::withCount('roles')->get();
        $totalPermissions = $allPermissions->count();
        $categories = $allPermissions->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return ucfirst($parts[1] ?? 'general');
        })->count();
        $assignedPermissions = $allPermissions->where('roles_count', '>', 0)->count();
        $unassignedPermissions = $totalPermissions - $assignedPermissions;

        return DataTables::of($query)
            ->addColumn('category', function ($permission) {
                $parts = explode('_', $permission->name);
                return ucfirst($parts[1] ?? 'general');
            })
            ->addColumn('roles_count', function ($permission) {
                return $permission->roles_count;
            })
            ->addColumn('created_at', function ($permission) {
                return $permission->created_at;
            })
            ->with([
                'summary' => [
                    'total' => $totalPermissions,
                    'categories' => $categories,
                    'assigned' => $assignedPermissions,
                    'unassigned' => $unassignedPermissions,
                ]
            ])
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function storePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            Permission::create(['name' => $request->name, 'guard_name' => 'web']);
            return response()->json(['success' => true, 'message' => 'Permission created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating permission: ' . $e->getMessage()]);
        }
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $permission->update(['name' => $request->name]);
            return response()->json(['success' => true, 'message' => 'Permission updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating permission: ' . $e->getMessage()]);
        }
    }

    public function destroyPermission($id)
    {
        try {
            $permission = Permission::findOrFail($id);

            if ($permission->roles()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Cannot delete permission that is assigned to roles']);
            }

            $permission->delete();
            return response()->json(['success' => true, 'message' => 'Permission deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting permission: ' . $e->getMessage()]);
        }
    }

    // Role-Permission Assignment
    public function rolePermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return ucfirst($parts[1] ?? 'general');
        });

        return view('admin.roles.permissions', compact('role', 'permissions'));
    }

    public function updateRolePermissions(Request $request, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $permissions = $request->input('permissions', []);

            $role->syncPermissions($permissions);

            return response()->json(['success' => true, 'message' => 'Role permissions updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating permissions: ' . $e->getMessage()]);
        }
    }

    // User-Role Assignment
    public function userRoles()
    {
        return view('admin.users.roles');
    }

    public function getUserRolesData(Request $request)
    {
        $query = User::with('roles');

        // Apply role filter if provided
        if ($request->has('role_filter') && !empty($request->role_filter)) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role_filter);
            });
        }

        // Calculate summary data for all users
        $allUsers = User::with('roles')->get();
        $allRoles = Role::withCount('users')->get();

        $totalUsers = $allUsers->count();
        $usersWithRoles = $allUsers->filter(function ($user) {
            return $user->roles->count() > 0;
        })->count();
        $usersWithoutRoles = $totalUsers - $usersWithRoles;
        $availableRoles = $allRoles->count();

        return DataTables::of($query)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->addColumn('updated_at', function ($user) {
                return $user->updated_at;
            })
            ->addColumn('actions', function ($user) {
                return '<a href="' . route('admin.users.roles.edit', $user->id) . '" class="btn btn-sm btn-primary">
                    <i class="material-icons-outlined">edit</i> Manage Roles
                </a>';
            })
            ->with([
                'summary' => [
                    'total_users' => $totalUsers,
                    'users_with_roles' => $usersWithRoles,
                    'users_without_roles' => $usersWithoutRoles,
                    'available_roles' => $availableRoles,
                ],
                'roles' => $allRoles->map(function ($role) {
                    return [
                        'name' => $role->name,
                        'users_count' => $role->users_count,
                    ];
                })
            ])
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function editUserRoles($userId)
    {
        $user = User::with('roles')->findOrFail($userId);
        $roles = Role::all();

        return view('admin.users.edit-roles', compact('user', 'roles'));
    }

    public function updateUserRoles(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $roles = $request->input('roles', []);

            $user->syncRoles($roles);

            return response()->json(['success' => true, 'message' => 'User roles updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating user roles: ' . $e->getMessage()]);
        }
    }

    public function previewPermissions(Request $request)
    {
        try {
            $roleNames = $request->input('roles', []);
            $roles = Role::whereIn('name', $roleNames)->with('permissions')->get();

            $allPermissions = collect();
            foreach ($roles as $role) {
                $allPermissions = $allPermissions->merge($role->permissions);
            }

            $uniquePermissions = $allPermissions->unique('id')->groupBy(function ($permission) {
                $parts = explode('_', $permission->name);
                return ucfirst($parts[1] ?? 'general');
            });

            $html = '';
            if ($uniquePermissions->count() > 0) {
                foreach ($uniquePermissions as $category => $permissions) {
                    $html .= '<div class="mb-3">';
                    $html .= '<h6 class="text-primary"><i class="material-icons-outlined me-1" style="font-size: 16px;">folder</i>' . $category . ' (' . $permissions->count() . ')</h6>';
                    $html .= '<div class="ms-3">';
                    foreach ($permissions as $permission) {
                        $html .= '<span class="badge bg-light text-dark me-1 mb-1">' . ucfirst(str_replace('_', ' ', $permission->name)) . '</span>';
                    }
                    $html .= '</div></div>';
                }
            } else {
                $html = '<div class="text-center text-muted"><i class="material-icons-outlined" style="font-size: 3rem;">block</i><p>No permissions found</p></div>';
            }

            return response($html);
        } catch (\Exception $e) {
            return response('<div class="alert alert-danger">Error loading permissions: ' . $e->getMessage() . '</div>');
        }
    }

    public function rolePermissionsPreview($roleName)
    {
        try {
            $role = Role::where('name', $roleName)->with('permissions')->firstOrFail();

            $permissions = $role->permissions->groupBy(function ($permission) {
                $parts = explode('_', $permission->name);
                return ucfirst($parts[1] ?? 'general');
            });

            $html = '';
            if ($permissions->count() > 0) {
                foreach ($permissions as $category => $categoryPermissions) {
                    $html .= '<div class="mb-3">';
                    $html .= '<h6 class="text-primary"><i class="material-icons-outlined me-1" style="font-size: 16px;">folder</i>' . $category . ' (' . $categoryPermissions->count() . ')</h6>';
                    $html .= '<div class="ms-3">';
                    foreach ($categoryPermissions as $permission) {
                        $html .= '<span class="badge bg-light text-dark me-1 mb-1">' . ucfirst(str_replace('_', ' ', $permission->name)) . '</span>';
                    }
                    $html .= '</div></div>';
                }
            } else {
                $html = '<div class="text-center text-muted"><i class="material-icons-outlined" style="font-size: 3rem;">block</i><p>No permissions assigned to this role</p></div>';
            }

            return response($html);
        } catch (\Exception $e) {
            return response('<div class="alert alert-danger">Error loading role permissions: ' . $e->getMessage() . '</div>');
        }
    }
}
