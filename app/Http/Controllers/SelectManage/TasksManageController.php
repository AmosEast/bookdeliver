<?php

namespace App\Http\Controllers\SelectManage;

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

class TasksManageController extends Controller
{
    //选书任务管理模块

    /**
     * 选书任务管理首页
     */
    public function index() {
        $pageData = [];
        //获取当前所有有效的并且未结束的任务
        $pageData['tasks'] = Task::where([
            ['is_valid', '=', 1],
            ['status', '<>', Task::$deliver_complete]
        ]) ->get();
        //获取状态定义
        $pageData['statusMeaning'] = Task::getTaskStatusMeanings();
        //获取任务状态切换时提示信息
        $pageData['statusTips'] = Task::getTipsForStatus();

        return view('selectManage.tasksManage.index') ->with($pageData);

    }

    /**
     * 添加选书任务
     */
    public function addTask(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $message = [
            'task_name.required' =>'任务名称不能为空',
            'task_name.max' =>'任务名称超过最大限度',
            'task_description.max' =>'任务描述超过最大限度'
        ];
        $validator = Validator::make($request ->all(), [
            'task_name' =>'bail|required|max:64',
            'task_description' =>'bail|nullable|max:256'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }
        $task = new Task;
        $task ->name = $request ->task_name;
        $task ->description = $request ->task_description;
        $task ->creator_id = Auth::id();
        $task ->updater_id = Auth::id();
        if($task ->save()) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }
    }

    /**
     * 修改任务状态
     */
    public function changeTaskStatus($taskId, $status) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $task = Task::find($taskId);
        if($task) {
            if(!in_array($status, array_keys(Task::getTaskStatusMeanings()))) {
                $retData['error'] = true;
                $retData['msg'] = ['该任务状态不存在！'];
                return response() ->json($retData);
            }
            //书籍状态相关检查，后面补上
            switch ($task ->status) {
                case Task::$select_process: {
                    foreach ($task ->selectLists as $selectList) {
                        if($selectList ->status < SelectList::$finalRefused) {
                            $retData['error'] = true;
                            $retData['msg'] = ['尚有任务未被审核或未提交审核，不允许进入下一阶段！'];
                            return response() ->json($retData);
                        }
                    }
                }
            }

            $task ->status = intval($status);
            if($task ->save()) {
                return response() ->json($retData);
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['数据库操作失败！'];
                return response() ->json($retData);
            }

        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['该任务不存在，请重试！'];
            return response() ->json($retData);
        }
    }

    /**
     * 指定选书人
     */
    public function setSelectorView() {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //获取用户的学院id
        $academyId = User::getAcademyId(Auth::id());
        if(!$academyId) {
            $pageData['error'] = true;
            $pageData['msg'] = '您没有学院信息，无法查看该页面！';
            return view('selectManage.tasksManage.selectLists') ->with($pageData);
        }
        //获取当前在教师选书过程中的任务并将task信息变成以id为下表的数组
        $arrTasks = [];
        $tasks = Task::select('id', 'name', 'status')
            ->where([
                ['status', '=', Task::$select_process],
                ['is_valid', '=', 1]
            ])
            ->get();
        //获取当前任务所有选书安排
        $selectLists = [];
        foreach ($tasks as $task) {
            $selectLists[$task ->id] = $task ->selectLists;
            $arrTasks[$task ->id] = $task;
        }
        //选取当前用户所在院系的所有专业
        $majors = Major::with('courses:courses.id,courses.name')
            ->where([
                ['majors.is_valid', '=', 1],
                ['majors.academy_id', '=', $academyId]
            ])
            ->get();
        //将专业转化为以id为下标的数组
        $arrMajors = [];
        //获取专业相关的课程并将课程信息转换成以id为下表的数组
        $arrMajorCourses = [];
        $arrCourses = [];
        foreach ($majors as $major) {
            $arrMajors[$major ->id] = $major;
            $arrMajorCourses[$major ->id] = $major ->courses;

            foreach ($major ->courses as $course) {
                $arrCourses[$course ->id] = $course;
            }
        }
        $jsonMajorCourses = json_encode($arrMajorCourses);
        //获取当前的年级
        $grades = self::getGrades();
        //获得当前拥有 教师 角色的所有用户并将用户信息变成以id为下标的数组
        $arrUsers = [];
        $users = Role::where([
            ['name', '=', '教师'],
            ['is_valid', '=', 1]
        ])
            ->first() ->users;
        foreach ($users as $user) {
            $arrUsers[$user ->id] = $user;
        }

        $pageData['tasks'] = $tasks;
        $pageData['arrTasks'] = $arrTasks;
        $pageData['selectLists'] = $selectLists;
        $pageData['academyId'] = $academyId;
        $pageData['majors'] = $majors;
        $pageData['arrMajors'] = $arrMajors;
        $pageData['jsonMajorCourses'] = $jsonMajorCourses;
        $pageData['arrCourses'] = $arrCourses;
        $pageData['grades'] = $grades;
        $pageData['users'] = $users;
        $pageData['arrUsers'] = $arrUsers;

        return view('selectManage.tasksManage.selectLists') ->with($pageData);
    }

    /**
     * 添加选书分配
     */
    public function setSelector(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $message = [
            'select_task.required' =>'任务选项不能为空',
            'select_task.exists' =>'所选任务不存在',
            'select_academy.required' =>'院系选项不能为空',
            'select_academy.exists' =>'所选院系不存在',
            'select_major.required' =>'专业选项不能为空',
            'select_major.exists' =>'所选专业不存在',
            'select_grade.required' =>'年级选项不能为空',
            'select_grade.integer' =>'所选年级格式不正确',
            'select_course.required' =>'课程选项不能为空',
            'select_course.exists' =>'所选课程不存在',
            'select_user.required' =>'选书人选项不能为空',
            'select_user.exists' =>'所指定选书人不存在'
        ];
        $validator = Validator::make($request ->all(), [
            'select_task' =>'bail|required|exists:tasks,id',
            'select_academy' =>'bail|required|exists:academies,id',
            'select_major' =>'bail|required|exists:majors,id',
            'select_grade' =>'bail|required|integer',
            'select_course' =>'bail|required|exists:courses,id',
            'select_user' =>'bail|required|exists:users,id'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }

        $selectList = new SelectList;
        //检测该任务的该课程是否已经指定选书人
        if($selectList ->where([
            ['task_id', '=', $request ->select_task],
            ['academy_id', '=', $request ->select_academy],
            ['major_id', '=', $request ->select_major],
            ['grade', '=', $request ->select_grade],
            ['course_id', '=', $request ->select_course]
        ]) ->exists()){
            $retData['error'] = true;
            $retData['msg'] = ['该选书人已经被指定！'];
            return response() ->json($retData);
        }

        //存储指定信息
        $selectList ->task_id = $request ->select_task;
        $selectList ->academy_id = $request ->select_academy;
        $selectList ->major_id = $request ->select_major;
        $selectList ->grade = $request ->select_grade;
        $selectList ->course_id = $request ->select_course;
        $selectList ->selector_id = $request ->select_user;
        $selectList ->creator_id = Auth::id();
        $selectList ->updater_id = Auth::id();

        if($selectList ->save()) {
            return response() ->json($retData);
        }
        else{
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);

        }

    }

    /**
     * 编辑选书分配任务视图
     */
    public function editSelectListView($selectId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        $selectList = SelectList::find($selectId);
        if(!$selectList) {
            $pageData['error'] = true;
            $pageData['msg'] = '未找到该选书安排！';
            return view('selectManage.tasksManage.editSelectList') ->with($pageData);
        }

        //检查该安排是否已经被执行或者已经提交审核
        if($selectList ->book_ids || $selectList ->status > SelectList::$noSubmit) {
            $pageData['error'] = true;
            $pageData['msg'] = '该选书任务已经被执行或者审批，不允许修改！';
            return view('selectManage.tasksManage.editSelectList') ->with($pageData);
        }

        $academyId = $selectList ->academy_id;
        //获取当前在教师选书过程中的任务
        $tasks = Task::select('id', 'name', 'status')
            ->where([
                ['status', '=', Task::$select_process],
                ['is_valid', '=', 1]
            ])
            ->get();
        //选取当前院系的所有专业
        $majors = Major::with('courses:courses.id,courses.name')
            ->where([
                ['majors.is_valid', '=', 1],
                ['majors.academy_id', '=', $academyId]
            ])
            ->get();
        //获取专业相关的课程
        $arrMajorCourses = [];
        foreach ($majors as $major) {
            $arrMajorCourses[$major ->id] = $major ->courses;
        }
        $jsonMajorCourses = json_encode($arrMajorCourses);
        //获取当前的年级
        $grades = self::getGrades();
        //获得当前拥有 教师 角色的所有用户
        $users = Role::where([
            ['name', '=', '教师'],
            ['is_valid', '=', 1]
        ])
            ->first() ->users;

        $pageData['tasks'] = $tasks;
        $pageData['academyId'] = $academyId;
        $pageData['majors'] = $majors;
        $pageData['arrMajorCourses'] = $arrMajorCourses;
        $pageData['jsonMajorCourses'] = $jsonMajorCourses;
        $pageData['grades'] = $grades;
        $pageData['users'] = $users;
        $pageData['selectList'] = $selectList;

        return view('selectManage.tasksManage.editSelectList') ->with($pageData);
    }

    /**
     * 编辑选书安排
     */
    public function editSelectList(Request $request, $selectId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $message = [
            'select_task.required' =>'任务选项不能为空',
            'select_task.exists' =>'所选任务不存在',
            'select_major.required' =>'专业选项不能为空',
            'select_major.exists' =>'所选专业不存在',
            'select_grade.required' =>'年级选项不能为空',
            'select_grade.integer' =>'所选年级格式不正确',
            'select_course.required' =>'课程选项不能为空',
            'select_course.exists' =>'所选课程不存在',
            'select_user.required' =>'选书人选项不能为空',
            'select_user.exists' =>'所指定选书人不存在',
            'select_valid.required' =>'状态选项不能为空',
            'select_valid.boolean' =>'所选状态不存在'
        ];
        $validator = Validator::make($request ->all(), [
            'select_task' =>'bail|required|exists:tasks,id',
            'select_major' =>'bail|required|exists:majors,id',
            'select_grade' =>'bail|required|integer',
            'select_course' =>'bail|required|exists:courses,id',
            'select_user' =>'bail|required|exists:users,id',
            'select_valid' =>'bail|required|boolean'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }

        $selectList = SelectList::find($selectId);

        //存储指定信息
        $selectList ->task_id = $request ->select_task;
        $selectList ->major_id = $request ->select_major;
        $selectList ->grade = $request ->select_grade;
        $selectList ->course_id = $request ->select_course;
        $selectList ->selector_id = $request ->select_user;
        $selectList ->is_valid = $request ->select_valid;
        $selectList ->updater_id = Auth::id();

        if($selectList ->save()) {
            return response() ->json($retData);
        }
        else{
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }

    }

    /**
     * 选择书籍列表视图
     */
    public function selectBooksView() {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //获取现在未结束的任务
        $tasks = Task::select('id', 'name')
            ->where([
                ['is_valid', '=', 1],
                ['status', '<>', Task::$deliver_complete]
            ])
            ->get();
        $arrTaskIds = $tasks ->pluck('id') ->toArray();
        //获取用户需要选书的课程安排信息
        $user = Auth::user();
        $selectLists = $user ->selectLists()
            ->whereIn('select_lists.task_id', $arrTaskIds)
            ->get();
        if(count($selectLists) <= 0) {
            $pageData['error'] = true;
            $pageData['msg'] = '当前您没有任务！';
            return view('selectManage.tasksManage.selectBooks') ->with($pageData);
        }
        //获取相关的任务、学院、专业、课程、书籍相关信息
        $arrAcademyIds = [];
        $arrMajorIds = [];
        $arrCourseIds = [];
        $arrBookIds = [];
        foreach ($selectLists as &$selectList) {
            $arrAcademyIds[$selectList ->academy_id] = $selectList ->academy_id;
            $arrMajorIds[$selectList ->major_id] = $selectList ->major_id;
            $arrCourseIds[$selectList ->course_id] = $selectList ->course_id;
            $selectList ->book_ids = json_decode($selectList ->book_ids);
            if(!empty($selectList ->book_ids)) {
                foreach ($selectList ->book_ids as $bookId) {
                    $arrBookIds[$bookId] = $bookId;
                }
            }
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
            ->whereIn('id', $arrBookIds)
            ->where('is_valid', '=', 1)
            ->get();

        $arrTasksInfo = [];
        $arrAcademiesInfo = [];
        $arrMajorsInfo = [];
        $arrCoursesInfo = [];
        $arrBooksInfo = [];

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
        $pageData['selectLists'] = $selectLists;
        $pageData['arrTasksInfo'] = $arrTasksInfo;
        $pageData['arrAcademiesInfo'] = $arrAcademiesInfo;
        $pageData['arrMajorsInfo'] = $arrMajorsInfo;
        $pageData['arrCoursesInfo'] = $arrCoursesInfo;
        $pageData['arrBooksInfo'] = $arrBooksInfo;
        $pageData['selectStatusMeaning'] = SelectList::getStatusMeaning();

        return view('selectManage.tasksManage.selectBooks') ->with($pageData);
    }

    /**
     * 编辑所选书籍视图
     */
    public function editSelectBooksView($selectId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //获取该选书安排
        $selectList = SelectList::find($selectId);
        if(!$selectList) {
            $pageData['error'] = true;
            $pageData['msg'] = '该选书安排不存在！';
            return view('selectManage.tasksManage.editSelectBooks') ->with($pageData);
        }
        //获取选书安排对应的课程
        $course = $selectList ->course;

        //获取该课程所有的教材类书籍
        $booksForStu = Book::select('id', 'name')
            ->where([
                ['type', '=', Book::$bookForStudent],
                ['course_id', '=', $course ->id]
            ])
            ->get();
        //获取该课程所有的教参类书籍
        $booksFroTea = Book::select('id', 'name')
            ->where([
                ['type', '=', Book::$bookForTeacher],
                ['course_id', '=', $course ->id]
            ])
            ->get();
        $pageData['selectList'] = $selectList;
        $pageData['booksForStu'] = $booksForStu;
        $pageData['booksForTea'] = $booksFroTea;

        return view('selectManage.tasksManage.editSelectBooks') ->with($pageData);

    }

    /**
     * 保存书籍选择信息
     */
    public function saveSelectBooks(Request $request, $selectId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $message = [
            'books_for_stu.required' =>'教材类书籍不能为空'
        ];
        $validator = Validator::make($request ->all(), [
            'books_for_stu' =>'bail|required',
            'books_for_tea' =>'bail|nullable'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }

        $arrBookIds = [];
        if(!empty($request ->books_for_stu)) {
            $arrBookIds = array_merge($arrBookIds, $request ->books_for_stu);
        }
        if(!empty($request ->books_for_tea)) {
            $arrBookIds = array_merge($arrBookIds, $request ->books_for_tea);
        }
        $arrBookIds = array_unique($arrBookIds);

        $selectList = SelectList::find($selectId);
        if(!$selectList) {
            $retData['error'] = true;
            $retData['msg'] = ['该选书安排不存在，请重试！'];
            return response() ->json($retData);
        }
        $selectList ->book_ids = json_encode($arrBookIds);
        $selectList ->updater_id = Auth::id();
        if($selectList ->save()) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }
    }

    /**
     * 提交选书安排
     */
    public function submitSelectList($selectId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $selectList = SelectList::find($selectId);
        if(!$selectList) {
            $retData['error'] = true;
            $retData['msg'] = ['该选书安排已经存在！'];
            return response() ->json($retData);
        }
        $selectList ->status = SelectList::$hasSubmit;
        $selectList ->updater_id = Auth::id();
        if($selectList ->save()) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }
    }

    /**
     * 审核选书视图
     */
    public function verifySelectListsView(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        $arrTasksWhere = [
            ['is_valid', '=', 1],
            ['status', '=', Task::$select_process]
        ];
        if($request ->isMethod('post')) {
            $request ->flash();
        }
        if($request ->isMethod('post') && $request ->select_task) {
            $arrTasksWhere[] = ['id', '=', $request ->select_task];
        }
        $arrSelectListsWhere = [
            ['is_valid', '=', 1],
        ];
        if($request ->isMethod('post') && $request ->select_academy) {
            $arrSelectListsWhere[] = ['academy_id', '=', intval($request ->select_academy)];
        }
        if($request ->isMethod('post') && $request ->select_book) {
            $arrSelectListsWhere[] = ['book_ids', 'like', '%'.intval($request ->select_book).'%'];
        }
        if($request ->isMethod('post') && $request ->select_status >= 0) {
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
            ->get();
        //获取相关的任务、学院、专业、课程、书籍相关信息
        $arrAcademyIds = [];
        $arrMajorIds = [];
        $arrCourseIds = [];
        $arrBookIds = [];
        $arrUserIds = [];
        foreach ($selectLists as &$selectList) {
            $arrAcademyIds[$selectList ->academy_id] = $selectList ->academy_id;
            $arrMajorIds[$selectList ->major_id] = $selectList ->major_id;
            $arrCourseIds[$selectList ->course_id] = $selectList ->course_id;
            $arrUserIds[$selectList ->selector_id] = $selectList ->selector_id;
            $selectList ->book_ids = json_decode($selectList ->book_ids);
            if(!empty($selectList ->book_ids)) {
                foreach ($selectList ->book_ids as $bookId) {
                    $arrBookIds[$bookId] = $bookId;
                }
            }
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
            ->whereIn('id', $arrBookIds)
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
            $arrBooksInfo[$user ->id] = $user;
        }
        $pageData['selectLists'] = $selectLists;
        $pageData['arrTasksInfo'] = $arrTasksInfo;
        $pageData['arrAcademiesInfo'] = $arrAcademiesInfo;
        $pageData['arrMajorsInfo'] = $arrMajorsInfo;
        $pageData['arrCoursesInfo'] = $arrCoursesInfo;
        $pageData['arrBooksInfo'] = $arrBooksInfo;
        $pageData['arrBooksInfo'] = $arrBooksInfo;
        $pageData['selectStatusMeaning'] = SelectList::getStatusMeaning();

        return view('selectManage.tasksManage.verifySelectLists') ->with($pageData);
    }

    /**
     * 批量更改选书安排状态
     */
    public function batchChangeSelectStatus(Request $request, $selectStatus) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $message = [
            'select_ids.required' =>'未选择进行操作的书籍',
            'select_ids.array' =>'所选数据格式错误'
        ];
        $validator = Validator::make($request ->all(), [
            'select_ids' =>'bail|required|array'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }
        $selectLists = new SelectList;
        $arrSelectStatus = $selectLists ->whereIn('id', $request ->select_ids) ->pluck('status') ->toArray();
        $arrSelectStatus = array_unique($arrSelectStatus);
        if(count($arrSelectStatus) > 1 || $arrSelectStatus[0] != SelectList::$hasSubmit) {
            $retData['error'] = true;
            $retData['msg'] = ['所选审核任务中存在不是 已提交审核 状态的任务！'];
            return response() ->json($retData);
        }
        $saveRet = $selectLists ->whereIn('id', $request ->select_ids)
            ->update([
                'status' =>intval($selectStatus),
                'updated_at' =>date('Y-m-d H:i:s', time()),
                'updater_id' =>Auth::id()
            ]);
        if($saveRet) {
            return response() ->json($retData);
        }
        else {
            $retData['error'] = ture;
            $retData['msg'] = '数据库操作失败！';
            return response() ->json($retData);
        }
    }
}
