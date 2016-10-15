<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Mail\User\CreateMail;
use App\Mail\User\DeleteMail;
use App\User;
use Auth;
use Spatie\Activitylog\Models\Activity;

/**
 * @author Yugo <dedy.yugo.purwanto@gmail.com>
 *
 * @link https://github.com/arvernester/newsletter
 */
class UserController extends Controller
{
    /**
     * Show all registered users.
     *
     * @return void
     */
    public function getIndex()
    {
        $this->authorize('index', User::class);

        activity()->log('Viewied users index.');

        $users = User::orderBy('name', 'ASC')
            ->with('lists')
            ->paginate(20);

        return view('auth.user.user.index', compact('users'))
            ->withTitle('Users');
    }

    /**
     * Show user profile by logged session or provider id.
     *
     * @param int $id
     *
     * @return void
     */
    public function getProfile($id = null)
    {
        if (empty($id)) {
            $user = Auth::user();
        } else {
            $user = User::findOrFail($id);
        }

        if ($user->id !== Auth::id()) {
            activity()->performedOn($user)
                ->log('Viewed :subject.name\'s profile.');
        }

        $activities = Activity::whereCauserId($user->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        return view('auth.user.user.profile', compact('user', 'activities'))
            ->withTitle($user->name);
    }

    /**
     * Show create form.
     *
     * @return void
     */
    public function getCreate()
    {
        $this->authorize('create', User::class);

        return view('auth.user.user.create')
            ->withTitle('Create New User');
    }

    /**
     * Save registered user into database.
     *
     * @param CreateRequest $request
     *
     * @return void
     */
    public function postCreate(CreateRequest $request)
    {
        $this->authorize('create', User::class);

        \DB::transaction(function () use ($request) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = '';
            $user->remember_token = $request->_token;
            $saved = $user->save();

            // send email confirmation
            \Mail::to($user->email, $user->name)->queue(new CreateMail($user));

            if ($saved === true) {
                return redirect()
                    ->route('admin.user')
                    ->with('success', sprintf('New user %s has been created.', $user->name));
            }
        });

        return redirect()->back();
    }

    /**
     * Delete single row of users.
     *
     * @param int $id
     *
     * @return void
     */
    public function getDelete($id = null)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        \DB::transaction(function () use ($user) {
            // send email notification first
            \Mail::to($user->email, $user->name)->queue(new DeleteMail($user));

            $deleted = $user->delete();

            if ($deleted === true) {
                return redirect()
                    ->route('admin.user')
                    ->with('success', sprintf('User %s has been deleted.', $user->name));
            }
        });

        // come here for no reason
        return redirect()->back();
    }
}
