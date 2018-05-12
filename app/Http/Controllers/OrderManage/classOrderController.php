<?php

namespace App\Http\Controllers\OrderManage;

use App\Models\Academy;
use App\Models\Book;
use App\Models\Major;
use App\Models\Order;
use App\Models\SchoolClass;
use App\Models\SelectList;
use App\Models\Task;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use function Psy\debug;

class classOrderController extends Controller
{
    //班级代购模块

    /**
     * 班级代购首页
     */
    public function index(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //获取用户信息
        $user = Auth::user();
        if($user ->belong_type != User::$belong_type_academy) {
            $pageData['error'] = true;
            $pageData['msg'] = '您的直接归属信息不符合要求，不能适用班级代购功能！';
            return view('orderManage.classOrder.index') ->with($pageData);
        }

        //获取当前可用任务
        $tasks = Task::select('id', 'name')
            ->where([
                ['is_valid', '=', 1],
                ['status', '=', Task::$order_process]
            ])
            ->get();
        //获取用户所在学院
        $academy = Academy::select('id', 'name')
            ->where([
                ['is_valid', '=', 1],
                ['id', '=', $user ->belong_id]
            ])
            ->first();
        if(!$academy) {
            $pageData['error'] = true;
            $pageData['msg'] = '您所在学院信息丢失，请重新登陆！';
            return view('orderManage.classOrder.index') ->with($pageData);
        }
        //获取用户的所在学院的所有专业
        $majors = Major::with('schoolClasses')
            ->where([
                ['is_valid', '=', 1],
                ['academy_id', '=', $academy ->id]
            ])
            ->get();
        //获取各专业下班级情况
        $arrClassesInfo = [];
        foreach ($majors as $major) {
            $arrClassesInfo[$major ->id] = $major ->schoolClasses;
        }
        //组织前端数据
        $pageData['academy'] = $academy;
        $pageData['majors'] = $majors;
        $pageData['tasks'] = $tasks;
        $pageData['jsonClassesInfo'] = json_encode($arrClassesInfo);
        return view('orderManage.classOrder.index') ->with($pageData);
    }

    /**
     * 显示班级可选书籍列表
     */
    public function orderBooksView(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //参数校验
        $message = [
            'select_task.required' =>'任务选项不能为空',
            'select_task.integer' =>'任务选项类型错误',
            'select_task.exists' =>'该任务不存在或者已经管理购书阶段',
            'select_academy.required' =>'学院选项必须为空',
            'select_academy.integer' =>'学院选项类型错误',
            'select_academy.exists' =>'该学院不存在',
            'select_major.required' =>'专业选项必须为空',
            'select_major.integer' =>'专业选项类型错误',
            'select_major.exists' =>'该专业不存在',
            'select_class.required' =>'班级选项必须为空',
            'select_class.integer' =>'班级选项类型错误',
            'select_class.exists' =>'该班级不存在',
        ];
        $validator = \Validator::make($request ->all(), [
            'select_task' =>[
                'required', 'integer',
                Rule::exists('tasks', 'id') ->where(function ($query){
                    $query ->where([
                        ['is_valid', '=', 1],
                        ['status', '=', Task::$order_process]
                    ]);
                })
            ],
            'select_academy' =>'required|integer|exists:academies,id',
            'select_major' =>'required|integer|exists:majors,id',
            'select_class' =>'required|integer|exists:classes,id'
        ], $message);
        if($validator ->fails()) {
            $pageData['error'] = true;
            $pageData['msg'] = implode('<br />', $validator ->errors() ->all());
            return view('orderManage.classOrder.orderBooks') ->with($pageData);
        }
        //检查用户是否满足要求
        $user = Auth::user();
        if($user ->belong_type != User::$belong_type_academy) {
            $pageData['error'] = true;
            $pageData['msg'] = '用户学院信息不完善，无法适用学生购书功能！';
            return view('orderManage.classOrder.orderBooks') ->with($pageData);
        }
        //获取班级信息
        $schoolClass = SchoolClass::find(intval($request ->select_class));
        //获取该班所有学生
        $students = $schoolClass ->students();
        //检验该班级是否已有订单
        $arrStudentIds = array_unique($students ->pluck('id') ->toArray());
        if(Order::whereIn('user_id', $arrStudentIds) ->exists()) {
            $pageData['error'] = true;
            $pageData['msg'] = '该班级已有部分学生订单，不适用班级代购功能！';
            return view('orderManage.classOrder.orderBooks') ->with($pageData);
        }
        //获取专业信息
        $major = Major::find(intval($request ->select_major));
        //获取学院信息
        $academy = Academy::find(intval($request ->select_academy));
        //获取任务信息
        $task = Task::find(intval($request ->select_task));
        //获取符合要求的选书安排
        $arrSelectListWhere = [
            ['is_valid', '=', 1],
            ['task_id', '=', $task ->id],
            ['academy_id', '=', $academy ->id],
            ['major_id', '=', $major ->id],
            ['grade', '=', $schoolClass ->grade]
        ];
        $selectLists = SelectList::with('course')
            ->where($arrSelectListWhere)
            ->get();
        //根据选书安排获取书籍信息
        $arrBookIds = [];
        foreach ($selectLists as &$selectList) {
            $selectList ->book_ids = (empty($selectList ->book_ids) ? [] : json_decode($selectList ->book_ids, true));
            foreach ($selectList ->book_ids as $book_id) {
                $arrBookIds[$book_id] = $book_id;
            }
        }
        $books = Book::whereIn('id', $arrBookIds)
            ->where('is_valid', '=', 1)
            ->get();

        //组织前端数据
        $arrBooksInfo = [];
        $curConsume = 0.0;
        foreach ($books as $book) {
            $arrBooksInfo[$book ->id] = $book;
        }


        $pageData['task'] = $task;
        $pageData['academy'] = $academy;
        $pageData['major'] = $major;
        $pageData['schoolClass'] = $schoolClass;
        $pageData['studentsNum'] = $students ->count();
        $pageData['selectLists'] = $selectLists;
        $pageData['arrBooksInfo'] = $arrBooksInfo;

        return view('orderManage.classOrder.orderBooks') ->with($pageData);
    }

    /**
     * 提交订单
     */
    public function orderBooks(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $message = [
            'books_selected.required' =>'没有有效书籍！',
            'books_selected.array' =>'书籍选择格式错误，请刷新页面后重试！',
        ];
        $validator = \Validator::make($request ->all(), [
            'books_selected' =>'bail|required|array',
            'task_id' =>[
                'bail', 'required', 'integer',
                Rule::exists('tasks', 'id') ->where(function ($query){
                    $query ->where([
                        ['is_valid', '=', 1],
                        ['status', '=', Task::$order_process]
                    ]);
                })
            ],
            'class_id' =>[
                'bail', 'required', 'integer',
                Rule::exists('classes', 'id') ->where(function ($query){
                    $query ->where('is_valid', '=', 1);
                })
            ]
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }
        //获取该班级所有学生
        $students = SchoolClass::find(intval($request ->class_id)) ->students;
        if($students ->count() <= 0) {
            $retData['error'] = true;
            $retData['msg'] = ['该班级没有学生信息，无法适用班级代订功能！'];
            return response() ->json($retData);
        }
        $saveData = [];
        foreach ($students as $student) {
            foreach ($request ->books_selected as $bookId) {
                $saveData[] = [
                    'task_id' =>$request ->class_orders[$bookId]['task_id'],
                    'user_id' =>$student ->id,
                    'book_id' =>$bookId,
                    'quantity' =>1,
                    'created_at' =>date('Y-m-d H:i:s', time()),
                    'updated_at' =>date('Y-m-d H:i:s', time()),
                    'creator_id' =>Auth::id(),
                    'updater_id' =>Auth::id(),
                ];
            }
        }
        if(empty($saveData)) {
            $retData['error'] = true;
            $retData['msg'] = ['没有有效订单，请重新选购！'];
            return response() ->json($retData);
        }
        try{
            DB::beginTransaction();
            $order = new Order;
            $order ->insert($saveData);
            DB::commit();
            return response() ->json($retData);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            $retData['error'] = true;
            $retData['msg'] = [$exception ->getMessage()];
            return response() ->json($retData);
        }
        return response() ->json($retData);
    }
}
