@extends('layouts.basePage')

@section('content')
    <!-- 侧边导航栏 -->
    <div id="left-navbar" class="pull-left" style="width: 20%;">
        <div id="left-navbar-main" class="sidebar-nav">
            <ul class="nav nav-list collpse">
                <li>
                    <a href="#order-books" class="nav-header" data-toggle="collapse">订书模块</a>
                    <ul id="order-books" class="nav nav-list collapse" style="padding-left: 5%;">
                        <li><a href="/">试试</a></li>
                        <li><a href="/">试试</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#deliver-books" class="nav-header" data-toggle="collapse">发书模块</a>
                    <ul id="deliver-books" class="nav nav-list collapse" style="padding-left: 5%;">
                        <li><a href="/">试试</a></li>
                        <li><a href="/">试试</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#query-block" class="nav-header" data-toggle="collapse">查询模块</a>
                    <ul id="query-block" class="nav nav-list collapse" style="padding-left: 5%;">
                        <li><a href="/">订书状态查询</a></li>
                        <li><a href="/">发书状态查询</a></li>
                        <li><a href="/">书单数量查询</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#system-manage" class="nav-header" data-toggle="collapse">系统管理</a>
                    <ul id="system-manage" class="nav nav-list collapse" style="padding-left: 5%;">
                        <li><a href="/">流程管理</a></li>
                        <li><a href="/">角色管理</a></li>
                        <li><a href="/">权限管理</a></li>
                        <li><a href="/">用户管理</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <!-- 右边主页面 -->
    <div id="right-main" class="pull-left" style="width:80%;background-color: #1b6d85; ">
        <iframe id="main-page-iframe" scrolling="yes" width="100%" frameborder="0" src=""></iframe>
    </div>
</div>
@endsection
