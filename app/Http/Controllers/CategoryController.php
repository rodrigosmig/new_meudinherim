<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StoreUpdateCategoryRequest;

class CategoryController extends Controller
{
    /* The CategoryService instance.
	 *
	 * @var CategoryService
	 */
    private $service;

    public function __construct(CategoryService $service)
    {
        $this->middleware(['auth', 'verified', 'categoryOwner']);

        $this->service = $service;
        $this->title = __('global.categories');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => $this->title,
            'incoming' => $this->service->getCategoriesByType(Category::INCOME),
            'outgoing' => $this->service->getCategoriesByType(Category::EXPENSE),
        ];

        return view('categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = $this->title;

        return view('categories.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateCategoryRequest $request)
    {
        $this->service->store($request->all());

        Alert::success(__('global.success'), __('messages.categories.create'));

        return redirect()->route('categories.index');
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = $this->service->findById($id);

        return view('categories.edit', [
            'category'  => $category,
            'title'     => $this->title
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateCategoryRequest $request, $id)
    {
        if (! $this->service->update($id, $request->all())) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));

            return redirect()->route('categories.index');
        }

        Alert::success(__('global.success'), __('messages.categories.update'));

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->service->delete($id)) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_delete')]);
        }

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.categories.delete')]);
            
    }
}
