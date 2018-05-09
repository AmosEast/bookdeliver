<?php

namespace App\Http\Controllers\OrderManage;

use App\Models\Academy;
use App\Models\Book;
use App\Models\Major;
use App\Models\Order;
use App\Models\SelectList;
use App\Models\Task;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeacherOrderController extends Controller
{
    //教师订书管理模块

    /**
     * 教师订书首页展示
     */
    public function index(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //每页显示条数
        $perPage = 1;
        //缓存请求数据
        $request ->flash();
        //获取用户学院信息
        $userAcademyId = User::getAcademyId(Auth::id());
        if($userAcademyId <= 0) {
            $pageData['error'] = true;
            $pageData['msg'] = '没有您可用的订书信息！';
            return view('orderManage.teacherOrder.index') ->with($pageData);

        }
        $academy = Academy::find($userAcademyId);
        //获取该学院所有的专业，同时预加载该学院所有的课程
        $majors = Major::with('courses:courses.id,courses.name')
            ->where([
                ['academy_id', '=', $academy ->id],
                ['is_valid', '=', 1]
            ])
            ->get();
        //获取相关任务
        $tasks = Task::select('id', 'name')
            ->where([
                ['is_valid', '=', 1],
                ['status', '=', Task::$order_process]
            ])
            ->get();
        //获取该学院所有审核通过的选书安排
        $selectListWhere = [
            ['is_valid', '=', 1],
            ['status', '=', SelectList::$finalAgreed],
        ];
        //前端查询字段
        $arrTaskIds = [];
        if($request ->has('select_task') && $request ->select_task) {
            $arrTaskIds[] = intval($request ->select_task);
        }
        else {
            foreach ($tasks as $task) {
                $arrTaskIds[$task ->id] = $task ->id;
            }
        }
        //获取当前任务下用户的所有订单
        $orders = Order::with('book:books.id,books.name')
            ->whereIn('task_id', $arrTaskIds)
            ->where([
                ['orders.user_id', '=', Auth::id()],
                ['orders.is_valid', '=', 1]
            ])
            ->get();
        $arrSelectIds = $orders ->pluck('select_id') ->toArray();

        if($request ->has('select_major') && $request ->select_major) {
            $selectListWhere[] = ['major_id', '=', intval($request ->select_major)];
        }
        if($request ->has('select_grade') && $request ->select_grade) {
            $selectListWhere[] = ['grade', '=', intval($request ->select_grade)];
        }
        if($request ->has('select_course') && $request ->select_course) {
            $selectListWhere[] = ['course_id', '=', intval($request ->select_course)];
        }
        if($request ->has('select_has_ordered') && $request ->select_has_ordered) {
            switch ($request ->select_has_ordered) {
                case -1: {
                    $selectLists = SelectList::whereIn('task_id', $arrTaskIds)
                        ->whereNotIn('id', $arrSelectIds)
                        ->where($selectListWhere)
                        ->paginate($perPage);
                    break;
                }
                case 1: {
                    $selectLists = SelectList::whereIn('task_id', $arrTaskIds)
                        ->whereIn('id', $arrSelectIds)
                        ->where($selectListWhere)
                        ->paginate($perPage);
                    break;
                }
                default: {
                    $selectLists = SelectList::whereIn('task_id', $arrTaskIds)
                        ->where($selectListWhere)
                        ->paginate($perPage);
                    break;
                }
            }
        }
        else {
            $selectLists = SelectList::whereIn('task_id', $arrTaskIds)
                ->where($selectListWhere)
                ->paginate($perPage);
        }

        //组织前端数据
        $arrTasksInfo = [];
        $arrMajorsInfo = [];
        $arrCoursesInfo = [];
        $arrOrdersInfo = [];

        foreach ($tasks as $task) {
            $arrTasksInfo[$task ->id] = $task;
        }
        foreach ($majors as $major) {
            $arrMajorsInfo[$major ->id] = $major;
            foreach ($major ->courses as $course) {
                $arrCoursesInfo[$course ->id] = $course;
            }
        }
        foreach ($orders as $order) {
            $arrOrdersInfo[$order ->select_id][] = $order;
        }

        $pageData['arrTasksInfo'] = $arrTasksInfo;
        $pageData['academy'] = $academy;
        $pageData['arrMajorsInfo'] = $arrMajorsInfo;
        $pageData['arrCoursesInfo'] = $arrCoursesInfo;
        $pageData['arrOrdersInfo'] = $arrOrdersInfo;
        $pageData['selectLists'] = $selectLists;
        $pageData['grades'] = $this ->getGrades();

        return view('orderManage.teacherOrder.index') ->with($pageData);
    }

    /**
     * 教师书籍订购视图
     */
    public function orderBooksView($selectId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        $selectList = SelectList::find($selectId);
        if(!$selectList) {
            $pageData['error'] = true;
            $pageData['msg'] = '该选书任务不存在，请重新刷新课程页面后重试';
            return view('orderManage.teacherOrder.orderBooks') ->with($pageData);
        }
        //获取书籍信息
        if($selectList ->book_ids) {
            $selectList ->book_ids = json_decode($selectList ->book_ids, true);
        }
        else {
            $selectList ->book_ids = [];
        }
        $books = Book::whereIn('id', $selectList ->book_ids)
            ->where('is_valid', '=', 1)
            ->get();
        $pageData['selectList'] = $selectList;
        $pageData['books'] = $books;

        return view('orderManage.teacherOrder.orderBooks') ->with($pageData);
    }

    /**
     * 提交教师订书信息
     */
    public function orderBooks(Request $request, $selectId, $taskId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $bookIds = [];
        if($request ->has('books_for_stu') && !empty($request ->books_for_stu) && is_array($request ->books_for_stu)) {
            $bookIds = array_merge($bookIds, $request ->books_for_stu);
        }
        if($request ->has('books_for_tea') && !empty($request ->books_for_tea) && is_array($request ->books_for_tea)) {
            $bookIds = array_merge($bookIds, $request ->books_for_tea);
        }

        if(empty($bookIds)) {
            return response() ->json($retData);
        }
        else{
            $arrBookQuantity = $request ->book_quantity;
            $saveData = [];
            foreach ($bookIds as $bookId) {
                if($arrBookQuantity[$bookId] <= 0) {
                    continue;
                }
                else {
                    $saveData[] = [
                        'select_id' =>intval($selectId),
                        'task_id' =>intval($taskId),
                        'user_id' => Auth::id(),
                        'book_id' =>intval($bookId),
                        'quantity' =>$arrBookQuantity[$bookId],
                        'created_at' =>date('Y-m-d H:i:s', time()),
                        'updated_at' =>date('Y-m-d H:i:s', time()),
                        'creator_id' =>Auth::id(),
                        'updater_id' =>Auth::id()
                    ];
                }
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
}
