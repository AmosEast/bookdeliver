<?php

namespace App\Http\Controllers\QueryManage;

use App\Models\Academy;
use App\Models\Book;
use App\Models\Course;
use App\Models\Major;
use App\Models\Role;
use App\Models\SelectList;
use App\Models\Task;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SelectQueryController extends Controller
{
    //选书查询模块

    /**
     * 选书结果查询首页
     */
    public function index(Request $request) {
        //分页时每页展示的数量
        $listNum = 1;
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        $arrTasksWhere = [
            ['is_valid', '=', 1],
            ['status', '<>', Task::$deliver_complete]
        ];
        $request ->flash();
        if($request ->has('select_task') && $request ->select_task) {
            $arrTasksWhere[] = ['id', '=', $request ->select_task];
        }
        $arrSelectListsWhere = [
            ['is_valid', '=', 1],
        ];
        if($request ->has('select_academy') && $request ->select_academy) {
            $arrSelectListsWhere[] = ['academy_id', '=', intval($request ->select_academy)];
        }
        if($request ->has('select_book') && $request ->select_book) {
            $arrSelectListsWhere[] = ['book_ids', 'like', '%'.intval($request ->select_book).'%'];
        }
        if($request ->has('select_status') && $request ->select_status >= 0) {
            $arrSelectListsWhere[] = ['status', '=', intval($request ->select_status)];
        }
        //获取现在未结束的任务
        $tasks = Task::select('id', 'name')
            ->where($arrTasksWhere)
            ->get();
        $arrTaskIds = $tasks ->pluck('id') ->toArray();
        //获取用户需要选书的课程安排信息
        $selectLists = SelectList::whereIn('task_id', $arrTaskIds)
            ->where($arrSelectListsWhere)
            ->paginate($listNum);
        //获取相关的任务、学院、专业、课程、选书人相关信息
        $arrAcademyIds = [];
        $arrMajorIds = [];
        $arrCourseIds = [];
        $arrUserIds = [];
        foreach ($selectLists as &$selectList) {
            $arrAcademyIds[$selectList ->academy_id] = $selectList ->academy_id;
            $arrMajorIds[$selectList ->major_id] = $selectList ->major_id;
            $arrCourseIds[$selectList ->course_id] = $selectList ->course_id;
            $arrUserIds[$selectList ->selector_id] = $selectList ->selector_id;
            $selectList ->book_ids = json_decode($selectList ->book_ids);

        }

        $academies = Academy::select('id', 'name')
            ->whereIn('id', $arrAcademyIds)
            ->where('is_valid', '=', 1)
            ->get();
        $majors = Major::select('id', 'name')
            ->whereIn('id', $arrMajorIds)
            ->where('is_valid', '=', 1)
            ->get();
        $courses = Course::select('courses.id', 'courses.name')
            ->whereIn('courses.id', $arrCourseIds)
            ->where('courses.is_valid', '=', 1)
            ->get();
        $books = Book::select('id', 'name', 'type')
            ->where('is_valid', '=', 1)
            ->get();
        $users = User::select('id', 'name')
            ->whereIn('id', $arrUserIds)
            ->get();

        $arrTasksInfo = [];
        $arrAcademiesInfo = [];
        $arrMajorsInfo = [];
        $arrCoursesInfo = [];
        $arrBooksInfo = [];
        $arrUsersInfo = [];

        foreach ($tasks as $task) {
            $arrTasksInfo[$task ->id] = $task;
        }
        foreach ($academies as $academy) {
            $arrAcademiesInfo[$academy ->id] = $academy;
        }
        foreach ($majors as $major) {
            $arrMajorsInfo[$major ->id] = $major;
        }
        foreach ($courses as $course) {
            $arrCoursesInfo[$course ->id] = $course;
        }
        foreach ($books as $book) {
            $arrBooksInfo[$book ->id] = $book;
        }
        foreach ($users as $user) {
            $arrUsersInfo[$user ->id] = $user;
        }
        $pageData['selectLists'] = $selectLists;
        $pageData['arrTasksInfo'] = $arrTasksInfo;
        $pageData['arrAcademiesInfo'] = $arrAcademiesInfo;
        $pageData['arrMajorsInfo'] = $arrMajorsInfo;
        $pageData['arrCoursesInfo'] = $arrCoursesInfo;
        $pageData['arrBooksInfo'] = $arrBooksInfo;
        $pageData['arrUsersInfo'] = $arrUsersInfo;
        $pageData['selectStatusMeaning'] = SelectList::getStatusMeaning();

        return view('queryManage.selectQuery.index') ->with($pageData);
    }

}
