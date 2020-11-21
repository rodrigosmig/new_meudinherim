<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryUpdateStoreRequest;

class CategoryController extends Controller
{
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
        if (isset($request->type)) {
            return response()->json($this->service->getCategoriesByType($request->type));
        }

        return response()->json($this->service->getAllCategories());
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

        $category = $this->service->store($data);

        return response()->json($category, Response::HTTP_CREATED);
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

        return response()->json($category);
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
        $category = $this->service->update($id, $request->validated());

        if (! $category) {
            return response()->json(['message' => __('messages.categories.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json($this->service->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = $this->service->delete($id);

        if (! $category) {
            return response()->json(['message' => __('messages.categories.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json();
    }
}
