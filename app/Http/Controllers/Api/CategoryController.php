<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Api\CategoryUpdateStoreRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    private $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (isset($request->form) && $request->form == 'true') {
            return response()->json($this->service->getAllCategoriesForApiForm());
        }

        $per_page = isset($request->per_page) && is_numeric(($request->per_page)) ? $request->per_page : 10;

        $filter = [
            'active' => (isset($request->active) && $request->active == 'false') ? false : true,
            'type'   => isset($request->type) ? $request->type : 'all',
        ];       

        return CategoryResource::collection($this->service->getCategories($filter, $per_page));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryUpdateStoreRequest $request)
    {
        $data = $request->validated();

        $category = $this->service->create($data);

        return (new CategoryResource($category))
                    ->response()
                    ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = $this->service->findById($id);

        if (! $category) {
            return response()->json(['message' => __('messages.categories.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateStoreRequest $request, $id)
    {
        $data = $request->validated();

        $category = $this->service->findById($id);

        if (! $category) {
            return response()->json(['message' => __('messages.categories.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $this->service->update($category, $data);

        return (new CategoryResource($category));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = $this->service->findById($id);

        if (! $category) {
            return response()->json(['message' => __('messages.categories.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->service->delete($category);
        } catch (QueryException $e) {
            return response()->json(['message' => __('messages.categories.not_delete')], Response::HTTP_BAD_REQUEST);
        } 

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
