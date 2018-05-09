@extends('layouts.basePage')

@section('css-text-part')
    <style>
        #left-navbar-main a {
            border-bottom: 1px solid #9fcdff;
        }
    </style>
@endsection

@section('content')
    <!-- 侧边导航栏 -->
    <div id="left-navbar" class="pull-left" style="width: 20%; min-height: 600px; border-right: 3px solid #ffffff;">
        <div id="left-navbar-main" class="sidebar-nav">
            <ul class="nav nav-list collpse">
                <li>
                    <a href="#select-books" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;&nbsp;选书模块</a>
                    <ul id="select-books" class="nav nav-list collapse" style="font-size: 1.1em;">
                        @can('tasksmanage@index')
                            <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('tasksmanage.index') }}')"><span style="margin-left: 23%;">任务管理</span></a></li>
                        @endcan
                        @can('tasksmanage@setselectorview')
                            <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('tasksmanage.setselectorview') }}')"><span style="margin-left: 23%;">选书分配</span></a></li>
                        @endcan
                        @can('tasksmanage@selectbooksview')
                            <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('tasksmanage.selectbooksview') }}')"><span style="margin-left: 23%;">书籍选择</span></a></li>
                        @endcan
                        @can('tasksmanage@verifyselectlistsview')
                            <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('tasksmanage.verifyselectlistsview') }}')"><span style="margin-left: 23%;">选书审核</span></a></li>
                        @endcan
                    </ul>
                </li>
                <li>
                    <a href="#order-books" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-fire"></span> &nbsp;&nbsp;&nbsp;购书模块</a>
                    <ul id="order-books" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('teacherorder.index') }}')"><span style="margin-left: 23%;">教师购书</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">学生购书</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">班级代购</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#deliver-books" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-book"></span> &nbsp;&nbsp;&nbsp;发书模块</a>
                    <ul id="deliver-books" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="/"><span style="margin-left: 23%;">试试</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">试试</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#query-block" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-search"></span> &nbsp;&nbsp;&nbsp;查询模块</a>
                    <ul id="query-block" class="nav nav-list collapse" style="font-size: 1.1em;">
                        @can('selectquery@index')
                            <li><a href="javascript:void(0);" onclick="changeMainPage('{{ route('selectquery.index') }}')"><span style="margin-left: 23%;">选书状态查询</span></a></li>
                        @endcan
                        <li><a href="/"><span style="margin-left: 23%;">订书状态查询</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">发书状态查询</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">书单数量查询</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#education-manage" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-briefcase"></span> &nbsp;&nbsp;&nbsp;教务管理</a>
                    <ul id="education-manage" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('classesmanage.index') }}')"><span style="margin-left: 23%;">班级管理</span></a></li>
                        <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('majorsmanage.index') }}')"><span style="margin-left: 23%;">专业管理</span></a></li>
                        <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('academiesmanage.index') }}')"><span style="margin-left: 23%;">学院管理</span></a></li>
                        <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('coursesmanage.index') }}')"><span style="margin-left: 23%;">课程管理</span></a></li>
                        <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('booksmanage.index') }}')"><span style="margin-left: 23%;">书籍管理</span></a></li>
                    </ul>
                </li>
                @if(Auth::user() ->can('rolesmanage@index') || Auth::user() ->can('permissionsmanage@index') || Auth::user() ->can('usersmanage@index'))
                    <li>
                        <a href="#system-manage" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-cog"></span> &nbsp;&nbsp;&nbsp;系统管理</a>
                        <ul id="system-manage" class="nav nav-list collapse" style="font-size: 1.1em;">
                            <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('register') }}')"><span style="margin-left: 23%;">流程管理</span></a></li>
                            @can('rolesmanage@index')
                                <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('rolesmanage.index') }}')"><span style="margin-left: 23%;">角色管理</span></a></li>
                            @endcan
                            @can('permissionsmanage@index')
                                <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('permissionsmanage.index') }}')"><span style="margin-left: 23%;">权限管理</span></a></li>
                            @endcan
                            @can('usersmanage@index')
                                <li><a href="javascript:void(0)" onclick="changeMainPage('{{ route('usersmanage.index') }}')"><span style="margin-left: 23%;">用户管理</span></a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <!-- 右边主页面 -->
    <div id="right-main" class="pull-left" style="width:80%;">
        <iframe id="main-page-iframe" scrolling="yes" width="100%" height="100%" frameborder="0" src="" style="min-height: 600px;"></iframe>
    </div>
</div>
@endsection

@section('js-text-part')
    <script type="text/javascript">
        function changeMainPage(pageUrl) {
            if(pageUrl == "" || pageUrl == undefined || pageUrl == null) {
                window.alert('pageUrl不能为空');
            }
            else {
                $("#main-page-iframe").attr("src", pageUrl);
            }
        }
    </script>
@endsection
