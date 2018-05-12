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

class StudentOrderController extends Controller
{
    //学生订书模块

    /**
     * 学生订书首页
     */
    public function index(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //缓存查询字段
        $request ->flash();
        $user = Auth::user();
        //检查用户是否满足要求
        if($user ->belong_type != User::$belong_type_class) {
            $pageData['error'] = true;
            $pageData['msg'] = '用户班级信息不完善，无法适用学生购书功能！';
            return view('orderManage.studentOrder.index') ->with($pageData);
        }
        //获取用户班级信息
        $schoolClass = SchoolClass::find(intval($user ->belong_id));
        if(!$schoolClass) {
            $pageData['error'] = true;
            $pageData['msg'] = '用户所在班级信息丢失，无法适用学生购书功能！';
            return view('orderManage.studentOrder.index') ->with($pageData);
        }
        //获取用户专业信息
        $major = Major::where([
            ['is_valid', '=', 1],
            ['id', '=', $schoolClass ->major_id]
        ])
            ->first();
        if(!$major) {
            $pageData['error'] = true;
            $pageData['msg'] = '用户所在专业信息丢失，无法适用学生购书功能！';
            return view('orderManage.studentOrder.index') ->with($pageData);
        }
        //获取用户学院信息
        $academy = Academy::where([
            ['is_valid', '=', 1],
            ['id', '=', $major ->academy_id]
        ])
            ->first();
        if(!$academy) {
            $pageData['error'] = true;
            $pageData['msg'] = '用户所在学院信息丢失，无法适用学生购书功能！';
            return view('orderManage.studentOrder.index') ->with($pageData);
        }
        //获取当前在购书状态中的任务
        $arrTaskWhere = [
            ['is_valid', '=', 1],
            ['status', '=', Task::$order_process]
        ];
        if($request ->has('query_task') && $request ->query_task) {
            $arrTaskWhere[] = ['id', '=', intval($request ->query_task)];
        }
        $tasks = Task::where($arrTaskWhere)
            ->get();
        $arrTaskIds = $tasks ->pluck('id') ->toArray();
        //获取当前任务下用户已订购的图书信息
        $orders = Order::select(\DB::raw('id, task_id, book_id, select_id, sum(quantity) as quantity'))
            ->whereIn('task_id', $arrTaskIds)
            ->where([
                ['user_id', '=', $user ->id],
                ['is_valid', '=', 1]
            ])
            ->groupBy([ 'select_id', 'book_id'])
            ->get();
        $orderedSelectIds = $orders ->pluck('select_id') ->toArray();
        //获取符合要求的选书安排
        $arrSelectListWhere = [
            ['is_valid', '=', 1],
            ['academy_id', '=', $academy ->id],
            ['major_id', '=', $major ->id],
            ['grade', '=', $schoolClass ->grade]
        ];
        $request ->query_status = ($request ->has('query_status') ? intval($request ->query_status) : 0);
        switch ($request ->query_status) {
            case -1:{
                $selectLists = SelectList::with('course')
                    ->whereNotIn('id', $orderedSelectIds)
                    ->whereIn('task_id', $arrTaskIds)
                    ->where($arrSelectListWhere)
                    ->get();
                break;
            }
            case 0:{
                $selectLists = SelectList::with('course')
                    ->whereIn('task_id', $arrTaskIds)
                    ->where($arrSelectListWhere)
                    ->get();
                break;
            }
            case 1:{
                $selectLists = SelectList::with('course')
                    ->whereIn('id', $orderedSelectIds)
                    ->where($arrSelectListWhere)
                    ->get();
                break;
            }
            default :{
                $selectLists = SelectList::with('course')
                    ->whereIn('task_id', $arrTaskIds)
                    ->where($arrSelectListWhere)
                    ->get();
            }
        }
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
        $arrTasksInfo = [];
        $arrBooksInfo = [];
        $arrOrdersInfo = [];
        $curConsume = 0.0;
        foreach ($tasks as $task) {
            $arrTasksInfo[$task ->id] = $task;
        }
        foreach ($books as $book) {
            $arrBooksInfo[$book ->id] = $book;
        }
        foreach ($orders as $order) {
            $arrOrdersInfo[$order ->select_id][] = $order;
            $curConsume += $arrBooksInfo[$order ->book_id] ->price * $arrBooksInfo[$order ->book_id] ->discount / 10 * $order ->quantity;
        }


        $pageData['academy'] = $academy;
        $pageData['major'] = $major;
        $pageData['schoolClass'] = $schoolClass;
        $pageData['selectLists'] = $selectLists;
        $pageData['arrTasksInfo'] = $arrTasksInfo;
        $pageData['arrBooksInfo'] = $arrBooksInfo;
        $pageData['arrOrdersInfo'] = $arrOrdersInfo;
        $pageData['curConsume'] = $curConsume;

        return view('orderManage.studentOrder.index') ->with($pageData);
    }

    /**
     * 提交订单
     */
    public function orderBooks(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        if(!$request ->has('stu_orders') || empty($request ->stu_orders) || !is_array($request ->stu_orders)) {
            $retData['error'] = true;
            $retData['msg'] = ['订单信息丢失，请刷新后重试！'];
            return response() ->json($retData);
        }
        $saveData = [];
        foreach ($request ->stu_orders as $bookId => $order) {
            if($order['quantity'] > 0) {
                $saveData[] = [
                    'task_id' =>intval($order['task_id']),
                    'select_id' =>intval($order['select_id']),
                    'user_id' =>Auth::id(),
                    'book_id' =>$bookId,
                    'quantity' =>intval($order['quantity']),
                    'created_at' =>date('Y-m-d H:i:s', time()),
                    'updated_at' =>date('Y-m-d H:i:s', time()),
                    'creator_id' =>Auth::id(),
                    'updater_id' =>Auth::id()
                ];
            }
        }
        if(count($saveData) <= 0) {
            $retData['error'] = true;
            $retData['msg'] = ['没有有效订单，请重试！'];
            return response() ->json($retData);
        }
        $order = new Order;
        if($order ->insert($saveData)) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }
    }
}
