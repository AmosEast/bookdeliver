<?php

namespace App\Http\Controllers\EducationManage;

use App\Models\Book;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BooksManageController extends Controller
{
    //书籍管理模块

    /**
     * 首页
     */
    public function index() {
        $pageData = [];
        $pageData['books'] = Book::with('course:id,name') ->get();
        $pageData['bookTypes'] = Book::getBookTypeMeaning();

        return view('educationManage/booksManage/index') ->with($pageData);
    }

    /**
     * 添加一本书籍视图
     */
    public function addBookView() {
        $pageData = [
            'error' =>false,
            'msg' =>[]
        ];
        $pageData['formTitle'] = '添加书籍';
        $pageData['formSubmitUrl'] = \route('booksmanage.addbook');
        $pageData['bookTypes'] = Book::getBookTypeMeaning();
        $pageData['courses'] = Course::select('id', 'name') ->where('is_valid', '=', 1) ->get();
        return view('educationManage/booksManage/addBook') ->with($pageData);

    }

    /**
     * 添加一本书籍
     */
    public function addBook(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        $message = [
            'book_isbn.required' =>'ISBN编号不能为空',
            'book_isbn.unique' =>'ISBN编号已经存在',
            'book_isbn.max' =>'ISBN编号超过最大限度',
            'book_name.required' =>'书名不能为空',
            'book_name.max' =>'书名超过最大限度',
            'book_description.max' =>'书籍描述超过最大限度',
            'book_author.required' =>'书籍作者不能为空',
            'book_author.max' =>'书籍作者超过最大限度',
            'book_publishing.required' =>'书籍出版社不能为空',
            'book_publishing.max' =>'书籍出版社超过最大限度',
            'book_price.required' =>'书籍价格不能为空',
            'book_price.numeric' =>'书籍价格必须为数字',
            'book_discount.required' =>'书籍折扣不能为空',
            'book_discount.numeric' =>'书籍折扣必须为数字',
            'book_discount.min' =>'书籍折扣不能为负数',
            'book_discount.max' =>'书籍折扣不能超过10折',
            'book_type.required' =>'书籍类型不能为空',
            'book_type.in' =>'书籍类型不支持',
            'book_valid.required' =>'书籍状态不能为空',
            'book_valid.boolean' =>'书籍状态不支持',
            'book_course.required' =>'书籍所属课程必选',
            'book_course.exists' =>'书籍所属课程不存在',
        ];
        $valicate = Validator::make($request ->all(),[
            'book_isbn' =>'bail|required|unique:books,isbn|max:15',
            'book_name' =>'bail|required|max:256',
            'book_description' =>'bail|nullable|max:512',
            'book_author' =>'bail|required|max:256',
            'book_publishing' =>'bail|required|max:128',
            'book_price' =>'bail|required|numeric',
            'book_discount' =>'bail|required|numeric|min:0|max:10',
            'book_course' =>'bail|required|exists:courses,id',
            'book_type' =>[
                'bail', 'required',
                Rule::in(array_keys(Book::getBookTypeMeaning()))
            ],
            'book_valid' =>'bail|required|boolean'
        ], $message);

        if($valicate ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $valicate ->errors() ->all();
            return response() ->json($retData);
        }

        $book = new Book;
        $book ->isbn = $request ->book_isbn;
        $book ->name = $request ->book_name;
        $book ->author = $request ->book_author;
        $book ->description = $request ->book_description;
        $book ->publishing = $request ->book_publishing;
        $book ->price = floatval($request ->book_price);
        $book ->discount = intval($request ->book_discount);
        $book ->type = intval($request ->book_type);
        $book ->course_id = intval($request ->book_course);
        $book ->is_valid = intval($request ->book_valid);
        $book ->creator_id = Auth::id();
        $book ->updater_id = Auth::id();

        if($book ->save()) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }
    }

    /**
     * 编辑一本书籍视图
     */
    public function editBookView($bookId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        $pageData['book'] = Book::find($bookId);
        $pageData['formTitle'] = '编辑书籍';
        $pageData['formSubmitUrl'] = \route('booksmanage.editbook', ['bookId' =>$bookId]);
        $pageData['bookTypes'] = Book::getBookTypeMeaning();
        $pageData['courses'] = Course::select('id', 'name') ->where('is_valid', '=', 1) ->get();
        return view('educationManage/booksManage/addBook') ->with($pageData);

    }

    /**
     * 编辑一本书
     */
    public function editBook(Request $request, $bookId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        $message = [
            'book_isbn.required' =>'ISBN编号不能为空',
            'book_isbn.unique' =>'更改后的ISBN编号已经存在',
            'book_isbn.max' =>'ISBN编号超过最大限度',
            'book_name.required' =>'书名不能为空',
            'book_name.max' =>'书名超过最大限度',
            'book_description.max' =>'书籍描述超过最大限度',
            'book_author.required' =>'书籍作者不能为空',
            'book_author.max' =>'书籍作者超过最大限度',
            'book_publishing.required' =>'书籍出版社不能为空',
            'book_publishing.max' =>'书籍出版社超过最大限度',
            'book_price.required' =>'书籍价格不能为空',
            'book_price.numeric' =>'书籍价格必须为数字',
            'book_discount.required' =>'书籍折扣不能为空',
            'book_discount.numeric' =>'书籍折扣必须为数字',
            'book_discount.min' =>'书籍折扣不能为负数',
            'book_discount.max' =>'书籍折扣不能超过10折',
            'book_type.required' =>'书籍类型不能为空',
            'book_type.in' =>'书籍类型不支持',
            'book_valid.required' =>'书籍状态不能为空',
            'book_valid.boolean' =>'书籍状态不支持',
            'book_course.required' =>'书籍所属课程必选',
            'book_course.exists' =>'书籍所属课程不存在',
        ];
        $valicate = Validator::make($request ->all(),[
            'book_isbn' =>[
                'bail', 'required', 'max:15',
                Rule::unique('books', 'isbn') ->where(function ($query) use($bookId){
                    $query ->where([
                        ['id', '<>', $bookId]
                    ]);
                })
            ],
            'book_name' =>'bail|required|max:256',
            'book_description' =>'bail|nullable|max:512',
            'book_author' =>'bail|required|max:256',
            'book_publishing' =>'bail|required|max:128',
            'book_price' =>'bail|required|numeric',
            'book_discount' =>'bail|required|numeric|min:0|max:10',
            'book_course' =>'bail|required|exists:courses,id',
            'book_type' =>[
                'bail', 'required',
                Rule::in(array_keys(Book::getBookTypeMeaning()))
            ],
            'book_valid' =>'bail|required|boolean'
        ], $message);

        if($valicate ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $valicate ->errors() ->all();
            return response() ->json($retData);
        }

        $book = Book::find($bookId);
        if(!$book){
            $retData['error'] = true;
            $retData['msg'] = ['该书籍不存在！'];
            return response() ->json($retData);
        }
        $book ->isbn = $request ->book_isbn;
        $book ->name = $request ->book_name;
        $book ->author = $request ->book_author;
        $book ->description = $request ->book_description;
        $book ->publishing = $request ->book_publishing;
        $book ->price = floatval($request ->book_price);
        $book ->discount = intval($request ->book_discount);
        $book ->type = intval($request ->book_type);
        $book ->course_id = intval($request ->book_course);
        $book ->is_valid = intval($request ->book_valid);
        $book ->creator_id = Auth::id();
        $book ->updater_id = Auth::id();

        if($book ->save()) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }

    }
}
