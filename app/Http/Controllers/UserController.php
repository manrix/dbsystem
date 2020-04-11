<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Http\Requests\SaveUser;
use DBSystem\Traits\HasDataTable;
use DBSystem\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HasDataTable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->getDataTable(User::query(), $request, [
            'id','name','email','updated_at'
        ]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveUser $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function store(SaveUser $request, User $user)
    {
        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $user->save();

        $message = "User successfully saved";

        return response()->json(compact('user', 'message'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SaveUser $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(SaveUser $request, User $user)
    {
        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $user->save();

        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user
        ]);
    }

    /**
     * Get user data for user profile
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function profile($user)
    {
        $user = User::withCount([
            'backups', 'filesBackups', 'databaseBackups', 'tasks', 'activeTasks'
        ])->findOrFail($user);
        $user->space_used = $user->backups()->local()->sum('size');

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return response()->json([
                'message' => "You can't removed the user with which you're logged in"
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User successfully removed'
        ]);
    }

    /**
     * Delete multiple backups
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
        ]);

        $ids = collect($data['items'])->pluck('id');

        if ($ids->contains(auth()->user()->id)) {
            return response()->json([
                'message' => "You can't removed the user with which you're logged in"
            ], 422);
        }

        $ids->each(function ($id) {
            User::destroy($id);
        });

        return response()->json([
            'message' => "Users successfully deleted"
        ]);
    }
}
