<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 列出全部分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function list(Request $request){
        try{
            $categories = Category::with(["levels", 'challenges'])->get();
            return \APIReturn::success($categories);
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 创建新分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function create(Request $request){
        $validator = \Validator::make($request->all(), [
           'categoryName' => 'required'
        ], [
            'categoryName.required' => '缺少分类名字段'
        ]);

        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $category = new Category();
            $category->category_name = $request->input('categoryName');
            $category->save();
            \Logger::info("Category: " . $category->category_name . " 被创建");
            return \APIReturn::success($category);
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 删除 分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function deleteCategory(Request $request){
        $validator = \Validator::make($request->only('categoryId'), [
            'categoryId' => 'required'
        ], [
            'categoryId.required' => '缺少 Category ID 字段'
        ]);

        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $category = Category::find($request->input('categoryId'));
            if ($category->levels->count() > 0){
                return \APIReturn::error("category_not_empty", "分类下仍有 Level");
            }
            \Logger::info("Category: " .  $category->category_name . " 被删除");
            $category->delete();
            return \APIReturn::success();
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
