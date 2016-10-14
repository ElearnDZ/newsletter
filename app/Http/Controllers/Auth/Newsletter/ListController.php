<?php

namespace App\Http\Controllers\Auth\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\CreateListRequest;
use App\Http\Requests\Newsletter\EditListRequest;
use App\NewsletterList;
use Auth;

/**
 * @author Yugo <dedy.yugo.purwanto@gmail.com>
 *
 * @link https://github.com/arvernester/newsletter
 */
class ListController extends Controller
{
    /**
     * Show all lists.
     *
     * @return void
     */
    public function getIndex()
    {
        $lists = NewsletterList::orderBy('name', 'ASC')
            ->with('subscribers')
            ->filter()
            ->paginate(20);

        if (Auth::user()->group === 'admin') {
            $lists->load('user');
        }

        if (request()->ajax()) {
            return [
                'isSuccess' => true,
                'content'   => $lists->map(function ($list) {
                    return [
                        'id'   => $list->id,
                        'name' => $list->name,
                    ];
                }),
            ];
        }

        return view('auth.newsletter.list.index', compact('lists'))
            ->withTitle('Lists');
    }

    /**
     * Create new list and save to database.
     *
     * @param CreateListRequest $request
     *
     * @return void
     */
    public function postCreate(CreateListRequest $request)
    {
        $list = new NewsletterList();
        $list->user_id = Auth::id();
        $list->slug = str_slug($request->name);
        $list->name = $request->name;
        $list->description = $request->description;
        $list->is_default = false;
        $saved = $list->save();

        if ($saved === true) {
            activity()->performedOn($list)
                ->log(sprintf('Created list %s.', $list->name));

            return redirect()
                ->route('admin.list')
                ->with('success', sprintf('New list named %s has been created.', $list->name));
        }

        return redirect()->back();
    }

    /**
     * Show form to edit existing list.
     *
     * @param int $id
     *
     * @return void
     */
    public function getEdit($id = null)
    {
        $list = NewsletterList::when(Auth::user()->group === 'user', function ($query) {
            return $query->whereUserId(Auth::id());
        })
            ->whereId($id)
            ->firstOrFail();

        return view('auth.newsletter.list.edit', compact('list'))
            ->withTitle(sprintf('Edit %s', $list->name));
    }

    /**
     * Save to database.
     *
     * @param EditListRequest $request
     *
     * @return void
     */
    public function postEdit(EditListRequest $request)
    {
        $list = NewsletterList::when(Auth::user()->group === 'user', function ($query) {
            return $query->whereUserId(Auth::id());
        })
            ->whereId($request->id)
            ->firstOrFail();

        $list->name = $request->name;
        $list->description = $request->description;

        if ($list->save() === true) {
            activity()->performedOn($list)
                ->log(sprintf('Updated lists %s.', $list->name));

            return redirect()
                ->route('admin.list')
                ->with('success', sprintf('List %s has been updated.', $list->name));
        }

        // come for no reason
        return redirect()->back();
    }

    /**
     * Delete single row of list.
     *
     * @param int $id
     *
     * @return void
     */
    public function getDelete($id = null)
    {
        $list = NewsletterList::when(Auth::user()->group === 'user', function ($query) {
            return $query->whereUserId(Auth::id());
        })
            ->whereId($id)
            ->firstOrFail();

        $listName = $list->name;

        if ($list->delete() === true) {
            activity()->log(sprintf('Deleted list %s.', $listName));

            return redirect()
                ->route('admin.list')
                ->with('success', 'List has been deleted.');
        }

        // come for no reason
        return redirect()->back();
    }
}
