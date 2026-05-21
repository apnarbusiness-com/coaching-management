<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = User::with(['roles', 'teacher', 'student'])->select(sprintf('%s.*', (new User)->table));

            if ($request->filled('role_id')) {
                $query->whereHas('roles', fn($q) => $q->where('roles.id', $request->input('role_id')));
            }

            if ($request->filled('status')) {
                if ($request->input('status') === 'active') {
                    $query->whereNull('deleted_at');
                } elseif ($request->input('status') === 'inactive') {
                    $query->whereNotNull('deleted_at');
                }
            }

            if ($request->filled('type')) {
                if ($request->input('type') === 'student') {
                    $query->whereHas('student');
                } elseif ($request->input('type') === 'teacher') {
                    $query->whereHas('teacher');
                } elseif ($request->input('type') === 'none') {
                    $query->whereDoesntHave('student')->whereDoesntHave('teacher');
                }
            }

            if ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }

            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'user_show';
                $editGate = 'user_edit';
                $deleteGate = 'user_delete';
                $crudRoutePart = 'users';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn($row) => $row->id ?: '');
            $table->editColumn('name', fn($row) => $row->name ?: '');
            $table->editColumn('email', fn($row) => $row->email ?: '');
            $table->editColumn('email_verified_at', fn($row) => $row->email_verified_at ?: '');
            $table->editColumn('roles', function ($row) {
                $labels = [];
                foreach ($row->roles as $role) {
                    $labels[] = sprintf('<span class="badge badge-info">%s</span>', e($role->title));
                }
                return implode(' ', $labels);
            });

            $table->addColumn('status', function ($row) {
                if ($row->deleted_at) {
                    return '<span class="badge badge-danger">Inactive</span>';
                }
                return '<span class="badge badge-success">Active</span>';
            });

            $table->addColumn('type_label', function ($row) {
                if ($row->teacher) {
                    return '<span class="badge badge-primary">Teacher</span>';
                }
                if ($row->student) {
                    return '<span class="badge badge-info">Student</span>';
                }
                return '<span class="badge badge-secondary">None</span>';
            });

            $table->rawColumns(['placeholder', 'actions', 'roles', 'status', 'type_label']);
            $table->setRowAttr([
                'data-entry-id' => fn($row) => $row->id,
            ]);

            return $table->make(true);
        }

        $roles = Role::pluck('title', 'id');

        return view('admin.users.index', compact('roles'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');
        $userName = generateUserName();

        return view('admin.users.create', compact('roles', 'userName'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->all();
        if (empty($data['user_name'])) {
            $data['user_name'] = generateUserName();
        }
        $user = User::create($data);
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');

        if (is_null($user->admission_id) && is_null($user->user_name)) {
            $user->user_name = generateUserName();
            $user->save();
        }

        $user->load('roles');

        return view('admin.users.edit', compact('roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load(['roles', 'teacher', 'student']);
        // return $user;

        return view('admin.users.show', compact('user'));
    }

    public function sendCredentials(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Generate a new random password
        $password = Str::random(8);

        // Update user's password
        $user->password = $password;
        $user->save();

        // Send email
        Mail::to($user->email)->send(new UserCredentialsMail($user, $password));

        return back()->with('message', 'Credentials sent to ' . $user->email . ' successfully!');
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        $users = User::find(request('ids'));

        foreach ($users as $user) {
            $user->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
